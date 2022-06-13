<?php

namespace FMT\PublicBundle\Service;

use FMT\DataBundle\Entity\Campaign;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use FMT\InfrastructureBundle\Service\AmazonS3\StorageInterface;
use FMT\PublicBundle\Controller\Common\CampaignController;
use FMT\PublicBundle\Controller\Common\PublicDashboardController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MarketingPortalService
 * @package FMT\PublicBundle\Service
 */
class MarketingPortalService
{
    /**
     * @var CampaignManagerInterface
     */
    private $campaignManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var StorageInterface
     */
    private $avatarStorage;

    /**
     * MarketingPortalService constructor.
     * @param CampaignManagerInterface $campaignManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param StorageInterface $storage
     */
    public function __construct(
        CampaignManagerInterface $campaignManager,
        UrlGeneratorInterface $urlGenerator,
        StorageInterface $storage
    ) {
        $this->campaignManager = $campaignManager;
        $this->urlGenerator = $urlGenerator;
        $this->avatarStorage = $storage;
    }

    /**
     * @return array
     */
    public function getRandomStudents(): array
    {
        $replaceString = '__SEARCH__';
        $searchLinkPattern = $this->urlGenerator->generate(
            PublicDashboardController::ROUTE_ACTIVE_CAMPAIGN,
            ['base_filter[search]' => $replaceString],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return [
            'searchStudents' => [
                'linkPattern' => $searchLinkPattern,
                'replaceVar' => $replaceString,
            ],
            'students' => $this->getRandomStudentsData()
        ];
    }

    /**
     * @return array
     */
    private function getRandomStudentsData(): array
    {
        $randomActiveCampaigns = $this->campaignManager->getRandomActiveCampaigns(3);

        $studentDataFromCampaign = function (Campaign $campaign) {
            $student = $campaign->getUser();
            $profile = $student->getProfile();
            $campaignUrl = $this->urlGenerator->generate(
                CampaignController::ROUTE_VIEW,
                ['id' => $campaign->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $donateUrl = $this->urlGenerator->generate(
                PublicDashboardController::ROUTE_DONOR_INVITATION_PAGE,
                ['id' => $student->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $avatarSrc = $this->avatarStorage->url($profile->getAvatar()->getFilename());

            return [
                'firstName' => $profile->getFirstName(),
                'lastName' => $profile->getLastName(),
                'school' => $profile->getSchool()->getName(),
                'avatarUrl' => $avatarSrc,
                'campaignLink' => $campaignUrl,
                'donateLink' => $donateUrl,
                'about' => $profile->getAboutText(),
            ];
        };

        return array_map($studentDataFromCampaign, $randomActiveCampaigns);
    }
}
