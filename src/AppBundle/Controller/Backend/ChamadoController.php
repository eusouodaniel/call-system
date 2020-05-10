<?php

namespace AppBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Chamado;
use AppBundle\Entity\ChamadoMessage;
use AppBundle\Entity\ChamadoSearch;
use AppBundle\Entity\StatusChamado;
use AppBundle\Form\ChamadoType;
use AppBundle\Form\ChamadoFinishType;
use AppBundle\Form\ChamadoCancelType;
use AppBundle\Form\ChamadoSearchType;
use AppBundle\Controller\BaseController;
use AppBundle\Controller\Backend\SLAController;

/**
 * Chamado controller.
 *
 * @Route("/backend/chamado")
 */
class ChamadoController extends BaseController
{

    /**
     * Lists all Chamado entities.
     *
     * @Route("/", name="backend_chamado")
     * @Template()
     */
    public function indexAction(Request $request)
    {
      $type = $this->getUserType();
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType($type), $chamadoSearch);
      $form->handleRequest($request);
      $em = $this->getDoctrine()->getManager();
      if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {

        if($this->getUser()->getCompany()!=null){
          $company = $this->getUser()->getCompany();
          //Linha comentada: permite que o usuário responsável da empresa veja todos os chamados da sua empresa
          if($company->getUser() != null && ($company->getUser()->getId() == $this->getUser()->getId())){
            $chamadoSearch->setCompany($this->getUser()->getCompany());
          }else{
            $chamadoSearch->setClient($this->getUser());
          }
        }else{
          $chamadoSearch->setClient($this->getUser());
        }
        //Linha comentada: permite que o usuário verifica todos os chamados da sua empresa
        /*if($this->getUser()->getCompany()!=null){
          $chamadoSearch->setCompany($this->getUser()->getCompany());
        }*/
      }
      $firstAccessStatus = null;
      if($request->getMethod() == 'GET' || ($chamadoSearch->getStatus()!= null && $chamadoSearch->getStatus()->getId() == 99)){
        $firstAccessStatus = array();
        $firstAccessStatus[] = StatusChamado::EM_ANDAMENTO;
        $firstAccessStatus[] = StatusChamado::AGUARDANDO;
        $firstAccessStatus[] = StatusChamado::AGUARDANDO_CLIENTE;
      }
      $entities = $em->getRepository('AppBundle:Chamado')->findByChamadoSearch($chamadoSearch, false, $firstAccessStatus);

      //var_dump($this->getUser()->getId());
      

