<?php

namespace FMT\PublicBundle\Service\Security;

use FMT\DomainBundle\Event\UserEvent;
use FMT\PublicBundle\Controller\Common\MarketingPortalController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FMT\DataBundle\Entity\User;
use FMT\PublicBundle\Controller\Donor\DashboardController as DonorDashboardController;
use FMT\PublicBundle\Controller\Student\DashboardController as StudentDashboardController;
use FMT\PublicBundle\Controller\Student\ProfileController as StudentProfileController;
use FMT\PublicBundle\Controller\Donor\ProfileController as DonorProfileController;
use FMT\PublicBundle\Controller\Common\PublicDashboardController;

/**
 * Class AuthenticationHandler
 * @package FMT\PublicBundle\Service\Security
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthenticationHandler implements
    AuthenticationSuccessHandlerInterface,
    AuthenticationFailureHandlerInterface,
    LogoutHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $marketingAppUrl;
    private $flashBag;
    /**
     * AuthenticationHandler constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param UrlGeneratorInterface $urlGenerator
     * @param TranslatorInterface $translator
     * @param RequestStack $requestStack
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        FlashBagInterface $flashBag
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->flashBag = $flashBag;
    }

    /**
     * @param string $marketingAppUrl
     */
    public function setMarketingAppUrl(string $marketingAppUrl)
    {
        $this->marketingAppUrl = $marketingAppUrl;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse([
                'success' => true,
                'location' => $this->generateSuccessRedirectHref($token->getUser()),
            ]);

            $this->eventDispatcher->dispatch(
                UserEvent::LOGIN_SUCCESS,
                new UserEvent($token->getUser())
            );

            if ($this->isUserStudent($token->getUser())) {
                $message = "FMT is a book funding platform and is not part of your bookstore. Please send any ";
                $message .= "questions via the support page of this site.";
                $this->flashBag->add('warning', $message);
            }

            return $response;
        }
    }

    /**
     * @param User $user
     * @return string
     */
    private function generateSuccessRedirectHref(User $user): string
    {
        $roleRouteMap = [
            User::ROLE_DONOR              => DonorDashboardController::ROUTE_INDEX,
            User::ROLE_STUDENT            => StudentDashboardController::ROUTE_INDEX,
            User::ROLE_INCOMPLETE_DONOR   => DonorProfileController::ROUTE_INDEX,
            User::ROLE_INCOMPLETE_STUDENT => StudentProfileController::ROUTE_INDEX,
        ];

        $roles = $user->getRoles();

        foreach ($roleRouteMap as $role => $routeName) {
            if (in_array($role, $roles)) {
                $resultRouteName = $routeName;
                break;
            }
        }

        if (empty($resultRouteName)) {
            $resultRouteName = PublicDashboardController::ROUTE_INDEX;
        }

        return $this->urlGenerator->generate($resultRouteName, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        echo "<pre>"; print_r($request); exit;

        if ($request->isXmlHttpRequest()) {
            $this->eventDispatcher->dispatch(
                UserEvent::LOGIN_FAILED,
                new UserEvent()
            );

            if ($exception->getPrevious() instanceof UsernameNotFoundException) {
                $trans = 'fmt.authentication.error.email';
            } else {
                $trans = 'fmt.authentication.error.password';
            }

            $response = new JsonResponse([
                'success' => false,
                'message' => $this->translator->trans($trans),
            ]);

            return $response;
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        if ($response instanceof RedirectResponse) {
            $response->setTargetUrl($this->marketingAppUrl);
        }
    }

    private function isUserStudent(User $user)
    {
        $userRolesMap = [
            User::ROLE_INCOMPLETE_STUDENT,
            User::ROLE_STUDENT
        ];

        $roles = $user->getRoles();
        foreach ($userRolesMap as $role) {
            if (in_array($role, $roles)) {
                return true;
            }
        }
    }
}
