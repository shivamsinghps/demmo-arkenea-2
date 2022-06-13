<?php

namespace FMT\PublicBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Email;

/**
 * Class FmtEmail
 * @package FMT\PublicBundle\Validator\Constraints
 */
class FmtEmail extends Email
{
    /**
     * @var string
     */
    public $message = "fmt.email.error";

    public $invalidCharactersMessage = 'fmt.email.invalid_characters_error';
}
