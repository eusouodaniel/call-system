<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatusChamado
 *
 * @ORM\Table(name="status_chamado")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatusChamadoRepository")
 */
class StatusChamado
{

  const AGUARDANDO = 1;
  const EM_ANDAMENTO = 2;
  const CONCLUIDO = 3;
  const CANCELADO = 4;
  const AGUARDANDO_CLIENTE = 5;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return StatusChamado
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
