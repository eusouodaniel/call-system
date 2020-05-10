<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\ChamadoMessage;

class UpdateChamadoMessageCommand extends ContainerAwareCommand {

    public $output;

    protected function configure() {
        $this
            ->setName('tst:update:chamado-message')
            ->setDescription('Atualiza os chamados do sistema')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->output = $output;

        //$output->writeln();
        $em = $this->getContainer()->get('doctrine')->getManager();

        # Atualiza a tabela Chamado message
        $this->updateChamadoMessage();
    }

    public function updateChamadoMessage()
    {
        $repositoryChamado = $this->getChamadoRepository();
        $repositoryUser = $this->getUserRepository();

        $entities = $repositoryChamado->findAll();

        $user = $repositoryUser->find(1);

        foreach ($entities as $entity) {
          $em = $this->getContainer()->get('doctrine')->getManager();

          # Entiy Notification
          $chamadoMessage = new ChamadoMessage();
          $chamadoMessage->setDescription($entity->getDescription());
          $chamadoMessage->setUser($user);
          $chamadoMessage->setChamado($entity);
          $em->persist($chamadoMessage);
          $em->flush();
        }
    }

    protected function getChamadoRepository() {
        return $this->getContainer()->get('doctrine')->getRepository('AppBundle:Chamado');
    }

    protected function getUserRepository() {
        return $this->getContainer()->get('doctrine')->getRepository('UserBundle:User');
    }


}

?>
