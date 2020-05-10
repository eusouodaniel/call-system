<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 */
class Client extends User
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Chamado", mappedBy="client")
     */
    private $chamados;

    /**
    * Transient
    *
    */
    private $totalchamados;

    public function __construct() {
      parent::__construct();
      $this->chamados = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set $chamados
     *
     * @param Chamados $chamados
     * @return client
     */
    public function setChamados($chamados)
    {
        $this->chamados = $chamados;

        return $this;
    }

    /**
     * Get chamados
     *
     * @return Chamados
     */
    public function getChamados()
    {
        return $this->chamados;
    }

    /**
     * Set $totalchamados
     *
     * @param int $totalchamados
     * @return client
     */
    public function setTotalchamados($totalchamados)
    {
        $this->totalchamados = $totalchamados;

        return $this;
    }

    /**
     * Get totalchamados
     *
     * @return Client
     */
    public function getTotalchamados()
    {
        return $this->totalchamados;
    }
}
