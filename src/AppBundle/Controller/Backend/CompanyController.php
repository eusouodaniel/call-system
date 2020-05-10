<?php

namespace AppBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Company;
use AppBundle\Form\CompanyType;
use AppBundle\Controller\BaseController;

/**
 * Company controller.
 *
 * @Route("/backend/company")
 */
class CompanyController extends BaseController
{

    /**
     * Lists all Company entities.
     *
     * @Route("/", name="backend_company")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Company')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Company entity.
     *
     * @Route("/", name="backend_company_create")
     * @Method("POST")
     * @Template("AppBundle:Company:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Company();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success',"O cadastro foi realizado com sucesso!");

            return $this->redirect($this->generateUrl('backend_company', array('id' => $entity->getId())));
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
     * Creates a form to create a Company entity.
     *
     * @param Company $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Company $entity)
    {
        $form = $this->createForm(new CompanyType(), $entity, array(
            'action' => $this->generateUrl('backend_company_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Company entity.
     *
     * @Route("/new", name="backend_company_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Company();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Company entity.
     *
     * @Route("/{id}/edit", name="backend_company_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Company')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Company entity.
    *
    * @param Company $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Company $entity)
    {
        $form = $this->createForm(new CompanyType($entity), $entity, array(
            'action' => $this->generateUrl('backend_company_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Company entity.
     *
     * @Route("/{id}", name="backend_company_update")
     * @Method("PUT")
     * @Template("AppBundle:Company:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Company')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success',"O cadastro foi atualizado com sucesso!");

            return $this->redirect($this->generateUrl('backend_company', array('id' => $id)));
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
     * Deletes a Company entity.
     *
     * @Route("/{id}/delete", name="backend_company_delete")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Company')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }

        $em->remove($entity);
        $em->flush();

        $request->getSession()
        ->getFlashBag()
        ->add('success',"O registro foi excluído com sucesso!");

        return $this->redirect($this->generateUrl('backend_company'));
    }

    /**
     * Get a Company contract type
     *
     * @Route("/get_contract_type", name="backend_get_contract_type", options={"expose"=true})
     *
     */
    public function getContractTypeAjax() {

        $companyId = $this->getUser()->getCompany()->getId();

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('AppBundle:Company')->find($companyId);

        $contract = $company->getContract();

        return $this->returnJson(array('responseCode' => 200, 'contract' => $contract));
    }
}





