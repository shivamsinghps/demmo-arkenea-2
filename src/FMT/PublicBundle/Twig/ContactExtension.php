<?php

namespace FMT\PublicBundle\Twig;

use FMT\DataBundle\Entity\Campaign;
use FMT\DomainBundle\Repository\UserTransactionRepositoryInterface;
use FMT\InfrastructureBundle\Helper\CurrencyHelper;
use FMT\PublicBundle\FormType\Contact\CampaignInvitationType;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Twig_Extension;

/**
 * Class ContactInvitationExtension
 * @package FMT\PublicBundle\Twig
 */
class ContactExtension extends Twig_Extension
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UserTransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @required
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactoryInstance(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @required
     * @param UserTransactionRepositoryInterface $transactionRepository
     */
    public function setTransactionRepository(UserTransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_contact_invitation_form', [$this, 'getContactInvitationView']),
            new \Twig_SimpleFunction('get_count_and_amount_donation', [$this, 'getCountAndAmountDonation']),
            new \Twig_SimpleFunction('get_donation_amount_for_campaign', [$this, 'getDonationAmountForCampaign']),
            new \Twig_SimpleFunction('get_donation_amount', [$this, 'getDonationAmount']),
            new \Twig_SimpleFunction('donation_view', [$this, 'getDonationView']),
        ];
    }

    /**
     * @return FormView
     */
    public function getContactInvitationView()
    {
        return $this->formFactory->create(CampaignInvitationType::class)->createView();
    }

    /**
     * @param $donor
     * @param $student
     * @return array
     */
    public function getCountAndAmountDonation($donor, $student)
    {
        if (!$student instanceof UserInterface || !$donor instanceof UserInterface) {
            return [];
        }

        return $this->transactionRepository->getCountAndAmountTransactionsByDonor($donor, $student);
    }

    /**
     * @param array $donationList
     * @param Campaign $campaign
     * @return array
     */
    public function getDonationAmountForCampaign(array $donationList, Campaign $campaign)
    {
        if (empty($donationList)) {
            return [];
        }

        $campaignDonation = array_filter($donationList, function ($campaignDonation) use ($campaign) {
            return $campaignDonation['campaignId'] == $campaign->getId();
        });

        return !empty($campaignDonation) ? array_shift($campaignDonation) : [];
    }

    /**
     * @param array $donationDetails
     * @return array
     */
    public function getDonationAmount(array $donationDetails)
    {
        $result = [
            'amountTransaction' => 0,
            'countTransaction' => 0
        ];

        return array_reduce($donationDetails, function ($val1, $val2) {
            return [
                'amountTransaction' => $val1['amountTransaction'] + $val2['amountTransaction'],
                'countTransaction' => $val1['countTransaction'] + $val2['countTransaction'],
            ];
        }, $result);
    }

    /**
     * @param array $donationDetails
     * @return string
     */
    public function getDonationView(array $donationDetails)
    {
        $amount = 0;
        $count = 0;

        if (!empty($donationDetails)) {
            $amount = $donationDetails['amountTransaction'] ? $donationDetails['amountTransaction'] : 0;
            $count = $donationDetails['countTransaction'] ? $donationDetails['countTransaction'] : 0;
        }

        return sprintf('%s (%s)', CurrencyHelper::priceFilter($amount), $count);
    }
}
