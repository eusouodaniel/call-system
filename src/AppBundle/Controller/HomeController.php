<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends BaseController
{

    /**
     * Página principal.
     * @Route("/", name="homepage")
     * @Template(engine="twig")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('backend_home'));
    }
}
