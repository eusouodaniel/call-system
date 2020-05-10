<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Chamado
 *
 * @ORM\Table(name="chamado")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChamadoRepository")
 */
class Chamado
{
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
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="telphone", type="string", length=255)
     */
    private $telphone;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

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
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $client;

    /**
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="responsible_id", referencedColumnName="id", nullable=true)
     */
    private $responsible;

    /**
     *
     * @ORM\ManyToOne(targetEntity="StatusChamado")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_limit", type="datetime", nullable=true)
     */
    private $dtLimit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_end", type="datetime", nullable=true)
     */
    private $dtEnd;


    /**
     * @var string
     *
     * @ORM\Column(name="conclusion_end", type="text", nullable=true)
     */
    private $conclusionEnd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cancel", type="datetime", nullable=true)
     */
    private $dtCancel;


    /**
     * @var string
     *
     * @ORM\Column(name="conclusion_cancel", type="text", nullable=true)
     */
    private $conclusionCancel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_creation", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $dtCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_update", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $dtUpdate;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $enterprise;

    /**
     * @ORM\OneToMany(targetEntity="ChamadoMessage", mappedBy="chamado", cascade={"all"})
     */
    protected $chamadoMessages;

    public function __construct() {
        $this->chamadoMessages = new ArrayCollection();

    }

    /**
     * Set telphone
     *
     * @param string $telphone
     * @return Chamado
     */
    public function setTelphone($telphone)
    {
        $this->telphone = $telphone;

        return $this;
    }

    /**
     * Get telphone
     *
     * @return string
     */
    public function getTelphone()
    {
        return $this->telphone;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
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
     * Set $responsible
     *
     * @param User $responsible
     * @return Chamado
     */
    public function setResponsible($responsible)
    {
        $this->responsible = $responsible;

        return $this;
    }

    /**
     * Get responsible
     *
     * @return User
     */
    public function getResponsible()
    {
        return $this->responsible;
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Chamado
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
     * Set file
     *
     * @param string $file
     * @return Chamado
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set dtEnd
     *
     * @param \DateTime $dtEnd
     *
     * @return Payment
     */
    public function setDtEnd($dtEnd)
    {
        $this->dtEnd = $dtEnd;

        return $this;
    }

    /**
     * Get dtEnd
     *
     * @return \DateTime
     */
    public function getDtEnd()
    {
        return $this->dtEnd;
    }

    /**
     * Set dtLimit
     *
     * @param \DateTime $dtLimit
     *
     * @return Payment
     */
    public function setDtLimit($dtLimit)
    {
        $this->dtLimit = $dtLimit;

        return $this;
    }

    /**
     * Get dtLimit
     *
     * @return \DateTime
     */
    public function getDtLimit()
    {
        return $this->dtLimit;
    }

    /**
     * Set conclusionEnd
     *
     * @param \DateTime $conclusionEnd
     *
     * @return Payment
     */
    public function setConclusionEnd($conclusionEnd)
    {
        $this->conclusionEnd = $conclusionEnd;

        return $this;
    }

    /**
     * Get conclusionEnd
     *
     * @return \DateTime
     */
    public function getConclusionEnd()
    {
        return $this->conclusionEnd;
    }

    /**
     * Set dtCancel
     *
     * @param \DateTime $dtCancel
     *
     * @return Payment
     */
    public function setDtCancel($dtCancel)
    {
        $this->dtCancel = $dtCancel;

        return $this;
    }

    /**
     * Get dtCancel
     *
     * @return \DateTime
     */
    public function getDtCancel()
    {
        return $this->dtCancel;
    }

    /**
     * Set conclusionCancel
     *
     * @param \DateTime $conclusionCancel
     *
     * @return Payment
     */
    public function setConclusionCancel($conclusionCancel)
    {
        $this->conclusionCancel = $conclusionCancel;

        return $this;
    }

    /**
     * Get conclusionCancel
     *
     * @return \DateTime
     */
    public function getConclusionCancel()
    {
        return $this->conclusionCancel;
    }

    /**
     * Set dtCreation
     *
     * @param \DateTime $dtCreation
     *
     * @return Payment
     */
    public function setDtCreation($dtCreation)
    {
        $this->dtCreation = $dtCreation;

        return $this;
    }

    /**
     * Get dtCreation
     *
     * @return \DateTime
     */
    public function getDtCreation()
    {
        return $this->dtCreation;
    }

    /**
     * Set dtUpdate
     *
     * @param \DateTime $dtUpdate
     *
     * @return Payment
     */
    public function setDtUpdate($dtUpdate)
    {
        $this->dtUpdate = $dtUpdate;

        return $this;
    }

    /**
     * Get dtUpdate
     *
     * @return \DateTime
     */
    public function getDtUpdate()
    {
        return $this->dtUpdate;
    }

    /**
     * Upload file
     */

    // Constante com o caminho para salvar a imagem screenshot
    const UPLOAD_PATH_FILE = 'uploads/chamado';

    private $fileTemp;

    /**
     * Sets fileTemp
     *
     * @param UploadedFile $fileTemp
     */
    public function setFileTemp(UploadedFile $fileTemp = null)
    {
        $this->fileTemp = $fileTemp;
    }

    /**
     * Get fileTemp
     *
     * @return UploadedFile
     */
    public function getFileTemp()
    {
        return $this->fileTemp;
    }

    /**
     * Sets enterprise
     *
     * @param $enterprise
     */
    public function setEnterprise($enterprise)
    {
        $this->enterprise = $enterprise;
    }

    /**
     * Get enterprise
     *
     * @return this
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * Sets chamadoMessages
     *
     * @param $chamadoMessages
     */
    public function setChamadoMessages($chamadoMessages)
    {
        $this->chamadoMessages = $chamadoMessages;
    }

    /**
     * Get chamadoMessages
     *
     * @return this
     */
    public function getChamadoMessages()
    {
        return $this->chamadoMessages;
    }

    /**
     * Unlink File (Apagar arquivo quando excluir objeto)
     */
    public function unlinkFile()
    {
        if ($this->getFile() != null) {
            unlink(Chamado::UPLOAD_PATH_FILE  ."/". $this->getFile());
        }
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function uploadFile()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFileTemp()) {
            return;
        }

        if(
            ($this->getFileTemp() != $this->getFile())
            && (null !== $this->getFile())
        ){
            unlink(Chamado::UPLOAD_PATH_FILE ."/". $this->getFile());
        }

        // Generate a unique name for the file before saving it
        $fileName = md5(uniqid()).'.'.$this->getFileTemp()->guessExtension();

        // move takes the target directory and target filename as params
        $this->getFileTemp()->move(
            Chamado::UPLOAD_PATH_FILE,
            $fileName
        );

        // set the path property to the filename where you've saved the file
        $this->file = $fileName;

        // clean up the file property as you won't need it anymore
        $this->setFileTemp(null);

    }

    //Transient
    private $tempoAtendimento;
    private $sla;
    private $slaAtingido;

    /**
     * Set tempoAtendimento
     *
     * @param string $tempoAtendimento
     * @return Chamado
     */
    public function setTempoAtendimento($tempoAtendimento)
    {
        $this->tempoAtendimento = $tempoAtendimento;

        return $this;
    }

    /**
     * Get tempoAtendimento
     *
     * @return string
     */
    public function getTempoAtendimento()
    {
        return $this->tempoAtendimento;
    }

    /**
     * Set sla
     *
     * @param string $sla
     * @return Chamado
     */
    public function setSla($sla)
    {
        $this->sla = $sla;

        return $this;
    }

    /**
     * Get sla
     *
     * @return string
     */
    public function getSla()
    {
        return $this->sla;
    }

    /**
     * Set slaAtingido
     *
     * @param string $slaAtingido
     * @return Chamado
     */
    public function setSlaAtingido($slaAtingido)
    {
        $this->slaAtingido = $slaAtingido;

        return $this;
    }

    /**
     * Get slaAtingido
     *
     * @return string
     */
    public function getSlaAtingido()
    {
        return $this->slaAtingido;
    }

}
