<?php

namespace FMT\PublicBundle\FormType\Contact;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class ContactInvitationType
 * @package FMT\PublicBundle\FormType\Contact
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CampaignInvitationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isPersonalNoteNeeded', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new NotBlank(),
                ],
                'choices' => [
                    'fmt.contacts.personal_note_not_needed' => 0,
                    'fmt.contacts.personal_note_needed' => 1
                ],
                'data' => 0,
                'label' => false,
            ])
            ->add('personalNote', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'maxlength' => 5000,
                    'readonly' => true
                ],
                'constraints' => [
                    new Length([
                        'max' => 5000,
                    ]),
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new Callback([
                    'callback' => function ($data, ExecutionContextInterface $context) {
                        if (is_null($data['isPersonalNoteNeeded'])) {
                            return;
                        }

                        if ($data['isPersonalNoteNeeded'] && !$data['personalNote']) {
                            $context
                                ->buildViolation((new NotBlank())->message)
                                ->atPath('personalNote')
                                ->addViolation();
                        }
                    }
                ])
            ],
        ]);
    }
}
