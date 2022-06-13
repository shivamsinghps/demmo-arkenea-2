<?php

namespace FMT\InfrastructureBundle\Helper;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * Class FormHelper
 * @package FMT\InfrastructureBundle\Helper
 */
class FormHelper
{
    /**
     * @param FormInterface $form
     * @return array
     */
    public static function collectErrors(FormInterface $form)
    {
        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $propertyPath = self::getFormErrorPropertyPath($error);

            if (empty($errors[$propertyPath])) {
                $errors[$propertyPath] = $error->getMessage();
            }
        }

        return $errors;
    }

    /**
     * @param FormError $error
     * @return string
     */
    public static function getFormErrorPropertyPath(FormError $error)
    {
        $propertyPath = $error->getCause() ? $error->getCause()->getPropertyPath() : '';

        if ('' === $propertyPath) {
            return 'general';
        }

        preg_match_all('/\[(\w+)\]/u', $propertyPath, $matches);

        if ($matches[1]) {
            return implode('-', $matches[1]);
        }

        $fields = explode('.', $propertyPath);

        return array_pop($fields);
    }
}
