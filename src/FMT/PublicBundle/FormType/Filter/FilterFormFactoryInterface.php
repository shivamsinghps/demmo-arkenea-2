<?php

namespace FMT\PublicBundle\FormType\Filter;

use Symfony\Component\Form\FormInterface;

/**
 * Interface FilterFormFactoryInterface
 * @package FMT\PublicBundle\FormType\Filter
 */
interface FilterFormFactoryInterface
{
    /**
     * @param $type
     * @param array $options
     * @return FormInterface
     */
    public function createForm($type, $options = []);
}
