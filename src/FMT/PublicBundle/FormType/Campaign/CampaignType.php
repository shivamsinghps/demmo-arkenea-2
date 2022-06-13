<?php

namespace FMT\PublicBundle\FormType\Campaign;

use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignBook;
use FMT\PublicBundle\FormType\DatepickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class CampaignType
 * @package FMT\PublicBundle\FormType
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CampaignType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('endDate', DatepickerType::class, [
                'label' => 'fmt.campaign.textbooks.form.labels.endDate',
                'attr' => array_merge(DatepickerType::DEFAULT_ATTR, ['data-end' => 1]),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('books', CollectionType::class, [
                'label' => false,
                'entry_type' => CampaignBookType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'fmt.campaign.books.count',
                    ]),
                ],
            ])
            ->add('shippingOption', HiddenType::class, ['attr' => ['data-field' => 'shipping-option']])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetData'])
        ;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (!$data instanceof Campaign) {
            return;
        }

        $params = [
            'label' => 'fmt.campaign.textbooks.form.labels.startDate',
            'attr' => array_merge(DatepickerType::DEFAULT_ATTR, ['data-start' => 1]),
            'constraints' => [
                new NotBlank(),
            ],
        ];

        if ($data->getId() && $data->getStartDate() < (new \DateTime())->setTime(0, 0)) {
            $params['mapped'] = false;
            $params['data'] = $data->getStartDate();
        }

        $form = $event->getForm();
        $form->add('startDate', DatepickerType::class, $params);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Campaign::class,
                'validation_groups' => ['Default'],
                'attr' => ['novalidate' => true],
                'constraints'        => [
                    new Callback([
                        'callback' => [$this, 'validateForm'],
                    ]),
                ],
            ]
        );
    }

    public function validateForm($data, ExecutionContextInterface $context)
    {
        if (!$data instanceof Campaign) {
            return;
        }

        if ($data->getStartDate() > $data->getEndDate()) {
            $context->buildViolation('fmt.campaign.date_range')
                ->atPath('startDate')
                ->setTranslationDomain('validators')
                ->addViolation();
        }


        /** @var CampaignBook[] $books */
        $books = array_values($data->getBooks()->toArray());
        $length = $data->getBooks()->count();

        for ($i = 0; $i < $length; $i++) {
            if (!$data->getBooks()->contains($books[$i])) {
                continue;
            }

            for ($j = $i + 1; $j < $length; $j++) {
                if ($books[$i]->getSku() == $books[$j]->getSku()) {
                    $data->removeBook($books[$j]);
                    $context->buildViolation('fmt.campaign.books.unique')
                        ->atPath('books')
                        ->setTranslationDomain('validators')
                        ->addViolation();
                }
            }
        }
    }
}
