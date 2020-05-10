<?php

namespace AppBundle\Controller\Backend;

use Lunetics\LocaleBundle\Event\FilterLocaleSwitchEvent;
use Lunetics\LocaleBundle\LocaleBundleEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;

class HomeController extends BaseController {

    /**
     * Página principal.
     * @Route("/backend", name="backend_home")
     * @Template(engine="twig")
     */
    public function indexAction() {
        return array(
               //...
            );
    }
}
