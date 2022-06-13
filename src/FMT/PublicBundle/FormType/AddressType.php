<?php

namespace FMT\PublicBundle\FormType;

use FMT\DataBundle\Entity\Address;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AddressType
 * @package FMT\PublicBundle\FormType
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AddressType extends AbstractType
{
    /**
     * @var array $usStates
     */
    private $usStates;

    /**
     * @param array $usStates
     */
    public function __construct($usStates)
    {
        $this->usStates = $usStates;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('region', ChoiceType::class, [
                'required' => true,
                'label' => 'fmt.form.region',
                'choices' => array_flip($this->usStates),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => 'fmt.form.city',
                'attr' => [
                    'placeholder' => 'fmt.form.city',
                ],
                'constraints' => [
                    new NotBlank(),
                ],

            ])
            ->add('code', TextType::class, [
                'required' => true,
                'label' => 'fmt.form.post_code',
                'attr' => [
                    'placeholder' => 'fmt.form.post_code',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('address1', TextType::class, [
                'required' => true,
                'label' => 'fmt.form.address_line',
                'attr' => [
                    'placeholder' => 'fmt.form.address_line',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('address2', TextType::class, [
                'required' => false,
                'label' => 'fmt.form.address_line2',
                'attr' => [
                    'placeholder' => 'fmt.form.address_line2',
                ],
                'constraints' => [
                    new Length([
                        'max' => 50,
                    ]),
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Address::class,
                'validation_groups' => ['Default'],
                'attr' => ['novalidate' => true],
            ]
        );
    }
}
