<?php

namespace FMT\PublicBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class FmtEmailValidator
 * @package FMT\PublicBundle\Validator\Constraints
 */
class FmtEmailValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }
        $hasInvalidCharacters = (bool)preg_match('/[\s"(),:;<>[\\]]+/s', $value);
        $errorMessage = $hasInvalidCharacters ? $constraint->invalidCharactersMessage : $constraint->message;

        $conditions = array_filter([
            (bool)preg_match("/[!#$%&'*+-\/=?^_`{|}~]?/s", $value),
            (bool)!preg_match('/[\s"(),:;<>[\\]]+/s', $value),
            (bool)preg_match('/^.+\@\S+\.\S+$/', $value),
            filter_var($value, FILTER_VALIDATE_EMAIL),
        ]);


        if (count($conditions) !== 4) {
            $this->context->buildViolation($errorMessage)
                ->addViolation();
        }
    }
}
