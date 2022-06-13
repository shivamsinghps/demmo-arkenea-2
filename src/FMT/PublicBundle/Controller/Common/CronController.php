<?php

namespace FMT\PublicBundle\Controller\Common;

use Doctrine\ORM\OptimisticLockException;
use FMT\DomainBundle\Service\BookstorePayment\SendTime;
use FMT\DomainBundle\Service\BookstorePaymentManagerInterface;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use FMT\DomainBundle\Service\Order\MonitorOrder;
use FMT\DomainBundle\Service\OrderReturnsCheckerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CronController
 * @package FMT\PublicBundle\Controller\Common
 * @Route("/cron")
 * @Security("is_granted('canRunByRequest')")
 */
class CronController extends AbstractBaseController
{
    const ROUTE_CAMPAIGN_STARTED = 'fmt-common-cron-campaign-started';
    const ROUTE_CAMPAIGN_FINISHED = 'fmt-common-cron-campaign-finished';
    const ROUTE_MONITORING_ORDER_ITEMS = 'fmt-common-cron-monitoring-order-items';
    const ROUTE_CHECK_ORDER_RETURNS = 'fmt-common-cron-check-order-returns';
    const ROUTE_CHECK_DEPOSIT_BOOKSTORE = 'fmt-common-cron-check-deposit-bookstore';

    /** @var CampaignManagerInterface */
    private $campaignManager;
    
    /** @var MonitorOrder */
    private $monitorOrder;
    
    /** @var OrderReturnsCheckerInterface */
    private $orderReturnsChecker;

    /** @var BookstorePaymentManagerInterface */
    private $paymentManager;

    /** @var SendTime */
    private $sendTime;

    /**
     * CronController constructor.
     * @param CampaignManagerInterface $campaignManager
     */
    public function __construct(
        CampaignManagerInterface $campaignManager,
        MonitorOrder $monitorOrder,
        OrderReturnsCheckerInterface $orderReturnsChecker,
        BookstorePaymentManagerInterface $paymentManager,
        SendTime $sendTime
    ) {
        $this->campaignManager = $campaignManager;
        $this->paymentManager = $paymentManager;
        $this->monitorOrder = $monitorOrder;
        $this->orderReturnsChecker = $orderReturnsChecker;
        $this->sendTime = $sendTime;
    }

    /**
     * @Route("/campaign-started", name=CronController::ROUTE_CAMPAIGN_STARTED )
     * @param Request $request
     * @return JsonResponse
     */
    public function campaignStartedAction(Request $request)
    {
        $dateTime = null;

        if ($date = $request->query->get('date')) {
            $dateTime = new \DateTime($date);
        }

        $this->campaignManager->handleStartedToday($dateTime);

        return $this->createSuccessAjaxResponse();
    }

    /**
     * @Route("/campaign-finished", name=CronController::ROUTE_CAMPAIGN_FINISHED )
     * @param Request $request
     * @return JsonResponse
     */
    public function campaignFinishedAction(Request $request)
    {
        $dateTime = null;

        if ($date = $request->query->get('date')) {
            $dateTime = new \DateTime($date);
        }

        $this->campaignManager->handleFinishedToday($dateTime);

        return $this->createSuccessAjaxResponse();
    }

    /**
     * @Route("/monitoring-order-items", name=CronController::ROUTE_MONITORING_ORDER_ITEMS)
     * @return JsonResponse
     * @throws OptimisticLockException
     */
    public function monitoringOrderItems()
    {
        $this->monitorOrder->monitor();

        return $this->createSuccessAjaxResponse();
    }

    /**
     * @Route("/check-order-returns", name=CronController::ROUTE_CHECK_ORDER_RETURNS)
     * @return JsonResponse
     */
    public function checkOrderReturns()
    {
        $this->orderReturnsChecker->check();

        return $this->createSuccessAjaxResponse();
    }

    /**
     * @Route("/check-deposit-bookstore", name=CronController::ROUTE_CHECK_DEPOSIT_BOOKSTORE)
     * @return JsonResponse
     */
    public function checkDepositBookstore()
    {
        $this->paymentManager->sendTransfer($this->sendTime, false);

        return $this->createSuccessAjaxResponse();
    }
}
