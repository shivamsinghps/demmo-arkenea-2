<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\DateOfBirthTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits\NameTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AbstractPerson
 */
abstract class AbstractPerson
{
    use NameTrait;
    use DateOfBirthTrait;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $ssn;

    /**
     * @var Address
     *
     * @Assert\Valid
     */
    protected $address;

    /**
     * @var Passport|null
     *
     * @Assert\Valid
     */
    protected $passport;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     *
     * @return $this
     */
    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Passport|null
     */
    public function getPassport(): ?Passport
    {
        return $this->passport;
    }

    /**
     * @param Passport|null $passport
     *
     * @return $this
     */
    public function setPassport(?Passport $passport): self
    {
        $this->passport = $passport;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSsn(): ?string
    {
        return $this->ssn;
    }

    /**
     * @param string|null $ssn
     *
     * @return Controller
     */
    public function setSsn(?string $ssn): self
    {
        $this->ssn = $ssn;

        return $this;
    }
}
