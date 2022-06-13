<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use FMT\DomainBundle\Service\UserTransactionManagerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\Filter\FilterFormFactory;
use FMT\PublicBundle\FormType\Filter\FilterFormFactoryInterface;
use FMT\PublicBundle\Traits\ControllerHelperTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PublicDashboardController
 * @package FMT\PublicBundle\Controller\Common
 * @Route("/")
 * @Template()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PublicDashboardController extends AbstractBaseController
{
    use ControllerHelperTrait;

    const ROUTE_STUDENT_INDEX = "fmt-common-dashboard-student-index";
    const ROUTE_DONOR_INVITATION_PAGE = "fmt-common-donor-invitation-page";
    const ROUTE_DONOR_INDEX = "fmt-common-dashboard-donor-index";
    const ROUTE_INDEX = "fmt-common-dashboard-index";
    const ROUTE_ACTIVE_CAMPAIGN = "fmt-common-dashboard-active-campaign";
    const ROUTE_LOG_IN = "fmt-log-in";
    const ROUTE_SIGN_UP = "fmt-sign-up";
    const ROUTE_SEARCH = "fmt-search";

    /**
     * @var $campaignManager CampaignManagerInterface
     */
    private $campaignManager;

    /**
     * @var FilterFormFactoryInterface
     */
    private $filterFormFactory;

    /**
     * @var UserTransactionManagerInterface
     */
    private $userTransactionManager;

    /**
     * @required
     * @param CampaignManagerInterface $campaignManager
     */
    public function setCampaignManager(CampaignManagerInterface $campaignManager)
    {
        $this->campaignManager = $campaignManager;
    }

    /**
     * @param UserTransactionManagerInterface $manager
     * @required
     */
    public function setUserTransactionManager(UserTransactionManagerInterface $manager)
    {
        $this->userTransactionManager = $manager;
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
     * @return array|RedirectResponse
     * @Route("/search", name=PublicDashboardController::ROUTE_SEARCH)
     */
    public function searchAction(Request $request)
    {
        $form = $this->filterFormFactory->createForm(
            FilterFormFactory::TYPE_ACTIVE_CAMPAIGN,
            [
                'action' => $this->generateUrl(PublicDashboardController::ROUTE_ACTIVE_CAMPAIGN),
            ]
        );

        return [
            'form' => $form->createView(),
        ];
    }
    /**
     * @return RedirectResponse
     * @Route("/", name=PublicDashboardController::ROUTE_INDEX)
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if ($user != null) {
            $url = $this->generateSuccessRedirectHref($user);
            return $this->redirect($url);
        }

        return $this->redirectToRoute(PublicDashboardController::ROUTE_LOG_IN);
    }

    /**
     * @return RedirectResponse|void
     * @Route("/log-in", name=PublicDashboardController::ROUTE_LOG_IN)
     */
    public function logInAction()
    {
        $user = $this->getUser();
        if ($user != null) {
            $url = $this->generateSuccessRedirectHref($user);
            return $this->redirect($url);
        }
    }

    /**
     * @return RedirectResponse|void
     * @Route("/sign-up", name=PublicDashboardController::ROUTE_SIGN_UP)
     */
    public function signUpAction()
    {
        $user = $this->getUser();
        if ($user != null) {
            $url = $this->generateSuccessRedirectHref($user);
            return $this->redirect($url);
        }
    }

    /**
     * @Route("/student/{id}/", requirements={"id":"\d+"}, name=PublicDashboardController::ROUTE_STUDENT_INDEX)
     * @Security("owner.isStudent()")
     * @param User $owner
     * @return array
     */
    public function studentIndexAction(User $owner)
    {
        return [
            'campaigns' => $owner->getCampaigns(),
            'user' => $owner,
            'statistic' => $owner->getStatistic(),
        ];
    }

    /**
     * @Route(
     *     "/student/{id}/invitation/{token}",
     *     requirements={"id":"\d+"},
     *     name=PublicDashboardController::ROUTE_DONOR_INVITATION_PAGE,
     *     defaults={
     *          "token" = null
     *     }
     * )
     * @ParamConverter("student", class="DataBundle:User", options={"invitation":true})
     * @param User $student
     * @return array|RedirectResponse
     */
    public function donorInvitationPageAction(User $student, $token)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute(PaymentController::ROUTE_DONATE, ['id' => $student->getId()]);
        }

        return [
            'student' => $student,
            'token' => $token,
        ];
    }

    /**
     * @param User $owner
     * @param $page
     * @return array
     * @Route("/donor/{id}/{page}",
     *     name=PublicDashboardController::ROUTE_DONOR_INDEX,
     *     defaults={
     *      "page" = 1
     *     },
     *     requirements={"id"="\d+", "page"="\d+"},
     * )
     * @Security("owner.isDonor()")
     */
    public function donorIndexAction(User $owner, $page)
    {
        $limits = BaseFilterOptions::RECORDS_LIMIT;
        $statistic = $owner->getStatistic();

        return [
            //TODO change repository method to actual method which will find all students belongs to donor by donation
            'campaigns' => $this->paginator->paginate(
                $this->campaignManager->getRepository()->findAll(),
                $page,
                array_shift($limits)
            ),
            'user' => $owner,
            'students_founded' => $statistic->getStudentsFounded(),
            'books_purchased' => $statistic->getBooksPurchasedFor(),
            'amount_founded' => $statistic->getAmountFounded(),
            'thanks_for_donations' => $this->userTransactionManager->getThanksData($owner),
        ];
    }

    /**
     * @param Request $request
     * @param $page
     * @return array|JsonResponse
     * @Route("/public/active-campaigns/{page}",
     *     name=PublicDashboardController::ROUTE_ACTIVE_CAMPAIGN,
     *     defaults={
     *      "page" = 1
     *     },
     *     requirements={"page"="\d+"}
     * )
     * @Method({"GET"})
     */
    public function activeCampaignsAction(Request $request, $page)
    {
        $formFilter = $this->filterFormFactory->createForm(
            FilterFormFactory::TYPE_ACTIVE_CAMPAIGN,
            [
                'action' => $this->generateUrl(self::ROUTE_ACTIVE_CAMPAIGN),
                'method' => 'GET'
            ]
        );

        $formFilter->handleRequest($request);
        $formFilterParams = $formFilter->getData();

        $user = $this->getUser();
        $userIsNotStudent = !$user->isStudent();
        $activeUserId = $user->getId();

        $responseParams = [
            'formFilter' => $formFilter->createView(),
            'campaigns' => $this->paginate(
                $this->campaignManager->getByFilter($formFilterParams),
                $page,
                $formFilterParams->getFilterLimit()
            ),
            'sortByMajor' => true,
            'total' => $this->campaignManager->getTotalCountByFilter($formFilterParams),
            'userIsNotStudent' => $userIsNotStudent,
            'activeUserId' => $activeUserId
        ];

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse(
                $responseParams,
                '@Public/common/public_dashboard/_search.html.twig',
                true,
                false
            );
        }

        return $responseParams;
    }
}
