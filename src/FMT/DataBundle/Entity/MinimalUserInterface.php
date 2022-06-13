<?php

namespace FMT\DataBundle\Entity;

/**
 * Interface MinimalUserInterface
 * @package FMT\DataBundle\Entity
 */
interface MinimalUserInterface
{
    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return string|null
     */
    public function getFirstName(): ?string;

    /**
     * @param string|null $firstName
     * @return $this
     */
    public function setFirstName($firstName);

    /**
     * @return string|null
     */
    public function getLastName(): ?string;

    /**
     * @param string|null $lastName
     * @return $this
     */
    public function setLastName($lastName);

    /**
     * @return string|null
     */
    public function getFullName();

    /**
     * @return bool
     */
    public function isCompleted();

    /**
     * @return bool
     */
    public function isRegistered();
}
