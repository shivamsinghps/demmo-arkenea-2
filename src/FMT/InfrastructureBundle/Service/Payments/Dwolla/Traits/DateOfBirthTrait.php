<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Traits;

use DateTime;

/**
 * Trait DateOfBirthTrait
 */
trait DateOfBirthTrait
{
    /**
     * @var DateTime|null
     */
    protected $dateOfBirth;

    /**
     * @return DateTime
     */
    public function getDateOfBirth(): DateTime
    {
        return $this->dateOfBirth;
    }

    /**
     * @param DateTime $dateOfBirth
     *
     * @return DateOfBirthTrait
     */
    public function setDateOfBirth(DateTime $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }
}
