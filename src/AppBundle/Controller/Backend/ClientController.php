<?php

namespace AppBundle\Controller\Backend;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Client;
use AppBundle\Form\ClientType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Client controller.
 *
 * @Route("/backend/client")
 */
class ClientController extends BaseController
{

    /**
     * Lists all Client entities.
     *
     * @Route("/", name="backend_client")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $entities = $em->getRepository('AppBundle:Client')->findAll();
        } else {
            $entities[] = $this->getUser();
        }

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Client entity.
     *
     * @Route("/", name="backend_client_create")
     * @Method("POST")
     * @Template("AppBundle:Backend\Client:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Client();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($this->validUsernameEmail($entity)) {
                $em = $this->getDoctrine()->getManager();

                $entity->setUsername($entity->getEmail());
                $entity->setEnabled(true);
                $entity->addRole('ROLE_CLIENT');

                // Set entity fields custom
                $this->requestCustomForm($request, $entity);
                $em->persist($entity);

                $em->flush();

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', "O cadastro foi realizado com sucesso!");

                return $this->redirect($this->generateUrl('backend_client', array('id' => $entity->getId())));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Email já cadastrado, tente novamente!');
            }
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('error', "Verifique os erros e tente novamente");
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Client entity.
     *
     * @param Client $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Client $entity)
    {
        $form = $this->createForm(new ClientType(), $entity, array(
            'action' => $this->generateUrl('backend_client_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Client entity.
     *
     * @Route("/new", name="backend_client_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Client();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Client entity.
     *
     * @Route("/{id}/edit", name="backend_client_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Client entity.
     *
     * @param Client $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Client $entity)
    {
        $form = $this->createForm(new ClientType(), $entity, array(
            'action' => $this->generateUrl('backend_client_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Client entity.
     *
     * @Route("/{id}", name="backend_client_update")
     * @Method("PUT")
     * @Template("AppBundle:Backend\Client:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if ($this->validUsernameEmail($entity)) {
                $this->requestCustomForm($request, $entity);
                $em->flush();

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', "O cadastro foi atualizado com sucesso!");

                return $this->redirect($this->generateUrl('backend_client', array('id' => $id)));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Email já cadastrado, tente novamente!');
            }
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('error', "Verifique os erros e tente novamente");
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }
    /**
     * Deletes a Client entity.
     *
     * @Route("/{id}/delete", name="backend_client_delete")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $em->remove($entity);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', "O registro foi excluído com sucesso!");

        return $this->redirect($this->generateUrl('backend_client'));
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
            if ($condicao == "Habilitado") {
                $entity->setEnabled(false);
            } else {
                $entity->setEnabled(true);
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_client', array('id' => $id)));
        } else {
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
        $clientCustomArray = $request->request->get('client_custom');

        if (!empty($clientCustomArray['plainPassword'])) {
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($entity);
            $new_pwd_encoded = $encoder->encodePassword($clientCustomArray['plainPassword'], $entity->getSalt());
            $entity->setPassword($new_pwd_encoded);
        }
    }

    /**
     * Valida se o username e email são válidos
     * @param Object $entity
     * @return Query Result
     */
    public function validUsernameEmail($entity)
    {
        $repository = $this->getUserRepository();
        $entities = $repository->getUserByNameAndUsername($entity);

        return $entities;
    }
}
