<?php

namespace AppBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Comercial;
use AppBundle\Form\ComercialType;
use AppBundle\Controller\BaseController;

/**
 * Comercial controller.
 *
 * @Route("/backend/comercial")
 */
class ComercialController extends BaseController
{

    /**
     * Lists all Comercial entities.
     *
     * @Route("/", name="backend_comercial")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
          $entities = $em->getRepository('AppBundle:Comercial')->findAll();
        }else{
          $entities[] = $this->getUser();
        }

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Comercial entity.
     *
     * @Route("/", name="backend_comercial_create")
     * @Method("POST")
     * @Template("AppBundle:Comercial:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Comercial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
          if ($this->validUsernameEmail($entity)) {
            $em = $this->getDoctrine()->getManager();

            $entity->setUsername($entity->getEmail());
            $entity->setEnabled(true);
            $entity->addRole('ROLE_COMERCIAL');

            // Set entity fields custom
            $this->requestCustomForm($request, $entity);
            $em->persist($entity);

            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success',"O cadastro foi realizado com sucesso!");

            return $this->redirect($this->generateUrl('backend_comercial', array('id' => $entity->getId())));
          } else {
              $this->get('session')->getFlashBag()->add('error', 'Email já cadastrado, tente novamente!');
          }
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
     * Creates a form to create a Comercial entity.
     *
     * @param Comercial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Comercial $entity)
    {
        $form = $this->createForm(new ComercialType(), $entity, array(
            'action' => $this->generateUrl('backend_comercial_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Comercial entity.
     *
     * @Route("/new", name="backend_comercial_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Comercial();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Comercial entity.
     *
     * @Route("/{id}/edit", name="backend_comercial_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Comercial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comercial entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Comercial entity.
    *
    * @param Comercial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Comercial $entity)
    {
        $form = $this->createForm(new ComercialType(), $entity, array(
            'action' => $this->generateUrl('backend_comercial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Comercial entity.
     *
     * @Route("/{id}", name="backend_comercial_update")
     * @Method("PUT")
     * @Template("AppBundle:Comercial:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Comercial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comercial entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
          if ($this->validUsernameEmail($entity)) {
            $this->requestCustomForm($request, $entity);
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success',"O cadastro foi atualizado com sucesso!");

            return $this->redirect($this->generateUrl('backend_comercial', array('id' => $id)));
          } else {
              $this->get('session')->getFlashBag()->add('error', 'Email já cadastrado, tente novamente!');
          }
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
     * Deletes a Comercial entity.
     *
     * @Route("/{id}/delete", name="backend_comercial_delete")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Comercial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comercial entity.');
        }

        $em->remove($entity);
        $em->flush();

        $request->getSession()
                ->getFlashBag()
                ->add('success',"O registro foi excluído com sucesso!");

        return $this->redirect($this->generateUrl('backend_comercial'));
    }

    /**
     * Atualiza o Status do Usuários para ATIVO
     *
     * @Route("/?id={id}", name="backend_user_status")
     */
    public function activateStatusAction($id)
    {
      if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $condicao = $entity->getStatus();
        if($condicao == "Habilitado")
           $entity->setEnabled(false);
        else
            $entity->setEnabled(true);

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_comercial', array('id' => $id)));
      }else{
        throw $this->createNotFoundException('Você não tem permissão para acessar esta tela.');
      }
    }

    /**
     * Set entity fields custom
     * @param Request $request
     * @param Entity $entity
     */
    public function requestCustomForm($request, $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $comercialCustomArray = $request->request->get('comercial_custom');

        if (!empty($comercialCustomArray['plainPassword'])) {
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($entity);
            $new_pwd_encoded = $encoder->encodePassword($comercialCustomArray['plainPassword'], $entity->getSalt());
            $entity->setPassword($new_pwd_encoded);
        }
    }

    /**
     * Valida se o username e email são válidos
     * @param Object $entity
     * @return Query Result
     */
    function validUsernameEmail($entity) {
        $repository = $this->getUserRepository();
        $entities = $repository->getUserByNameAndUsername($entity);

        return $entities;
    }
}
