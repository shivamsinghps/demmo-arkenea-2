<?php

namespace FMT\PublicBundle\FormType;

use FMT\DataBundle\Entity\UserProfile;
use FMT\PublicBundle\Validator\Constraints\FmtEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Class DonorProfileType
 * @package FMT\PublicBundle\FormType
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DonorProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profile', UserProfileType::class, ['isLabelNeeded' => true]);

        $builder
            ->get('profile')
            ->remove('studentId')
            ->remove('school')
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
            ->add('facebook', CheckboxType::class, [
                'required' => false,
                'label' => 'fmt.user.profile.facebook_donor_text',
            ])
            ->add('twitter', CheckboxType::class, [
                'required' => false,
                'label' => 'fmt.user.profile.twitter_donor_text',
            ])
            ->add('visible', ChoiceType::class, [
                'required' => true,
                'label' => false,
                'choices' => [
                    'fmt.user.profile.label.donot_visibility_yes' => UserProfile::VISIBILITY_ALL,
                    'fmt.user.profile.label.donot_visibility_no' => UserProfile::VISIBILITY_NON,
                ],
                'choice_attr' => function ($val) {
                    $visible = $val == UserProfile::VISIBILITY_ALL ? 'yes' : 'no';

                    return ['data-visible' => $visible];
                },
                'constraints' => [
                    new Callback([$this, 'validateSocialVisibility']),
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
                'validation_groups' => ['Default'],
                'attr' => ['novalidate' => true],
            ]
        );
    }

    /**
     * @param $data
     * @param ExecutionContext $context
     */
    public function validateSocialVisibility($data, ExecutionContext $context)
    {
        /**
         * @var $form FormInterface
         */
        $form = $context->getRoot();
        $isVisible = $data === UserProfile::VISIBILITY_ALL;
        $isSocialChecked = $form->get('profile')->get('facebook')->getData() ||
            $form->get('profile')->get('twitter')->getData();

        if ($isVisible && !$isSocialChecked) {
            $context->buildViolation('fmt.profile.donor_visibility_social_error')
                ->addViolation();
        }
    }
}
