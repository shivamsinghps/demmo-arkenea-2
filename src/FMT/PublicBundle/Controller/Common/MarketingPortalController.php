<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\PublicBundle\Service\MarketingPortalService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MarketingPortalController
 * @package FMT\PublicBundle\Controller\Common
 * @Route("/marketingportal")
 * @Template()
 */
class MarketingPortalController extends Controller
{
    const MARKETING_IFRAME = 'marketing-portal-iframe';

    /**
     * @var MarketingPortalService
     */
    private $marketingPortalService;

    /**
     * MarketingPortalController constructor.
     * @param MarketingPortalService $marketingPortalService
     */
    public function __construct(MarketingPortalService $marketingPortalService)
    {
        $this->marketingPortalService = $marketingPortalService;
    }

    /**
     * @return JsonResponse
     * @Route("/randomstudents")
     */
    public function publicDashboardData()
    {
        return new JsonResponse($this->marketingPortalService->getRandomStudents());
    }

    /**
     * @Route("/iframe", name=MarketingPortalController::MARKETING_IFRAME )
     * @param Request $request
     * @return array
     */
    public function iframe(Request $request)
    {
        return [
            'iframe_url' => $request->get('url') . '?iframe'
        ];
    }
}
