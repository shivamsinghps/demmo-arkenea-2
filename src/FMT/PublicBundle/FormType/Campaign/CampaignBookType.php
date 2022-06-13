<?php

namespace FMT\PublicBundle\FormType\Campaign;

use FMT\DataBundle\Entity\CampaignBook;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BookType
 * @package FMT\PublicBundle\FormType
 */
class CampaignBookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productFamilyId', HiddenType::class, ['attr' => ['data-field' => 'family-id']])
            ->add('sku', HiddenType::class, ['attr' => ['data-field' => 'sku']])
            ->add('title', HiddenType::class, ['attr' => ['data-field' => 'title']])
            ->add('author', HiddenType::class, ['attr' => ['data-field' => 'author']])
            ->add('class', HiddenType::class, ['attr' => ['data-field' => 'class']])
            ->add('isbn', HiddenType::class, ['attr' => ['data-field' => 'isbn']])
            ->add('price', HiddenType::class, ['attr' => ['data-field' => 'price']])
            ->add('state', HiddenType::class, ['attr' => ['data-field' => 'state']])
            ->add('status', HiddenType::class, ['attr' => ['data-field' => 'status']])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => CampaignBook::class,
                'validation_groups' => ['Default'],
                'attr' => ['novalidate' => true],
            ]
        );
    }
}
