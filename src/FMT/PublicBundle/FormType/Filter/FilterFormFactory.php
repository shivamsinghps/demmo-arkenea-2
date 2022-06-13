<?php

namespace FMT\PublicBundle\FormType\Filter;

use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Service\UserManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class FilterFormFactory
 * @package FMT\PublicBundle\FormType\Filter
 */
class FilterFormFactory implements FilterFormFactoryInterface
{
    const TYPE_ACTIVE_CAMPAIGN = 'ACF';
    const TYPE_DONATED_STUDENTS = 'TDS';
    const TYPE_DONOR_TRANSACTION_HISTORY = 'DTHF';
    const TYPE_STUDENT_TRANSACTION_HISTORY = 'STHF';
    const SORT_CHOICES = [
        self::TYPE_ACTIVE_CAMPAIGN => [
            'profile.firstName' => 'fmt.form.first_name',
            'profile.lastName' => 'fmt.form.last_name',
            'profile.gradYear' => 'fmt.form.grad_year',
            'major.name' => 'fmt.form.major',
        ],
        self::TYPE_DONOR_TRANSACTION_HISTORY => [
            'transaction.id' => 'fmt.form.filter.row_number',
            'profile.firstName,profile.lastName' => 'fmt.form.filter.student',
            'transaction.date' => 'fmt.form.filter.trx_date',
            'amount' => 'fmt.form.filter.trx_amount',
        ],
        self::TYPE_STUDENT_TRANSACTION_HISTORY => [
            'transaction.id' => 'fmt.form.filter.row_number',
            'transaction.date' => 'fmt.form.filter.trx_date',
            'amount' => 'fmt.form.filter.trx_amount',
        ],
        self::TYPE_DONATED_STUDENTS => [],
    ];
    const FORM_PREPARE_METHOD = [
        self::TYPE_ACTIVE_CAMPAIGN => 'makeActiveCampaignFilter',
    ];

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /** @var UserManagerInterface */
    private $userManager;

    /**
     * FilterFormFactory constructor.
     * @param FormFactoryInterface $formFactory
     * @param UserManagerInterface $userManager
     */
    public function __construct(FormFactoryInterface $formFactory, UserManagerInterface $userManager)
    {
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
    }

    /**
     * @param $type
     * @param array $options
     * @return FormInterface
     */
    public function createForm($type, $options = [])
    {
        $data = new BaseFilterOptions();
        $options['sortChoices'] = self::SORT_CHOICES[$type];
        $form = $this->formFactory->create(BaseFilterType::class, $data, $options);

        if (isset(self::FORM_PREPARE_METHOD[$type]) && method_exists($this, self::FORM_PREPARE_METHOD[$type])) {
            $form = call_user_func([$this, self::FORM_PREPARE_METHOD[$type]], $form);
        }

        return $form;
    }

    /**
     * @param FormInterface $form
     * @return FormInterface
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function makeActiveCampaignFilter(FormInterface $form)
    {
        $form->add('major', EntityType::class, [
            'class' => UserMajor::class,
            'label' => 'fmt.form.filter.major',
            'choice_label' => 'name',
            'choices' => $this->userManager->getMajors(true),
            'placeholder' => 'fmt.form.filter.all_majors',
        ]);

        return $form;
    }
}
