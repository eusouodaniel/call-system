<?php

namespace AppBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\SLA;
use AppBundle\Form\SLAType;
use AppBundle\Controller\BaseController;

/**
 * SLA controller.
 *
 * @Route("/backend/sla")
 */
class SLAController extends BaseController
{

    /**
     * Lists all SLA entities.
     *
     * @Route("/", name="backend_sla")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:SLA')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new SLA entity.
     *
     * @Route("/", name="backend_sla_create")
     * @Method("POST")
     * @Template("AppBundle:SLA:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new SLA();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success',"O cadastro foi realizado com sucesso!");

            return $this->redirect($this->generateUrl('backend_sla', array('id' => $entity->getId())));
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

    /**
     * Creates a form to create a SLA entity.
     *
     * @param SLA $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SLA $entity)
    {
        $form = $this->createForm(new SLAType(), $entity, array(
            'action' => $this->generateUrl('backend_sla_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new SLA entity.
     *
     * @Route("/new", name="backend_sla_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new SLA();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing SLA entity.
     *
     * @Route("/{id}/edit", name="backend_sla_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:SLA')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SLA entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a SLA entity.
    *
    * @param SLA $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SLA $entity)
    {
        $form = $this->createForm(new SLAType(), $entity, array(
            'action' => $this->generateUrl('backend_sla_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing SLA entity.
     *
     * @Route("/{id}", name="backend_sla_update")
     * @Method("PUT")
     * @Template("AppBundle:SLA:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:SLA')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SLA entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success',"O cadastro foi atualizado com sucesso!");

            return $this->redirect($this->generateUrl('backend_sla', array('id' => $id)));
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
    /**
     * Deletes a SLA entity.
     *
     * @Route("/{id}/delete", name="backend_sla_delete")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:SLA')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SLA entity.');
        }

        $em->remove($entity);
        $em->flush();

        $request->getSession()
        ->getFlashBag()
        ->add('success',"O registro foi excluído com sucesso!");

        return $this->redirect($this->generateUrl('backend_sla'));
    }

     /**
     * Find a SLA entity.
     *
     * @Route("/find_sla_ajax", name="backend_sla_find", options={"expose"=true})
     *
     */
     public function findAction(Request $request)
     {

        $item = $request->query->get('itemId');
        $attendance = $request->query->get('attendanceId');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:SLA')->findOneBy(
         array('item' => $item, 'attendance' => $attendance)
        );

        // converte horas do SLA para dias
        $additionalDays = $entity->getHour() / 24;

        // adiciona os dias do SLA ao prazo de atendimento considerando os finais de semana
        $dtLimit = new \DateTime();
        date_add($dtLimit, date_interval_create_from_date_string(''.$additionalDays.' weekdays'));

        $resultSearch = $this->returnJson(array('responseCode' => 200,'dados' => $dtLimit->format('d/m/Y')));

        return $resultSearch;
    }
}