      return array(
        'entities' => $entities,
        'form'=> $form->createView(),
        'firstAccessStatus' => $firstAccessStatus
      );
    }
    /**
     * Creates a new Chamado entity.
     *
     * @Route("/create", name="backend_chamado_create")
     * @Method("POST")
     * @Template("AppBundle:Backend\Chamado:new.html.twig")
     */
    public function createAction(Request $request)
    {
      $entity = new Chamado();
      $form = $this->createCreateForm($entity);
      $form->handleRequest($request);

      if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $statusChamado = $em->getRepository('AppBundle:StatusChamado')->find(StatusChamado::AGUARDANDO);
        $entity->setStatus($statusChamado);

        // captura todos os dados da requisição
        $array = $request->request->all();

        // captura o item e o atendimento
        $itemId = $array['appbundle_chamado']['item'];
        $attendanceId = $array['appbundle_chamado']['attendance'];

        // busca um SLA com o item e o atendimento capturados
        $sla = $em->getRepository("AppBundle:SLA")->findOneByItemAttendance($itemId, $attendanceId);

        // converte as horas do SLA para dias
        $additionalDays = $sla->getHour() / 24;

        // adiciona os dias do SLA ao prazo de atendimento considerando os finais de semana
        $dtLimit = new \DateTime();
        date_add($dtLimit, date_interval_create_from_date_string(''.$additionalDays.' weekdays'));
        $entity->setDtLimit($dtLimit);

        $discrUser = $this->getUserType();
            //Se o criador do chamado for client ou comercial
        if($discrUser != "SUPER_ADMIN"){
          if($entity->getEnterprise() != null){
            $company = $entity->getEnterprise();
            $entity->setClient($company->getUser());
          }else{
            $entity->setEnterprise($this->getUser()->getCompany());
            $entity->setClient($this->getUser());
          }
        }
            //Se o criador do chamado for admin ou user 
        else{
          $entity->setClient($this->getUser());
        }

        $entity->uploadFile();
        $em->persist($entity);

        $chamadoMessage = new ChamadoMessage();
        $chamadoMessage->setDescription($entity->getDescription());
        $chamadoMessage->setUser($this->getUser());
        $chamadoMessage->setChamado($entity);
        $em->persist($chamadoMessage);
        $em->flush();

        $request->getSession()
        ->getFlashBag()
        ->add('success',"O cadastro foi realizado com sucesso!");

        $message = $this->createMessage($entity);
        $email = "";
        if($entity->getEnterprise()!=null){
          $email = $entity->getEnterprise()->getEmail();
          if($email != null){
            $this->log("[Abertura de Chamado] Empresa ".$email);
            $this->sendEmail($email, "Chamado criado", $message);
          }
        }
        $this->log("[Abertura de Chamado] Responsável ".$entity->getAttendance()->getResponsibleEmail());
        $this->sendEmail($entity->getAttendance()->getResponsibleEmail(), "Chamado criado", $message);
        $this->log("[Abertura de Chamado] Usuário atual ".$this->getUser()->getEmail());
        $this->sendEmail($this->getUser()->getEmail(), "Chamado criado", $message);
        if($this->getUser()->getCompany()!=null){
          $company = $this->getUser()->getCompany();
          if($company->getUser() != null && ($this->getUser()->getId() != $company->getUser()->getId())){
            if($email != $company->getUser()->getEmail()){
              $this->log("[Abertura de Chamado] Responsável da empresa ".$company->getUser()->getEmail());
              $this->sendEmail($company->getUser()->getEmail(), "Chamado criado", $message);
            }
          }
        }

        return $this->redirect($this->generateUrl('backend_chamado', array('id' => $entity->getId())));
      }else{
        $request->getSession()
        ->getFlashBag()
        ->add('error',"Verifique os erros e tente novamente");
      }

      return array(
        'entity' => $entity,
        'form'   => $form->createView(),
      );
    }

    private function sendEmail($email, $subject, $message){
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mailMessage = \Swift_Message::newInstance()
        ->setSubject("[Sistema de chamados] - ".$subject)
        ->setFrom("contato@eusouodaniel.com")
        ->setTo($email)
        ->setContentType("text/html")
        ->setBody($message);

        $this->get('mailer')->send($mailMessage);
      }else{
        $this->log("[Email inválido] ".$email);
      }
    }

    private function createMessage($entity, $historico = false){
      if($historico){
        $em = $this->getDoctrine()->getManager();
        $chamadoMessages = $em->getRepository('AppBundle:ChamadoMessage')->findByChamado($entity);
      }
      $message = "<html><body>";
      $message = '<table rules="all" style="border-color: #ddd; margin-bottom: 20px; width: 100%;" cellpadding="10">';
      $message .= "<tr style='background: #F7941D; font-size: 18px; color: #fff;'><td colspan='4'><strong>Dados do chamado</strong></td></tr>";
      $message .= "<tr><td width='25%' style='background: #F3F3F3;'><strong>Número de chamado:</strong> </td><td>" . $entity->getId() . "</td></tr>";
      $message .= "<tr><td width='25%' style='background: #F3F3F3;'><strong>Tipo de atendimento:</strong> </td><td>" . $entity->getAttendance() . "</td></tr>";
      $message .= "<tr><td style='background: #F3F3F3;'><strong>Item:</strong> </td><td>" . $entity->getItem() . "</td></tr>";
      if(!$historico){
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Descrição:</strong> </td><td>" . $entity->getDescription() . "</td></tr>";
      }
      if($entity->getEnterprise()!=null){
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Empresa:</strong> </td><td>" . $entity->getEnterprise() . "</td></tr>";
      }else if($entity->getClient()!=null){
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Empresa:</strong> </td><td>" . $entity->getClient()->getCompany() . "</td></tr>";
      }
      if($entity->getClient()!=null){
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Registrado por:</strong> </td><td>" . $entity->getClient()->getFullName() . "</td></tr>";
      }
      $message .= "<tr><td style='background: #F3F3F3;'><strong>Status:</strong> </td><td>" . $entity->getStatus() . "</td></tr>";
      // if ($entity->getDtLimit()!=null){
      //   $message .= "<tr><td style='background: #F3F3F3;'><strong>Prazo de Atendimento:</strong> </td><td>" . $entity->getDtLimit()->format("d/m/Y") . "</td></tr>";
      // }
      if($entity->getUser()!=null){
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Responsável:</strong> </td><td>" . $entity->getUser()->getFullName() . "</td></tr>";
      }
      if($entity->getDtEnd()!=null){
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Data de Encerramento:</strong> </td><td>" . $entity->getDtEnd()->format("d/m/Y") . "</td></tr>";
      }
      if($entity->getConclusionEnd()!=null){
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Descrição de Fechamento:</strong> </td><td>" . $entity->getConclusionEnd() . "</td></tr>";
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Pesquisa de satisfação:</strong> </td><td><a href='https://docs.google.com/forms/d/e/1FAIpQLSe0Kim0tA0rlLFZnwxEFeX-T8Mf32hiKsrKcKefiYxhFMj6UQ/viewform?c=0&w=1' target='_blank'>Clique aqui</a></td></tr>";
      }
      if($entity->getDtCancel()!=null){
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Data de Cancelamento:</strong> </td><td>" . $entity->getDtCancel()->format("d/m/Y") . "</td></tr>";
      }
      if($entity->getConclusionCancel()!=null){
        $message .= "<tr><td style='background: #F3F3F3;'><strong>Descrição de Cancelamento:</strong> </td><td>" . $entity->getConclusionCancel() . "</td></tr>";
      }
      if($historico){
        $message .= "<tr><td style='background: #F3F3F3;' colspan='2'><strong>Historico do chamado</strong> </td></tr>";
        foreach ($chamadoMessages as $chamadoMessage) {
          $message .= "<tr><td><strong>".$chamadoMessage->getUser()."</strong> - ".$chamadoMessage->getDtCreation()->format("d/m/Y H:i")."</td><td>".$chamadoMessage->getDescription()."</td></tr>";
        }
      }
      $message .= "</table>";
      $message .= "</body></html>";
      return $message;
    }

    /**
     * Creates a form to create a Chamado entity.
     *
     * @param Chamado $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Chamado $entity)
    {
      $type = $this->getUserType();

      $form = $this->createForm(new ChamadoType($type), $entity, array(
        'action' => $this->generateUrl('backend_chamado_create'),
        'method' => 'POST',
      ));

      $form->add('submit', 'submit', array('label' => 'Create'));

      return $form;
    }

    /**
     * Displays a form to create a new Chamado entity.
     *
     * @Route("/new", name="backend_chamado_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
      $entity = new Chamado();
      $form   = $this->createCreateForm($entity);

      return array(
        'entity' => $entity,
        'form'   => $form->createView(),
      );
    }

    /**
     * Displays a form to edit an existing Chamado entity.
     *
     * @Route("/{id}/edit", name="backend_chamado_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('AppBundle:Chamado')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Chamado entity.');
      }

      $entity->setDescription("");

      $editForm = $this->createEditForm($entity);

      $chamadoMessages = $em->getRepository('AppBundle:ChamadoMessage')->findByChamado($entity);

      return array(
        'entity'           => $entity,
        'chamadoMessages'  => $chamadoMessages,
        'edit_form'        => $editForm->createView(),
      );
    }

    /**
     * Displays a form to finish an existing Chamado entity.
     *
     * @Route("/{id}/finish", name="backend_chamado_finish")
     * @Template()
     */
    public function finishAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('AppBundle:Chamado')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Chamado entity.');
      }
      $editForm = $this->createForm(new ChamadoFinishType(), $entity);
      $editForm->handleRequest($request);
      if ($request->getMethod() == 'POST') {
        $statusChamado = $em->getRepository('AppBundle:StatusChamado')->find(StatusChamado::CONCLUIDO);
        $entity->setUser($this->getUser());
        $entity->setStatus($statusChamado);
        $entity->setDtEnd(new \DateTime());
        $em->persist($entity);

        $chamadoMessage = new ChamadoMessage();
        $chamadoMessage->setDescription($entity->getConclusionEnd());
        $chamadoMessage->setUser($this->getUser());
        $chamadoMessage->setChamado($entity);
        $em->persist($chamadoMessage);
        $em->flush();

        $message = $this->createMessage($entity, true);
        $this->log("[Finalização de Chamado] Responsável ".$entity->getAttendance()->getResponsibleEmail());
        $this->sendEmail($entity->getAttendance()->getResponsibleEmail(), "Chamado finalizado", $message);
        $email = "";
        if($entity->getEnterprise()!=null){
          $email = $entity->getEnterprise()->getEmail();
          if($email != null){
            $this->log("[Finalização de Chamado] Empresa ".$email);
            $this->sendEmail($email, "Chamado finalizado", $message);
          }
        }

        if($entity->getClient()!=null){
          $this->log("[Finalização de Chamado] Cliente ".$entity->getClient()->getEmail());
          $this->sendEmail($entity->getClient()->getEmail(), "Chamado finalizado", $message);
          if($entity->getClient()->getCompany()!=null){
            $company = $entity->getClient()->getCompany();
            if($company->getUser() != null && ($entity->getClient()->getId() != $company->getUser()->getId()) ){
              if($email != $company->getUser()->getEmail()){
                $this->log("[Finalização de Chamado] Responsável da empresa ".$company->getUser()->getEmail());
                $this->sendEmail($company->getUser()->getEmail(), "Chamado finalizado", $message);
              }
            }
          }
        }

        $request->getSession()
        ->getFlashBag()
        ->add('success',"O chamado foi finalizado com sucesso!");

        return $this->redirect($this->generateUrl('backend_chamado', array('id' => $entity->getId())));
      }

      return array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
      );
    }

    /**
     * Displays a form to cancel an existing Chamado entity.
     *
     * @Route("/{id}/cancel", name="backend_chamado_cancel")
     * @Template()
     */
    public function cancelAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('AppBundle:Chamado')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Chamado entity.');
      }
      $editForm = $this->createForm(new ChamadoCancelType(), $entity);
      $editForm->handleRequest($request);
      if ($request->getMethod() == 'POST') {
        $statusChamado = $em->getRepository('AppBundle:StatusChamado')->find(StatusChamado::CANCELADO);
        $entity->setUser($this->getUser());
        $entity->setStatus($statusChamado);
        $entity->setDtCancel(new \DateTime());
        $em->persist($entity);

        $chamadoMessage = new ChamadoMessage();
        $chamadoMessage->setDescription($entity->getConclusionCancel());
        $chamadoMessage->setUser($this->getUser());
        $chamadoMessage->setChamado($entity);
        $em->persist($chamadoMessage);
        $em->flush();

        $message = $this->createMessage($entity, true);

        $this->sendEmail($entity->getAttendance()->getResponsibleEmail(), "Chamado cancelado", $message);

        $email = "";
        if($entity->getEnterprise() != null){
          $email = $entity->getEnterprise()->getEmail();
          if($email != null){
            $this->log("[Cancelamento de Chamado] Empresa ".$email);
            $this->sendEmail($email, "Chamado cancelado", $message);
          }
        }

        if($entity->getClient()!=null){
          $this->log("[Cancelamento de Chamado] Cliente ".$entity->getClient()->getEmail());
          $this->sendEmail($entity->getClient()->getEmail(), "Chamado cancelado", $message);
          if($entity->getClient()->getCompany()!=null){
            $company = $entity->getClient()->getCompany();
            if($company->getUser() != null && ($entity->getClient()->getId() != $company->getUser()->getId())){
              if($company->getUser()->getEmail() != $email){
                $this->log("[Cancelamento de Chamado] Responsável da empresa ".$company->getUser()->getEmail());
                $this->sendEmail($company->getUser()->getEmail(), "Chamado cancelado", $message);
              }
            }
          }
        }

        $request->getSession()
        ->getFlashBag()
        ->add('success',"O chamado foi cancelado com sucesso!");

        return $this->redirect($this->generateUrl('backend_chamado', array('id' => $entity->getId())));
      }

      return array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
      );
    }

    /**
     * Reopen an finished Chamado entity.
     *
     * @Route("/{id}/reopen", name="backend_chamado_reopen")
     * @Template()
     */
    public function reopenAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('AppBundle:Chamado')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Chamado entity.');
      }
      $statusChamado = $em->getRepository('AppBundle:StatusChamado')->find(StatusChamado::EM_ANDAMENTO);
      $entity->setStatus($statusChamado);
      $em->persist($entity);
      $em->flush();

      $request->getSession()
      ->getFlashBag()
      ->add('success',"O chamado foi reaberto com sucesso!");

      return $this->redirect($this->generateUrl('backend_chamado', array('id' => $entity->getId())));

      return array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
      );
    }

    /**
    * Creates a form to edit a Chamado entity.
    *
    * @param Chamado $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Chamado $entity)
    {
      $type = $this->getUserType();

      $form = $this->createForm(new ChamadoType($type), $entity, array(
        'action' => $this->generateUrl('backend_chamado_update', array('id' => $entity->getId())),
        'method' => 'PUT',
      ));

      $form->add('submit', 'submit', array('label' => 'Update'));

      return $form;
    }
    /**
     * Edits an existing Chamado entity.
     *
     * @Route("/{id}", name="backend_chamado_update")
     * @Method("PUT")
     * @Template("AppBundle:Backend\Chamado:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('AppBundle:Chamado')->find($id);

      $originaStatus = $entity->getStatus();

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Chamado entity.');
      }

      $editForm = $this->createEditForm($entity);
      $editForm->handleRequest($request);

      if ($editForm->isValid()) {
        $entity->uploadFile();

        $chamadoMessage = new ChamadoMessage();
        $chamadoMessage->setDescription($entity->getDescription());
        $chamadoMessage->setUser($this->getUser());
        $chamadoMessage->setChamado($entity);
        $em->persist($chamadoMessage);

        $em->flush();

        $request->getSession()
        ->getFlashBag()
        ->add('success',"O chamado foi atualizado com sucesso!");

        if($originaStatus != null && $originaStatus->getId() != $entity->getStatus()->getId()){
          $message = $this->createMessage($entity, true);
          $email = "";
          if($entity->getEnterprise() != null){
            $email = $entity->getEnterprise()->getEmail();
            if($email != null){
              $this->log("[Alteração de status de Chamado] Empresa ".$email);
              $this->sendEmail($email, "Status de chamado alterado", $message);
            }
          }
          if($entity->getClient()!=null){
            $this->log("[Alteração de status de Chamado] Cliente ".$entity->getClient()->getEmail());
            $this->sendEmail($entity->getClient()->getEmail(), "Status de chamado alterado", $message);
            if($entity->getClient()->getCompany()!=null){
              $company = $entity->getClient()->getCompany();
              if($company->getUser() != null && ($entity->getClient()->getId() != $company->getUser()->getId())){
                if($company->getUser()->getEmail() != $email){
                  $this->log("[Alteração de status de Chamado] Responsável da empresa ".$company->getUser()->getEmail());
                  $this->sendEmail($company->getUser()->getEmail(), "Status de chamado alterado", $message);
                }
              }
            }
          }
          $this->sendEmail($entity->getAttendance()->getResponsibleEmail(), "Status de chamado alterado", $message);
        }else{
          $message = $this->createMessage($entity, true);
          $email = "";
          if($entity->getEnterprise()!=null){
            $email = $entity->getEnterprise()->getEmail();
            if($email != null){
              $this->log("[Atualização de Chamado] Cliente ".$email);
              $this->sendEmail($email, "Atualização de chamado", $message);
            }
          }

          if($entity->getClient()!=null){
            $this->log("[Atualização de Chamado] Cliente ".$entity->getClient()->getEmail());
            $this->sendEmail($entity->getClient()->getEmail(), "Atualização de chamado", $message);
            if($entity->getClient()->getCompany()!=null){
              $company = $entity->getClient()->getCompany();
              if($company->getUser() != null && ($entity->getClient()->getId() != $company->getUser()->getId())){
                if($company->getUser()->getEmail() != $email){
                  $this->log("[Atualização de Chamado] Responsável da empresa ".$company->getUser()->getEmail());
                  $this->sendEmail($company->getUser()->getEmail(), "Atualização de chamado", $message);
                }
              }
            }
          }
          $this->sendEmail($entity->getAttendance()->getResponsibleEmail(), "Atualização de chamado", $message);
        }

        return $this->redirect($this->generateUrl('backend_chamado', array('id' => $id)));
      }else{
        $request->getSession()
        ->getFlashBag()
        ->add('error',"Verifique os erros e tente novamente");
      }

      return array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
      );
    }
  }
