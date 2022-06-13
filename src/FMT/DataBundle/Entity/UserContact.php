<?php

namespace FMT\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * UserContacts
 *
 * @ORM\Table(
 *     name="user_contacts",
 *     indexes={
 *          @ORM\Index(name="FK_contact_user", columns={"student_id"}),
 *          @ORM\Index(name="FK_contact_donor", columns={"donor_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="FMT\DataBundle\Repository\UserContactRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserContact implements EntityInterface
{
    use TimestampableEntity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FMT\DataBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     * })
     */
    private $student;

    /**
     * @var \FMT\DataBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="donor_id", referencedColumnName="id")
     * })
     */
    private $donor;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var CampaignContact[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="CampaignContact", mappedBy="contact")
     */
    private $campaignContacts;

    public function __construct()
    {
        $this->campaignContacts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @param User $student
     * @return $this
     */
    public function setStudent($student)
    {
        $this->student = $student;
        return $this;
    }

    /**
     * @return User
     */
    public function getDonor()
    {
        return $this->donor;
    }

    /**
     * @param User $donor
     * @return $this
     */
    public function setDonor($donor)
    {
        $this->donor = $donor;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    /**
     * @return CampaignContact|null
     */
    public function getCampaignContact()
    {
        $result = $this->campaignContacts->filter(function (CampaignContact $contact) {
            return $contact->getCampaign()->isActive();
        });

        return $result->isEmpty() ? null : $result->first();
    }

    /**
     * @param CampaignContact $contact
     * @return CampaignContact
     */
    public function addCampaignContact(CampaignContact $contact)
    {
        if ($contact->getCampaign() && !$contact->getCampaign()->isActive() && $contact->getCampaign()->isFinished()) {
            throw new \RuntimeException("Unable to add contact into inactive campaign");
        }

        if (!$this->hasCampaignContact($contact)) {
            if (!$contact->getContact()) {
                $contact->setContact($this);
            }
            $this->campaignContacts->add($contact);
        }

        return $contact;
    }

    /**
     * @param CampaignContact $contact
     * @return bool
     */
    public function hasCampaignContact(CampaignContact $contact)
    {
        if (!$contact->getCampaign()) {
            return false;
        }

        $result = $this->campaignContacts->filter(function (CampaignContact $item) use ($contact) {
            return $item->getCampaign() && $item->getCampaign()->getId() == $contact->getCampaign()->getId();
        });

        return !$result->isEmpty();
    }
}
