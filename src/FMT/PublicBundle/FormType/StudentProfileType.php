<?php

namespace FMT\PublicBundle\FormType;

use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Entity\UserProfile;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\FormType\Subscribers\UserAvatarSubscriber;
use FMT\PublicBundle\Validator\Constraints\FmtEmail;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class StudentProfileType
 * @package FMT\PublicBundle\FormType
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class StudentProfileType extends AbstractType
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var UserAvatarSubscriber
     */
    private $userAvatarSubscriber;

    /**
     * StudentProfileType constructor.
     * @param UserAvatarSubscriber $userAvatarSubscriber
     * @param UserManagerInterface $userManager
     */
    public function __construct(
        UserAvatarSubscriber $userAvatarSubscriber,
        UserManagerInterface $userManager
    ) {
        $this->userAvatarSubscriber = $userAvatarSubscriber;
        $this->userManager = $userManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $majors = $this->userManager->getMajors()->getValues();

        $builder
            ->add('profile', UserProfileType::class, [
                'isLabelNeeded' => true,
                'label' => false,
            ]);

        $builder
            ->get('profile')
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'fmt.user.profile.label.email',
                'attr' => [
                    'placeholder' => 'fmt.user.profile.email_placeholder',
                ],
                'constraints' => [
                    new NotBlank(),
                    new FmtEmail(),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('major', EntityType::class, [
                'required' => true,
                'class' => UserMajor::class,
                'label' => 'fmt.form.major',
                'choice_label' => 'name',
                'choices' => $majors,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('gradYear', IntegerType::class, [
                'required' => true,
                'label' => 'fmt.form.grad_year',
                'attr' => [
                    'placeholder' => 'fmt.form.grad_year',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 1900,
                        'max' => 3000,
                        'invalidMessage' => 'fmt.grad_year.error',
                        'maxMessage' => 'fmt.grad_year.error',
                        'minMessage' => 'fmt.grad_year.error',
                    ]),
                ],
            ])
            ->add('visible', ChoiceType::class, [
                'required' => true,
                'label' => false,
                'choices' => array_flip([
                    UserProfile::VISIBILITY_ALL => 'fmt.user.profile.label.visibility_all',
                    UserProfile::VISIBILITY_REGISTRED => 'fmt.user.profile.label.visibility_register',
                    UserProfile::VISIBILITY_NON => 'fmt.user.profile.label.visibility_no',
                ]),
                'choice_attr' => function ($val) {
                    $visible = ($val == UserProfile::VISIBILITY_NON || $val == UserProfile::VISIBILITY_REGISTRED) ? 'no'
                        : 'yes';

                    return ['data-visible' => $visible];
                },
            ])
            ->add('facebook', CheckboxType::class, [
                'required' => false,
                'label' => 'fmt.user.profile.facebook_text',
            ])
            ->add('twitter', CheckboxType::class, [
                'required' => false,
                'label' => 'fmt.user.profile.twitter_text',
            ])
            ->add('aboutText', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'rows' => 10,
                    'maxlength' => 5000,
                ],
                'constraints' => [
                    new Length([
                        'max' => 5000,
                    ]),
                ],
            ])
            ->add('address', AddressType::class, [
                'label' => false,
            ])
            ->add('avatar', UserAvatarType::class, [
                'label' => false,
                'oldAvatarName' => $options['oldAvatarName'],
            ])
            ->addEventSubscriber($this->userAvatarSubscriber);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => ['Default'],
                'attr' => ['novalidate' => true],
                'oldAvatarName' => null,
            ]
        );
        $resolver->setAllowedTypes('oldAvatarName', ['string', 'null']);
    }
}
