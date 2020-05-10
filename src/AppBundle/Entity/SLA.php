<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SLA
 *
 * @ORM\Table(name="sla")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SLARepository")
 */
class SLA
{
    public function __toString()
    {
        return $this->attendance." - ".$this->item;
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
     *
     * @ORM\ManyToOne(targetEntity="Item")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Attendance")
     * @ORM\JoinColumn(name="attendance_id", referencedColumnName="id")
     */
    private $attendance;

    /**
     * @var string
     *
     * @ORM\Column(name="hour", type="integer")
     */
    private $hour;


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
     * Set $item
     *
     * @param Item $item
     * @return Chamado
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set $attendance
     *
     * @param Attendance $attendance
     * @return Chamado
     */
    public function setAttendance($attendance)
    {
        $this->attendance = $attendance;

        return $this;
    }

    /**
     * Get attendance
     *
     * @return Attendance
     */
    public function getAttendance()
    {
        return $this->attendance;
    }

    /**
     * Set hour
     *
     * @param string $hour
     *
     * @return Times
     */
    public function setHour($hour)
    {
        $this->hour = $hour;

        return $this;
    }

    /**
     * Get hour
     *
     * @return string
     */
    public function getHour()
    {
        return $this->hour;
    }

}
