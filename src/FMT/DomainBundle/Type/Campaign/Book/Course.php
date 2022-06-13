<?php

namespace FMT\DomainBundle\Type\Campaign\Book;

class Course
{
    const UNKNOWN_NAME = 'Unknown';

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $realName;

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
     * @return string
     */
    public function getRealName()
    {
        return $this->realName;
    }
}
