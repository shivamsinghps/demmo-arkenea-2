<?php

namespace FMT\PublicBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class FmtPassword
 * @package FMT\PublicBundle\Validator\Constraints
 */
class FmtPassword extends Constraint
{
    /**
     * @var string
     */
    public $message = "fmt.password.error";
}
