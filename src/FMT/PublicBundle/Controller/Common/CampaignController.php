<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\DataBundle\Entity\Campaign;
use FMT\DomainBundle\Service\Manager\CampaignManager;
use FMT\PublicBundle\Controller\AbstractBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CampaignController
 *
 * @var Response
 * @var Route
 * @var Template
 * @var Security
 * @var Method
 * @var ParamConverter
 *
 * @package FMT\PublicBundle\Controller
 * @Route("/campaign")
 * @Template()
 */
class CampaignController extends AbstractBaseController
{
    const ROUTE_VIEW = "fmt-common-campaign-view";
    const ROUTE_VALIDATE_DONATE_AMOUNT = "fmt-common-campaign-validate-donate-amount";

    /**
     * @param Campaign $campaign
     * @return array
     * @Route("/{id}/view", requirements={"id":"\d+"}, name=CampaignController::ROUTE_VIEW)
     * @ParamConverter("campaign", class="DataBundle:Campaign")
     * @Security("is_granted('canView', campaign)")
     */
    public function viewAction(Campaign $campaign)
    {
        $user = $this->getUser();
        $userIsNotStudent = !$user->isStudent();
        $activeUserId = $user->getId();

        $this->addCantDonateWarning($campaign);
        return [
            'campaign' => $campaign,
            'userIsNotStudent' => $userIsNotStudent,
            'activeUserId' => $activeUserId
        ];
    }

    /**
     * @param Campaign $campaign
     */
    private function addCantDonateWarning(Campaign $campaign)
    {
        $currentUser = $this->getUser();

        if (!empty($currentUser) && $campaign->getUser() !== $currentUser && $currentUser->isStudent()) {
            $this->addFlashBagError('fmt.campaign.student_donate_not_implemented');
        }
    }

    /**
     * @Route(
     *     "/{id}/validate-donate-amount",
     *      requirements={"id":"\d+"},
     *      name=CampaignController::ROUTE_VALIDATE_DONATE_AMOUNT
     *     )
     * @ParamConverter("campaign", class="DataBundle:Campaign")
     * @param Request $request
     * @param Campaign $campaign
     * @param CampaignManager $campaignManager
     * @return JsonResponse
     */
    public function validateDonateAmountAction(
        Request $request,
        Campaign $campaign,
        CampaignManager $campaignManager
    ) {
        $this->checkAjaxRequest($request);
        $amount = $request->get('amount', '0');

        return new JsonResponse($campaignManager->validateDonateAmount($campaign, $amount));
    }
}
