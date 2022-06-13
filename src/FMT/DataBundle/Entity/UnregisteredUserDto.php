<?php

namespace FMT\DataBundle\Entity;

class UnregisteredUserDto implements MinimalUserInterface
{
    /** @var string */
    private $email;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /**
     * UnregisteredUserDto constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->email = $data['email'] ?? '';
        $this->firstName = $data['firstName'] ?? '';
        $this->lastName = $data['lastName'] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @inheritDoc
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @inheritDoc
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFullName()
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    /**
     * @inheritDoc
     */
    public function isCompleted()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isRegistered()
    {
        return false;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }
}
