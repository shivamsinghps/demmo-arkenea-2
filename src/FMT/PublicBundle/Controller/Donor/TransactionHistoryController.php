<?php

namespace FMT\PublicBundle\Controller\Donor;

use FMT\DomainBundle\Service\UserTransactionManagerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\Filter\FilterFormFactory;
use FMT\PublicBundle\FormType\Filter\FilterFormFactoryInterface;
use FMT\PublicBundle\Traits\ControllerHelperTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TransactionHistoryController
 * @package FMT\PublicBundle\Controller\Donor
 * @Route("/donor/transaction-history")
 * @Template()
 * @Security("has_role('ROLE_DONOR')")
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TransactionHistoryController extends AbstractBaseController
{
    use ControllerHelperTrait;

    const ROUTE_INDEX = 'fmt-donor-transaction-history-index';

    /**
     * @var UserTransactionManagerInterface
     */
    private $userTransactionManager;

    /**
     * @var FilterFormFactoryInterface
     */
    private $filterFormFactory;

    /**
     * @required
     * @param UserTransactionManagerInterface $userTransactionManager
     */
    public function setCampaignManager(UserTransactionManagerInterface $userTransactionManager)
    {
        $this->userTransactionManager = $userTransactionManager;
    }

    /**
     * @required
     * @param FilterFormFactoryInterface $filterFormFactory
     */
    public function setFilterFormManager(FilterFormFactoryInterface $filterFormFactory)
    {
        $this->filterFormFactory = $filterFormFactory;
    }

    /**
     * @param Request $request
     * @param $page
     * @return array|JsonResponse
     * @Route("/{page}",
     *     name=TransactionHistoryController::ROUTE_INDEX,
     *     defaults={
     *      "page" = 1
     *     },
     *     requirements={"page"="\d+"}
     * )
     * @Method({"GET"})
     */
    public function indexAction(Request $request, $page)
    {
        $formFilter = $this->filterFormFactory->createForm(FilterFormFactory::TYPE_DONOR_TRANSACTION_HISTORY);

        $formFilter->handleRequest($request);
        $formFilterParams = $formFilter->getData();
        $donor = $this->getUser();
        $transactions = $this->userTransactionManager->getDonorTransactionByFilterQB($formFilterParams, $donor);

        $responseParams = [
            'formFilter' => $formFilter->createView(),
            'transactions' => $this->paginate(
                $transactions,
                $page,
                $formFilterParams->getFilterLimit()
            ),
        ];

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse(
                $responseParams,
                '@Public/donor/transaction_history/_search.html.twig',
                true,
                false
            );
        }

        return $responseParams;
    }
}
