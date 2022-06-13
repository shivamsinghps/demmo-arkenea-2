<?php

namespace FMT\PublicBundle\Twig;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserAvatar;
use FMT\DomainBundle\Repository\UserRepositoryInterface;
use FMT\DomainBundle\Service\FileManagerInterface;
use FMT\InfrastructureBundle\Helper\CurrencyHelper;
use FMT\InfrastructureBundle\Service\AmazonS3\StorageInterface;
use FMT\PublicBundle\Controller\Donor\DashboardController as DonorDashboardController;
use FMT\PublicBundle\Controller\Donor\ProfileController as DonorProfileController;
use FMT\PublicBundle\Controller\Student\ContactController;
use FMT\PublicBundle\Controller\Student\DashboardController as StudentDashboardController;
use FMT\PublicBundle\Controller\Student\ProfileController as StudentProfileController;
use FMT\PublicBundle\Controller\Common\CartController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserExtension
 * @package FMT\PublicBundle\Twig
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserExtension extends \Twig_Extension
{
    const USER_ROUTES = [
        User::ROLE_STUDENT => [
            'profile' => StudentProfileController::ROUTE_INDEX,
            'dashboard' => StudentDashboardController::ROUTE_INDEX,
            'contacts' => ContactController::ROUTE_INDEX,
            'fmt-cart-index' => CartController::ROUTE_INDEX,
        ],
        User::ROLE_DONOR => [
            'profile' => DonorProfileController::ROUTE_INDEX,
            'dashboard' => DonorDashboardController::ROUTE_INDEX,
            'fmt-cart-index' => CartController::ROUTE_INDEX,
        ],
    ];

    const USER_SHARE_TEXT = [
        'text' => 'fmt.social_network.share_text',
        'text_self' => 'fmt.social_network.share_text_self_user',
    ];

    const SHARE_LINKS = [
        'fb' => 'https://www.facebook.com/sharer.php?',
        'tw' => 'https://twitter.com/intent/tweet?',
    ];

    const SHARE_PARAM_NAME = 'share';

    const SHARE_PARAM = [
        'self' => 'self',
        'guest' => 'guest',
    ];

    /**
     * @var StorageInterface
     */
    private $avatarStorage;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var FileManagerInterface
     */
    private $fileManager;

    /**
     * UserExtension constructor.
     * @param StorageInterface $avatarStorage
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     */
    public function __construct(
        StorageInterface $avatarStorage,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        TranslatorInterface $translator
    ) {
        $this->avatarStorage = $avatarStorage;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * @required
     * @param FileManagerInterface $fileManager
     */
    public function setFileManager(FileManagerInterface $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * @required
     * @param UserRepositoryInterface $repository
     */
    public function setUserRepository(UserRepositoryInterface $repository)
    {
        $this->userRepository = $repository;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_user_available_route', [$this, 'getUserAvailableRoute']),
            new \Twig_SimpleFunction('get_user_avatar', [$this, 'getUserAvatar']),
            new \Twig_SimpleFunction('generate_share_text', [$this, 'generateShareText']),
            new \Twig_SimpleFunction('generate_share_link', [$this, 'generateShareLink']),
            new \Twig_SimpleFunction('generate_share_param_val', [$this, 'generateShareParamVal']),
            // TODO: Use `get_registrations_amount` function on admin panel
            new \Twig_SimpleFunction('get_registrations_amount', [$this, 'getRegistrationsAmount']),
            new \Twig_SimpleFunction('get_temp_avatar_filename', [$this, 'getTempAvatarFilename']),
        ];
    }

    /**
     * @param User $user
     * @param $routeName
     * @return mixed
     */
    public function getUserAvailableRoute(User $user, $routeName)
    {
        if ($user->isStudent()) {
            return self::USER_ROUTES[User::ROLE_STUDENT][$routeName] ?? [];
        }

        if ($user->isIncompleteStudent()) {
            return self::USER_ROUTES[User::ROLE_STUDENT]['profile'];
        }

        if ($user->isDonor()) {
            return self::USER_ROUTES[User::ROLE_DONOR][$routeName] ?? [];
        }

        if ($user->isIncompleteDonor()) {
            return self::USER_ROUTES[User::ROLE_DONOR]['profile'];
        }

        return [];
    }

    /**
     * @param User $user
     * @return bool|string
     */
    public function getUserAvatar(User $user)
    {
        $avatar = $user->getProfile()->getAvatar();

        if (!$avatar instanceof UserAvatar || !$avatar->getFilename()) {
            return false;
        }

        try {
            return $this->avatarStorage->url($avatar->getFilename());
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param User $user
     * @return string
     */
    public function generateShareText(User $user)
    {
        if ($user->isStudent()) {
            return $this->generateStudentShareText($user);
        } else {
            return $this->generateDonorShareText($user);
        }
    }

    /**
     * @param User $user
     * @return string
     */
    private function generateStudentShareText(User $user)
    {
        $shareParam = $this->requestStack->getCurrentRequest()->get(UserExtension::SHARE_PARAM_NAME);
        $isSelfText = $shareParam === UserExtension::SHARE_PARAM['self'] || $this->isCurrentUser($user);
        $statistic = $user->getStatistic();

        $shareText = $isSelfText ?
            self::USER_SHARE_TEXT['text_self'] :
            self::USER_SHARE_TEXT['text'];

        $statisticData = [
            '%campaigns%' => $user->getCampaigns()->count(),
            '%books_purchased%' => $statistic->getBooksPurchasedMe(),
            '%funds_total%' => CurrencyHelper::priceFilter($statistic->getDonatedToMe()),
        ];

        $statisticText = $this->translator->trans('fmt.social_network.student_share_statistics', $statisticData);

        return sprintf("%s\n%s", $this->translator->trans($shareText), $statisticText);
    }

    /**
     * @param User|null $user
     * @return bool
     */
    private function isCurrentUser($user)
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();
        return $currentUser === $user;
    }

    /**
     * @param User $user
     * @return string
     */
    private function generateDonorShareText($user)
    {
        $commonText = $this->translator->trans('fmt.social_network.donor_share_text');
        $statistic = $user->getStatistic();

        $statisticsTransParameters = [
            '%students_founded%' => $statistic->getStudentsFounded(),
            '%books_purchased%' => $statistic->getBooksPurchasedFor(),
            '%amount_founded%' => CurrencyHelper::priceFilter($statistic->getAmountFounded()),
        ];

        $statisticText = $this->translator->trans(
            'fmt.social_network.donor_share_statistics',
            $statisticsTransParameters
        );

        return sprintf("%s\n%s", $commonText, $statisticText);
    }

    /**
     * @param $parameters
     * @param $type
     * @return string
     */
    public function generateShareLink($parameters, $type)
    {
        $link = self::SHARE_LINKS[$type];
        $linkParameter = array_map(function ($key, $value) {
            return sprintf('%s=%s', $key, urlencode($value));
        }, array_keys($parameters), array_values($parameters));

        $linkParameter = implode('&', $linkParameter);

        return sprintf('%s%s', $link, $linkParameter);
    }

    /**
     * @param User|null $user
     * @return string
     */
    public function generateShareParamVal($user)
    {
        $currentPageShareVal = $this->requestStack->getCurrentRequest()->get(self::SHARE_PARAM_NAME);
        $isSelfSharing = $currentPageShareVal === self::SHARE_PARAM['self'];

        return $this->isCurrentUser($user) || $isSelfSharing ?
            self::SHARE_PARAM['self'] :
            self::SHARE_PARAM['guest'];
    }

    /**
     * @param User $user
     * @return int
     */
    public function getRegistrationsAmount(User $user)
    {
        return $this->userRepository->getRegistrationsAmountOfUser($user);
    }

    /**
     * @param User $user
     * @return string|null
     */
    public function getTempAvatarFilename(User $user)
    {
        return $this->fileManager->getTempAvatarFileName($user);
    }
}
