<?php

namespace FMT\PublicBundle\FormType\Security;

use FMT\DataBundle\Entity\User;
use FMT\PublicBundle\FormType\UserProfileType;
use FMT\PublicBundle\Validator\Constraints\FmtEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class RegistrationStudentType
 * @package FMT\PublicBundle\FormType\Security
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RegistrationStudentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', EmailType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email Address',
                ],
                'constraints' => [
                    new NotBlank([
                        'groups' => ['registration']
                    ]),
                    new FmtEmail([
                        'groups' => ['registration']
                    ]),
                ],
            ])
            ->add('profile', UserProfileType::class, [
                'required' => true,
                'label' => false,
                'constraints' => [
                    new Valid(),
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
                'validation_groups' => ['registration'],
                'attr' => ['novalidate' => true],
            ]
        );
    }
}
