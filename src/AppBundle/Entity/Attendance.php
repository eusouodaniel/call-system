<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Attendance
 *
 * @ORM\Table(name="attendance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttendanceRepository")
 */
class Attendance
{
    const CLIENTE = 0;
    const COMERCIAL = 1;

    const ATTENDANCE_MANUTENCAO = 1;
    const ATTENDANCE_REPARO = 2;
    const ATTENDANCE_ORCAMENTO = 3;
    const ATTENDANCE_INSTALACAO = 4;
    const ATTENDANCE_OUTROS = 5;
    const ATTENDANCE_ATIVO = 6;
    const ATTENDANCE_RECEPTIVO = 7;

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
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="attendance_profile", type="text")
     */
    private $attendance_profile;

    /**
     * @var string
     *
     * @ORM\Column(name="responsible_email", type="string", length=255)
     */
    private $responsible_email;

    /**
     * @ORM\OneToMany(targetEntity="Chamado", mappedBy="attendance")
     */
    private $chamados;

    public function __construct() {
      $this->chamados = new ArrayCollection();
    }

    /**
    * Transient
    *
    */
    private $averagetime;
    private $percentAttended;
    private $statusPendent;
    private $statusOuttatime;
    private $statusConcluded;
    private $statusConcludedOnTime;
    private $statusConcludedOnTimePercent;

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
     * @return Attendance
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

    /**
     * Set description
     *
     * @param string $description
     * @return Attendance
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set attendance_profile
     *
     * @param string $attendance_profile
     * @return Attendance
     */
    public function setAttendanceProfile($attendance_profile)
    {
        $this->attendance_profile = $attendance_profile;

        return $this;
    }

    /**
     * Get attendance_profile
     *
     * @return string
     */
    public function getAttendanceProfile()
    {
        return $this->attendance_profile;
    }

    /**
     * Set responsible_email
     *
     * @param string $responsible_email
     * @return Attendance
     */
    public function setResponsibleEmail($responsible_email)
    {
        $this->responsible_email = $responsible_email;

        return $this;
    }

    /**
     * Get responsible_email
     *
     * @return string
     */
    public function getResponsibleEmail()
    {
        return $this->responsible_email;
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
     * Set $averagetime
     *
     * @param int $averagetime
     * @return chamado
     */
    public function setAveragetime($averagetime)
    {
        $this->averagetime = $averagetime;

        return $this;
    }

    /**
     * Get averagetime
     *
     * @return chamado
     */
    public function getAveragetime()
    {
        return $this->averagetime;
    }

    /**
     * Set $percentAttended
     *
     * @param int $percentAttended
     * @return chamado
     */
    public function setPercentAttended($percentAttended)
    {
        $this->percentAttended = $percentAttended;

        return $this;
    }

    /**
     * Get percentAttended
     *
     * @return chamado
     */
    public function getPercentAttended()
    {
        return $this->percentAttended;
    }

    /**
     * Set $statusPendent
     *
     * @param int $statusPendent
     * @return chamado
     */
    public function setStatusPendent($statusPendent)
    {
        $this->statusPendent = $statusPendent;

        return $this;
    }

    /**
     * Get statusPendent
     *
     * @return chamado
     */
    public function getStatusPendent()
    {
        return $this->statusPendent;
    }

    /**
     * Set $statusOuttatime
     *
     * @param int $statusOuttatime
     * @return chamado
     */
    public function setStatusOuttatime($statusOuttatime)
    {
        $this->statusOuttatime = $statusOuttatime;

        return $this;
    }

    /**
     * Get statusOuttatime
     *
     * @return chamado
     */
    public function getStatusOuttatime()
    {
        return $this->statusOuttatime;
    }

    /**
     * Set $statusConcluded
     *
     * @param int $statusConcluded
     * @return chamado
     */
    public function setStatusConcluded($statusConcluded)
    {
        $this->statusConcluded = $statusConcluded;

        return $this;
    }

    /**
     * Get statusConcluded
     *
     * @return chamado
     */
    public function getStatusConcluded()
    {
        return $this->statusConcluded;
    }

    /**
     * Set $statusConcludedOnTime
     *
     * @param int $statusConcludedOnTime
     * @return chamado
     */
    public function setStatusConcludedOnTime($statusConcludedOnTime)
    {
        $this->statusConcludedOnTime = $statusConcludedOnTime;

        return $this;
    }

    /**
     * Get statusConcludedOnTime
     *
     * @return chamado
     */
    public function getStatusConcludedOnTime()
    {
        return $this->statusConcludedOnTime;
    }

    /**
     * Set $statusConcludedOnTimePercent
     *
     * @param int $statusConcludedOnTimePercent
     * @return chamado
     */
    public function setStatusConcludedOnTimePercent($statusConcludedOnTimePercent)
    {
        $this->statusConcludedOnTimePercent = $statusConcludedOnTimePercent;

        return $this;
    }

    /**
     * Get statusConcludedOnTimePercent
     *
     * @return chamado
     */
    public function getStatusConcludedOnTimePercent()
    {
        return $this->statusConcludedOnTimePercent;
    }
}
