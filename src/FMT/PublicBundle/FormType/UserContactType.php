<?php

namespace FMT\PublicBundle\FormType;

use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\FormType\Subscribers\RegistrationDonorSubscriber;
use FMT\PublicBundle\Validator\Constraints\FmtEmail;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Class UserContactType
 * @package FMT\PublicBundle\FormType
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserContactType extends AbstractType
{
    /** @var UserInterface */
    private $student;

    /** @var UserManagerInterface */
    private $userManager;

    /**
     * @var RegistrationDonorSubscriber
     */
    private $subscriber;

    /**
     * RegistrationDonorType constructor.
     * @param RegistrationDonorSubscriber $subscriber
     * @param UserManagerInterface $userManager
     */
    public function __construct(RegistrationDonorSubscriber $subscriber, UserManagerInterface $userManager)
    {
        $this->subscriber = $subscriber;
        $this->userManager = $userManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->student = $options['student'];

        $builder
            ->setMethod('POST')
            ->add('login', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'fmt.contacts.form.email',
                ],
                'constraints' => [
                    new NotBlank(['groups' => ['contact']]),
                    new FmtEmail(['groups' => ['contact']]),
                    new Callback(['groups' => ['contact'], 'callback' => [$this, 'validateUser']]),
                ],
            ])
            ->add('profile', UserProfileType::class, [
                'label' => false,
                'constraints' => [
                    new Valid(),
                ],
            ])
            ->addEventSubscriber($this->subscriber);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'required' => false,
                'validation_groups' => [
                    'contact',
                ],
                'student' => null,
            ]
        );
    }

    /**
     * @param $data
     * @param ExecutionContext $context
     */
    public function validateUser($data, ExecutionContext $context)
    {
        $user = $this->userManager->getUserByEmail($data);

        if ($user && $user->isAnyStudent()) {
            $context
                ->buildViolation('fmt.contact.student_error')
                ->addViolation();
            return;
        }

        if ($this->student->hasContactByEmail($data)) {
            $context->buildViolation('fmt.contact.already_invited_error')
                ->addViolation();
        }
    }
}
