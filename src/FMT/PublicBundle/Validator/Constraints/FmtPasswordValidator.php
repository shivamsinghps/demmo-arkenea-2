<?php

namespace FMT\PublicBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class FmtPasswordValidator
 * @package FMT\PublicBundle\Validator\Constraints
 */
class FmtPasswordValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {

        $conditions = array_filter([
            strlen($value) >= 8,
            (bool)preg_match('/[A-Z]+/s', $value),
            (bool)preg_match('/[a-z]+/s', $value),
            (bool)preg_match('/\d+/s', $value),
            (bool)preg_match("/[\W]+/s", $value),
        ]);


        if (count($conditions) !== 5) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
