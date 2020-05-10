<?php

namespace AppBundle\Controller;

use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller base das actions do Projeto SGI.
 */
class BaseController extends Controller {

    public function getUserType(){
      if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
        $type = "SUPER_ADMIN";
    }else if($this->get('security.authorization_checker')->isGranted('ROLE_CLIENT')){
        $type = "CLIENTE";
    }else if($this->get('security.authorization_checker')->isGranted('ROLE_COMERCIAL')){
        $type = "COMERCIAL";
    }
    return $type;
}

    /**
     * Adds support for magic finders for repositories.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return object The found repository.
     * @throws \BadMethodCallException If the method called is an invalid find* method
     *                                 or no find* method at all and therefore an invalid
     *                                 method call.
     */
    public function __call($method, $arguments) {
      if (preg_match('/^get(\w+)Repository$/', $method, $matches)) {
        return $this->getDoctrine()->getRepository('AppBundle:' . $matches[1]);
    } else {
        throw new \BadMethodCallException(
            "Undefined method '$method'. Provide a valid repository name!");
    }
}

    /**
     * Retorna o service "logger".
     * @return \Monolog\Logger
     */
    protected function getLogger() {
        return $this->get("logger");
    }

    /**
     * Método para retornar o objeto User.
     * @return CoreBundle\Entity\User
     */
    public function getUser() {
        return parent::getUser();
    }

    /**
     * Retorna o repositório de user.
     * @return AppBundle\Repository\UserRepository
     */
    protected function getUserRepository(){
        return $this->getDoctrine()->getRepository('UserBundle:User');
    }

    /**
     * Atalho para geração de logs no sistema.
     * @param string $message Mensagem a ser incluida no log.
     * @param string $level Level do log. Default: error.
     */
    protected function log($message, $level = "error") {
        $this->getLogger()->log($level, $message);
    }

    /**
    * Função que retorna o json para a requisição
    * @param array $return Array de retorno
    * @return Response
    */
    public function returnJson($return) {
        $serializer = SerializerBuilder::create()->build();
        $return = $serializer->serialize($return, 'json');
        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }
}
