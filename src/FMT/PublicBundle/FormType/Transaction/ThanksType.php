<?php

namespace FMT\PublicBundle\FormType\Transaction;

use FMT\DataBundle\Entity\UserTransaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ThanksType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('thanks', TextareaType::class, [
            'attr'        => [
                'maxlength' => 128,
            ],
            'required'    => true,
            'constraints' => [
                new NotBlank(),
                new Length([
                    'max' => 128
                ])
            ],
            'label'       => false,
        ]);
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            /** @var UserTransaction $transaction */
            $transaction = $event->getData();
            if (empty($transaction)) {
                return;
            }
            $form = $event->getForm();
            $form->add('id', HiddenType::class, [
                'mapped' => false,
                'data'   => $transaction->getId(),
            ]);
        });
    }
}
