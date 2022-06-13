<?php

namespace FMT\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * UserSchool
 *
 * @ORM\Table(name="user_school")
 * @ORM\Entity(repositoryClass="FMT\DataBundle\Repository\UserSchoolRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserSchool implements EntityInterface
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
}
