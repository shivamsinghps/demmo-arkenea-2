<?php

namespace FMT\PublicBundle\FormType;

use FMT\DataBundle\Entity\UserProfile;
use FMT\DataBundle\Entity\UserSchool;
use FMT\DomainBundle\Repository\UserSchoolRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserProfileType
 * @package FMT\PublicBundle\FormType\UserProfile
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserProfileType extends AbstractType
{
    /** @var  UserSchoolRepositoryInterface */
    private $schoolRepository;

    /**
     * UserProfileType constructor.
     * @param UserSchoolRepositoryInterface $schoolRepository
     */
    public function __construct(UserSchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => $options['isLabelNeeded'] ? 'fmt.form.first_name' : false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'First Name',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => $options['isLabelNeeded'] ? 'fmt.form.last_name' : false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Last Name',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('studentId', TextType::class, [
                'label' => $options['isLabelNeeded'] ? 'fmt.form.student_id' : false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'fmt.form.student_id',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('school', EntityType::class, [
                'label' => $options['isLabelNeeded'] ? 'fmt.form.school' : false,
                'required' => true,
                'class' => UserSchool::class,
                'choices' => $this->schoolRepository->getSchoolsCollection(),
                'choice_label' => 'name',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UserProfile::class,
                'validation_groups' => ['Default'],
                'attr' => ['novalidate' => true],
                'isLabelNeeded' => false,
            ]
        );
        $resolver->setAllowedTypes('isLabelNeeded', ['boolean']);
    }
}
