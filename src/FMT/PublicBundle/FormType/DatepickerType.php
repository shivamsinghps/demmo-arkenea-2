<?php

namespace FMT\PublicBundle\FormType;

use FMT\InfrastructureBundle\Helper\DateHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DatepickerType
 * @package FMT\PublicBundle\FormType
 */
class DatepickerType extends AbstractType
{
    const DEFAULT_ATTR = [
        'class' => 'datepicker',
        'placeholder' => 'Choose date',
        'data-date-format' => DateHelper::JS_STANDARD_FORMAT,
    ];

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'widget' => 'single_text',
                'format' => DateHelper::INTL_FORMAT,
                'attr' => self::DEFAULT_ATTR,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return DateType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'datepicker';
    }
}
