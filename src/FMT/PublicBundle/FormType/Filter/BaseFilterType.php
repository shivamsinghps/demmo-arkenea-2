<?php

namespace FMT\PublicBundle\FormType\Filter;

use FMT\DataBundle\Model\BaseFilterOptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BaseFilterType
 * @package FMT\PublicBundle\FormType\Filter
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class BaseFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sortDirection = BaseFilterOptions::SORT_DIRECTION;
        $defaultSort = array_keys($sortDirection);
        $builder
            ->setMethod('GET')
            ->add('sortBy', ChoiceType::class, [
                'choices' => array_flip($options['sortChoices']),
                'label' => 'fmt.form.filter.sort_by',
            ])
            ->add('sortDirection', ChoiceType::class, [
                'choices' => array_flip($sortDirection),
                'label' => false,
                'expanded' => true,
                'data' => array_shift($defaultSort),
                'choice_attr' => function ($choice, $key, $value) {
                    return [
                        'data-sort-order-choice' => $value,
                    ];
                },
            ])
            ->add('limit', ChoiceType::class, [
                'label' => 'fmt.form.filter.records_per_page',
                'choices' => array_flip(BaseFilterOptions::RECORDS_LIMIT),
            ])
            ->add('search', TextType::class, [
                'label' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => BaseFilterOptions::class,
                    'validation_groups' => ['Default'],
                    'attr' => ['novalidate' => true],
                    'sortChoices' => FilterFormFactory::SORT_CHOICES[FilterFormFactory::TYPE_ACTIVE_CAMPAIGN],
                    'required' => false,
                ]
            )
            ->setAllowedTypes('sortChoices', ['array']);
    }
}
