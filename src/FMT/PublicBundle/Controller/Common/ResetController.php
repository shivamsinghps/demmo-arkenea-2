<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\FormType\Security\UserPasswordType;
use FMT\PublicBundle\Traits\ControllerHelperTrait;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\ResettingController as FOSBaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ResetController
 * @package FMT\PublicBundle\Controller
 * @Route("/resetting")
 * @Template()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ResetController extends FOSBaseController
{
    use ControllerHelperTrait;

    const ROUTE_RESET = 'fmt-reset-reset';
    const ROUTE_SEND_EMAIL = 'fmt-reset-send-email';
    const ROUTE_CHECK_EMAIL = 'fmt-reset-check-email';
    const ROUTE_REQUEST = 'fmt-reset-request';

    /**
     * @var EventDispatcherInterface $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @required
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /** @var UserManagerInterface */
    private $manager;

    /**
     * @required
     *
     * @param UserManagerInterface $manager
     */
    public function setUserManager(UserManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/request", name=ResetController::ROUTE_REQUEST)
     */
    public function requestAction()
    {
        return $this->render('@FOSUser/Resetting/request.html.twig');
    }

    /**
     * @param Request $request
     * @param $token
     * @param User $user
     * @return null|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/reset/{token}", name=ResetController::ROUTE_RESET)
     * @ParamConverter("user", class="DataBundle:User", options={
     *     "repository_method" = "findUserByConfirmationToken",
     *     "mapping": {"token": "token"},
     *     "map_method_signature" = true
     * })
     */
    public function resetPasswordAction(Request $request, $token, User $user)
    {
        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->update($user, true);

            if (null === $response = $event->getResponse()) {
                //TODO change this route if needed
                $url = $this->generateUrl(PublicDashboardController::ROUTE_INDEX);
                $response = new RedirectResponse($url);
            }

            $this->eventDispatcher->dispatch(
                FOSUserEvents::RESETTING_RESET_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $response;
        }

        return $this->render('@FOSUser/Resetting/reset.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
            'email' => $user->getEmail(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/send-email", name=ResetController::ROUTE_SEND_EMAIL)
     */
    public function sendEmailAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedHttpException('Incorrect request');
        }

        $email = $request->get('username');
        $user = $this->manager->getUserByEmail($email);

        if (!$user instanceof User) {
            return $this->prepareJsonResponse(
                [
                    'formError' => true,
                    'email' => $email,
                ],
                '@Public/common/login/_reset_form_input.htm.twig',
                false
            );
        }

        /**
         * @var $redirect RedirectResponse
         */
        $redirect = parent::sendEmailAction($request);
        $redirect = $redirect->getTargetUrl();
        $flashBag = $this->get('session.flash_bag');
        $flashBag->add('success', 'fmt.registration.reset.success_reset');

        return $this->prepareJsonResponse(
            ['email' => $email],
            '@Public/common/login/_reset_form_input.htm.twig',
            true,
            $redirect
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/check-email", name=ResetController::ROUTE_CHECK_EMAIL)
     * @Security("not has_role('ROLE_USER')")
     */
    public function checkEmailAction(Request $request)
    {
        return parent::checkEmailAction($request);
    }
}
