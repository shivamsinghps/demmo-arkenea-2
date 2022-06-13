<?php

namespace FMT\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * UserMajor
 *
 * @ORM\Table(name="user_major")
 * @ORM\Entity(repositoryClass="FMT\DataBundle\Repository\UserMajorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserMajor implements EntityInterface
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="smallint", nullable=false)
     */
    private $active = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="campus_id", type="integer", nullable=false)
     */
    private $campusId;

    /**
     * @var integer
     *
     * @ORM\Column(name="department_id", type="integer", nullable=false)
     */
    private $departmentId;

    /**
     * @ORM\OneToMany(targetEntity="UserProfile", mappedBy="major")
     */
    protected $profile;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active == 1;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active ? 1 : 0;

        return $this;
    }

    /**
     * @return int
     */
    public function getCampusId()
    {
        return $this->campusId;
    }

    /**
     * @param $campusId
     * @return $this
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;

        return $this;
    }

    /**
     * @return int
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     *
     * @param $departmentId
     * @return $this
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param $profile
     * @return $this
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }
}
