<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Controller\BaseController;
use UserBundle\Entity\User;
use UserBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/backend/user")
 */
class UserController extends BaseController
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="backend_user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('UserBundle:User')->findOnlyUser('user');

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new User entity.
     *
     * @Route("/", name="backend_user_create")
     * @Method("POST")
     * @Template("UserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
          if ($this->validUsernameEmail($entity)) {
            $em = $this->getDoctrine()->getManager();

            $entity->setUsername($entity->getEmail());
            $entity->setEnabled(true);
            $entity->setRoles(array("ROLE_SUPER_ADMIN"));

            // Set entity fields custom
            $this->requestCustomForm($request, $entity);
            $em->persist($entity);

            $em->persist($entity);
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success',"O cadastro foi realizado com sucesso!");

            return $this->redirect($this->generateUrl('backend_user', array('id' => $entity->getId())));
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
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('backend_user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="backend_user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="backend_user_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('backend_user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="backend_user_update")
     * @Method("PUT")
     * @Template("UserBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
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

            return $this->redirect($this->generateUrl('backend_user', array('id' => $id)));
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
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="backend_user_delete")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $em->remove($entity);
        $em->flush();

        $request->getSession()
                ->getFlashBag()
                ->add('success',"O registro foi excluído com sucesso!");

        return $this->redirect($this->generateUrl('backend_user'));
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

        return $this->redirect($this->generateUrl('backend_user', array('id' => $id)));
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
        $userCustomArray = $request->request->get('user_custom');

        if (!empty($userCustomArray['plainPassword'])) {
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($entity);
            $new_pwd_encoded = $encoder->encodePassword($userCustomArray['plainPassword'], $entity->getSalt());
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
