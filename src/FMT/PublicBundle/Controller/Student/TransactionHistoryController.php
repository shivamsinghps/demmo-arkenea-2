<?php

namespace FMT\PublicBundle\Controller\Student;

use FMT\DomainBundle\Service\UserTransactionManagerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\Filter\FilterFormFactory;
use FMT\PublicBundle\FormType\Filter\FilterFormFactoryInterface;
use FMT\PublicBundle\Traits\ControllerHelperTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TransactionHistoryController
 * @package FMT\PublicBundle\Controller\Student
 * @Route("/student/transaction-history")
 * @Template()
 * @Security("has_role('ROLE_STUDENT')")
 */
class TransactionHistoryController extends AbstractBaseController
{
    use ControllerHelperTrait;

    const ROUTE_INDEX = 'fmt-student-transaction-history-index';

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
     */
    public function indexAction(Request $request, $page)
    {
        $formFilter = $this->filterFormFactory->createForm(FilterFormFactory::TYPE_STUDENT_TRANSACTION_HISTORY);

        $formFilter->handleRequest($request);
        $formFilterParams = $formFilter->getData();
        $student = $this->getUser();
        $transactions = $this->userTransactionManager->getStudentTransactionByFilterQB($formFilterParams, $student);

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
                '@Public/student/transaction_history/_search.html.twig',
                true,
                false
            );
        }

        return $responseParams;
    }
}
