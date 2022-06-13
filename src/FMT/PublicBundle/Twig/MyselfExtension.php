<?php

namespace FMT\PublicBundle\Twig;

use FMT\DataBundle\Entity\Campaign;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use \Twig_Extension;
use \Twig_SimpleFilter;

/**
 * Class MyselfExtension
 * @package FMT\PublicBundle\Twig
 */
class MyselfExtension extends Twig_Extension
{
    const NAME = 'myself';

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /**
     * MyselfExtension constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('fund_student_name', [$this, 'fundStudentNameFilter']),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_my_campaign', [$this, 'isMyCampaignFunction']),
        ];
    }

    /**
     * @param Campaign $campaign
     * @return string
     */
    public function fundStudentNameFilter(Campaign $campaign)
    {
        $student = $campaign->getUser();
        $currentUser = $this->tokenStorage->getToken()->getUser();

        return 'fmt.campaign.student.' . ($currentUser == $student ? 'fund_myself' : 'fund_student');
    }

    /**
     * @param Campaign $campaign
     * @return bool
     */
    public function isMyCampaignFunction(Campaign $campaign)
    {
        $student = $campaign->getUser();
        $currentUser = $this->tokenStorage->getToken()->getUser();

        return $currentUser == $student;
    }
}
