<?php

namespace FMT\PublicBundle\Controller\Donor;

use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\DomainBundle\Service\UserTransactionManagerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\Filter\FilterFormFactory;
use FMT\PublicBundle\FormType\Filter\FilterFormFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DashboardController
 * @package FMT\PublicBundle\Controller\Donor
 * @Route("/donor/dashboard")
 * @Template()
 * @Security("has_role('ROLE_DONOR')")
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DashboardController extends AbstractBaseController
{
    const ROUTE_INDEX = 'fmt-donor-dashboard-index';

    /**
     * @var FilterFormFactoryInterface
     */
    private $filterFormFactory;

    /**
     * @var UserManagerInterface|null
     */
    private $userManager;

    /**
     * @var UserTransactionManagerInterface
     */
    private $userTransactionManager;

    /**
     * @required
     * @param FilterFormFactoryInterface $filterFormFactory
     */
    public function setFilterFormManager(FilterFormFactoryInterface $filterFormFactory)
    {
        $this->filterFormFactory = $filterFormFactory;
    }

    /**
     * @required
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @required
     * @param UserTransactionManagerInterface $userTransactionManager
     */
    public function setUserTransactionManager(UserTransactionManagerInterface $userTransactionManager)
    {
        $this->userTransactionManager = $userTransactionManager;
    }

    /**
     * @param Request $request
     * @param $page
     * @return array
     * @Route("/{page}",
     *     name=DashboardController::ROUTE_INDEX,
     *     defaults={
     *      "page" = 1
     *     },
     * )
     */
    public function indexAction(Request $request, $page)
    {
        $limits = BaseFilterOptions::RECORDS_LIMIT;

        $form = $this->filterFormFactory->createForm(FilterFormFactory::TYPE_ACTIVE_CAMPAIGN);

        $form->handleRequest($request);

        $user = $this->getUser();
        $statistic = $user->getStatistic();

        return [
            'form' => $form->createView(),
            'students' => $this->paginator->paginate(
                $this->userManager->getDonatedStudentsFiltered($form->getData()),
                $page,
                array_shift($limits)
            ),
            'user' => $user,
            'students_founded' => $statistic->getStudentsFounded(),
            'books_purchased' => $statistic->getBooksPurchasedFor(),
            'amount_founded' => $statistic->getAmountFounded(),
            'thanks_for_donations' => $this->userTransactionManager->getThanksData($user),
        ];
    }
}
