<?php

namespace FMT\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * CampaignContact
 *
 * @ORM\Table(
 *      name="campaign_contact",
 *      indexes={
 *          @ORM\Index(name="FK_campaign", columns={"campaign_id"}),
 *          @ORM\Index(name="FK_campaign_contact", columns={"contact_id"})
 *      }
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class CampaignContact implements EntityInterface
{
    use TimestampableEntity;

    const STATUS_UNCONFIRMED = 1;
    const STATUS_CONFIRMED = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FMT\DataBundle\Entity\Campaign
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\Campaign")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     * })
     */
    private $campaign;

    /**
     * @var \FMT\DataBundle\Entity\UserContact
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\UserContact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $contact;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     * @return $this
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
        return $this;
    }

    /**
     * @return UserContact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param UserContact $contact
     * @return $this
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
        $contact->addCampaignContact($this);
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUnconfirmedContact()
    {
        return $this->status === self::STATUS_UNCONFIRMED;
    }

    /**
     * @return bool
     */
    public function isConfirmedContact()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }
}
