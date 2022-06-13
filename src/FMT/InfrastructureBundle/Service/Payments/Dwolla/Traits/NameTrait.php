<?php

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits;

use Symfony\Component\Validator\Constraints as Assert;

trait NameTrait
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=50)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=50)
     */
    protected $lastName;

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
}
