<?php

namespace FMT\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * UserProfile
 *
 * @ORM\Table(
 *     name="user_profile",
 *     indexes={
 *          @ORM\Index(name="FK_profile_school", columns={"school_id"}),
 *          @ORM\Index(name="FK_profile_major", columns={"major_id"}),
 *          @ORM\Index(name="FK_profile_address", columns={"address_id"}),
 *          @ORM\Index(name="FK_profile_avatar", columns={"avatar_id"})
 *      }
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class UserProfile implements EntityInterface
{
    use TimestampableEntity;

    const VISIBILITY_ALL = 0;
    const VISIBILITY_REGISTRED = 1;
    const VISIBILITY_NON = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FMT\DataBundle\Entity\UserSchool
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\UserSchool", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="id")
     * })
     */
    private $school;

    /**
     * @var \FMT\DataBundle\Entity\UserMajor
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\UserMajor", inversedBy="profile", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="major_id", referencedColumnName="id")
     * })
     */
    private $major;

    /**
     * @var \FMT\DataBundle\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\Address", inversedBy="profile", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * })
     */
    private $address;

    /**
     * @var \FMT\DataBundle\Entity\UserAvatar
     *
     * @ORM\ManyToOne(targetEntity="FMT\DataBundle\Entity\UserAvatar", inversedBy="profile", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     * })
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="grad_year", type="integer", nullable=true)
     */
    private $gradYear;

    /**
     * @var string
     *
     * @ORM\Column(name="student_id", type="string", length=255, nullable=true)
     */
    private $studentId;

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="text", length=65535, nullable=true)
     */
    private $aboutText;

    /**
     * @var integer
     *
     * @ORM\Column(name="visible", type="smallint", nullable=false)
     */
    private $visible = self::VISIBILITY_ALL;

    /**
     * @var integer
     *
     * @ORM\Column(name="facebook", type="smallint", nullable=false)
     */
    private $facebook = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="twitter", type="smallint", nullable=false)
     */
    private $twitter = 0;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", mappedBy="profile")
     */
    protected $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserSchool
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param UserSchool $school
     * @return $this
     */
    public function setSchool($school)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * @return UserMajor
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @param UserMajor $major
     * @return $this
     */
    public function setMajor($major)
    {
        $this->major = $major;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return UserAvatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param UserAvatar $avatar
     * @return $this
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {

        $this->email = $email;

        if ($this->user) {
            $this->user->syncFromProfile();
        }

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
     * @return int|null
     */
    public function getGradYear()
    {
        return $this->gradYear;
    }

    /**
     * @param int|null $gradYear
     * @return $this
     */
    public function setGradYear($gradYear)
    {
        $this->gradYear = $gradYear;

        return $this;
    }

    /**
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param string $studentId
     * @return $this
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getAboutText()
    {
        return $this->aboutText;
    }

    /**
     * @param string $aboutText
     * @return $this
     */
    public function setAboutText($aboutText)
    {
        $this->aboutText = $aboutText;

        return $this;
    }

    /**
     * @return int
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return in_array($this->visible, [
            self::VISIBILITY_ALL,
            self::VISIBILITY_REGISTRED,
            self::VISIBILITY_NON,
        ]);
    }

    /**
     * @return bool
     */
    public function isVisibleForAll()
    {
        return $this->visible == self::VISIBILITY_ALL;
    }

    /**
     * @return bool
     */
    public function isVisibleForRegisteredOnly()
    {
        return $this->visible == self::VISIBILITY_REGISTRED;
    }

    /**
     * @return bool
     */
    public function isInvisible()
    {
        return $this->visible == self::VISIBILITY_NON;
    }

    /**
     * @param bool $visible
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFacebook()
    {
        return $this->facebook == 1;
    }

    /**
     * @param bool $facebook
     * @return $this
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTwitter()
    {
        return $this->twitter == 1;
    }

    /**
     * @param bool $twitter
     * @return $this
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter ? 1 : 0;

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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return $this
     */
    public function syncFromUser()
    {
        if ($this->user) {
            $this->email = $this->user->getLogin();
        }

        return $this;
    }
}
