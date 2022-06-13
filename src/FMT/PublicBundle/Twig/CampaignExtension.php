<?php

namespace FMT\PublicBundle\Twig;

use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\User;
use FMT\PublicBundle\Controller\Common\CampaignController;
use FMT\PublicBundle\Controller\Common\PaymentController;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FMT\PublicBundle\Controller\Common\PublicDashboardController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Class CampaignExtension
 * @package FMT\PublicBundle\Twig
 */
class CampaignExtension extends Twig_Extension
{
    const COLOR_MAPPED_TO_PERCENT = [
        [
            'percent' => 0,
            'color' => '#b4181a'
        ],
        [
            'percent' => 0.25,
            'color' => '#d05712'
        ],
        [
            'percent' => 0.50,
            'color' => '#fed200'
        ],
        [
            'percent' => 0.75,
            'color' => '#6a992f'
        ],
        [
            'percent' => 1,
            'color' => '#3a863c'
        ]
    ];



    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * MyselfExtension constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack $requestStack
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('sort_campaign_by_major', [$this, 'sortCampaignByMajor']),
            new Twig_SimpleFunction('campaign_fund_link_according_role', [$this, 'campaignFundLinkAccordingRole']),
            new Twig_SimpleFunction('donate_user_link', [$this, 'donateUserLink']),
            new Twig_SimpleFunction('total_goal_amount', [$this, 'totalGoalAmount']),
            new Twig_SimpleFunction('color_by_percent', [$this, 'colorOfCampaignCompletionPercentage']),
        ];
    }

    /**
     * @param SlidingPagination $campaigns
     * @return array
     */
    public function sortCampaignByMajor(SlidingPagination $campaigns)
    {
        $filteredArray = [];
        /**
         * @var $campaign Campaign
         */
        foreach ($campaigns as $campaign) {
            $majorName = $campaign
                ->getUser()
                ->getProfile()
                ->getMajor()
                ->getName();
            $filteredArray[$majorName][] = $campaign;
        }

        return $filteredArray;
    }

    /**
     * @param Campaign $campaign
     * @return string
     */
    public function campaignFundLinkAccordingRole($campaign): string
    {
        $campaignUser = $campaign->getUser();
        /** @var $currentUser User|string|null $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();

        if (empty($currentUser) || is_string($currentUser)) {
            return $this->urlGenerator->generate(PublicDashboardController::ROUTE_DONOR_INVITATION_PAGE, [
                'id' => $campaignUser->getId()
            ]);
        } elseif (!$currentUser->isStudent() || $currentUser === $campaignUser) {
            return $this->urlGenerator->generate(PaymentController::ROUTE_DONATE, [
                'id' => $campaignUser->getId()
            ]);
        } else {
            return $this->urlGenerator->generate(CampaignController::ROUTE_VIEW, [
                'id' => $campaign->getId()
            ]);
        }
    }

    /**
     * @param User $campaignUser
     * @return string
     */
    public function donateUserLink(User $campaignUser)
    {
        /** @var $currentUser User|string|null $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();

        if (empty($currentUser) ||
            is_string($currentUser) ||
            !$currentUser->isStudent() ||
            $currentUser === $campaignUser) {
            return $this->urlGenerator->generate(PaymentController::ROUTE_DONATE, [
                'id' => $campaignUser->getId()
            ]);
        } else {
            return $this->requestStack->getCurrentRequest()->getUri();
        }
    }

    /**
     * @param $campaigns
     * @return int
     */
    public function totalGoalAmount($campaigns)
    {
        $totalSum = 0;
        foreach ($campaigns as $campaign) {
            $totalSum += $campaign->getCampaignGoal();
        }

        return $totalSum;
    }

    /**
     * @param float $percent
     * @return string
     */
    public function colorOfCampaignCompletionPercentage(float $percent)
    {
        $colorsCount = count(self::COLOR_MAPPED_TO_PERCENT);
        for ($i = 1; $i < $colorsCount - 1; $i++) {
            if ($percent < self::COLOR_MAPPED_TO_PERCENT[$i]['percent']) {
                break;
            }
        }
        $lower = self::COLOR_MAPPED_TO_PERCENT[$i - 1];
        $upper = self::COLOR_MAPPED_TO_PERCENT[$i];
        $range = $upper['percent'] - $lower['percent'];
        $rangePercent = ($percent - $lower['percent']) / $range;
        $lowerPercent = 1 - $rangePercent;
        $upperPercent = $rangePercent;
        [$lowerRed, $lowerGreen, $lowerBlue] = sscanf($lower['color'], "#%02x%02x%02x");
        [$upperRed, $upperGreen, $upperBlue] = sscanf($upper['color'], "#%02x%02x%02x");
        $color = [
            'r' => floor($lowerRed * $lowerPercent + $upperRed * $upperPercent),
            'g' => floor($lowerGreen * $lowerPercent + $upperGreen * $upperPercent),
            'b' => floor($lowerBlue * $lowerPercent + $upperBlue * $upperPercent),
        ];

        return sprintf('rgb(%d, %d, %d)', $color['r'], $color['g'], $color['b']);
    }
}
