<?php

namespace FMT\PublicBundle\Controller\Student;

use FMT\PublicBundle\Controller\AbstractBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class DashboardController
 *
 * @var Response
 * @var Route
 * @var Template
 * @var Security
 * @var Method
 * @var ParamConverter
 *
 * @package FMT\PublicBundle\Controller
 * @Route("/student/dashboard")
 * @Template()
 * @Security("has_role('ROLE_STUDENT')")
 */
class DashboardController extends AbstractBaseController
{
    const ROUTE_INDEX = "fmt-student-dashboard";

    /**
     * @return array
     * @Route("/", name=DashboardController::ROUTE_INDEX)
     */
    public function indexAction()
    {
        $booksPurchased = 0;
        $booksCount = 0;
        foreach ($this->getUser()->getCampaigns() as $campaign) {
            foreach ($campaign->getBooks() as $book) {
                $booksCount++;
                if ($book->isOrdered()) {
                    $booksPurchased++;
                }
            }
        }
        return [
            'campaigns' => $this->getUser()->getCampaigns(),
            'user' => $this->getUser(),
            'statistic' => $this->getUser()->getStatistic(),
            'booksPurchased' => $booksPurchased,
            'booksCount' => $booksCount,
        ];
    }
}
