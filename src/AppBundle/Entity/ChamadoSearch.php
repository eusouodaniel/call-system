<?php

namespace AppBundle\Entity;

class ChamadoSearch
{

    private $id;
    private $item;
    private $attendance;
    private $user;
    private $client;
    private $company;
    private $status;
    private $begin_date;
    private $end_date;
    private $type;

    /**
     * Set $id
     *
     * @param Integer $id
     * @return Chamado
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return Integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set $status
     *
     * @param Status $status
     * @return Chamado
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set $user
     *
     * @param User $user
     * @return Chamado
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set $client
     *
     * @param Client $client
     * @return Chamado
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set $company
     *
     * @param Company $company
     * @return Chamado
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
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
     * Set begin_date
     *
     * @param \DateTime $begin_date
     *
     * @return Payment
     */
    public function setBeginDate($begin_date)
    {
        $this->begin_date = $begin_date;

        return $this;
    }

    /**
     * Get begin_date
     *
     * @return \DateTime
     */
    public function getBeginDate()
    {
        return $this->begin_date;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $end_date
     *
     * @return Payment
     */
    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set $type
     *
     * @param String $type
     * @return Chamado
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

}
