<?php

namespace AppBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Attendance;
use AppBundle\Form\AttendanceType;
use AppBundle\Controller\BaseController;

/**
 * Attendance controller.
 *
 * @Route("/backend/attendance")
 */
class AttendanceController extends BaseController
{

    /**
     * Lists all Attendance entities.
     *
     * @Route("/", name="backend_attendance")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Attendance')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Attendance entity.
     *
     * @Route("/", name="backend_attendance_create")
     * @Method("POST")
     * @Template("AppBundle:Attendance:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Attendance();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success',"O cadastro foi realizado com sucesso!");

            return $this->redirect($this->generateUrl('backend_attendance', array('id' => $entity->getId())));
        }else{
          $request->getSession()
                  ->getFlashBag()
                  ->add('error',"Verifique os erros e tente novamente.");
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Attendance entity.
     *
     * @param Attendance $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Attendance $entity)
    {
        $form = $this->createForm(new AttendanceType(), $entity, array(
            'action' => $this->generateUrl('backend_attendance_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Attendance entity.
     *
     * @Route("/new", name="backend_attendance_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Attendance();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Attendance entity.
     *
     * @Route("/{id}/edit", name="backend_attendance_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Attendance')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Attendance entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Attendance entity.
    *
    * @param Attendance $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Attendance $entity)
    {
        $form = $this->createForm(new AttendanceType(), $entity, array(
            'action' => $this->generateUrl('backend_attendance_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Attendance entity.
     *
     * @Route("/{id}", name="backend_attendance_update")
     * @Method("PUT")
     * @Template("AppBundle:Attendance:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Attendance')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Attendance entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $request->getSession()
                    ->getFlashBag()
                    ->add('success',"O cadastro foi atualizado com sucesso!");
            return $this->redirect($this->generateUrl('backend_attendance', array('id' => $id)));
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
     * Deletes a Attendance entity.
     *
     * @Route("/{id}/delete", name="backend_attendance_delete")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Attendance')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Attendance entity.');
        }

        $em->remove($entity);
        $em->flush();

        $request->getSession()
                ->getFlashBag()
                ->add('success',"O registro foi excluído com sucesso!");

        return $this->redirect($this->generateUrl('backend_attendance'));
    }
}
