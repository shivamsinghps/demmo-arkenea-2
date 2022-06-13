<?php

namespace FMT\PublicBundle\FormType\Security;

use FMT\DataBundle\Entity\User;
use FMT\PublicBundle\Validator\Constraints\FmtPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserPasswordType
 * @package FMT\PublicBundle\FormType\Security
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => [
                'label' => false,
                'attr' => [
                    'placeholder' => 'fmt.form.password',
                ],
            ],
            'second_options' => [
                'label' => false,
                'attr' => [
                    'placeholder' => 'fmt.form.re_password',
                ],
            ],
            'invalid_message' => 'Invalid password',
            'constraints' => [
                new NotBlank(),
                new FmtPassword(),
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
                'data_class' => User::class,
                'validation_groups' => ['Default'],
                'attr' => ['novalidate' => true],
            ]
        );
    }
}
