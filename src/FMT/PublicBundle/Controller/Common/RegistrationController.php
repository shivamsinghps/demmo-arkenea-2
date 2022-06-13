<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\FormType\Security\RegistrationDonorType;
use FMT\PublicBundle\FormType\Security\RegistrationStudentType;
use FMT\PublicBundle\FormType\Security\UserPasswordType;
use FMT\PublicBundle\Traits\ControllerHelperTrait;
use FMT\PublicBundle\Twig\FlashExtension;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Controller\RegistrationController as FOSBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class RegistrationController
 *
 * @var Response
 * @var Route
 * @var Template
 * @var Security
 * @var Method
 * @var ParamConverter
 *
 * @package FMT\PublicBundle\Controller
 * @Route("/")
 * @Template()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RegistrationController extends FOSBaseController
{
    use ControllerHelperTrait;

    const ROUTE_DONOR_REGISTER = "fmt-public-donor-registration";
    const ROUTE_STUDENT_REGISTER = "fmt-public-student-registration";
    const ROUTE_CONFIRM = "fmt-public-registration-confirm";
    const ROUTE_REGISTRATION_COMPLETED = "fmt-public-registration-completed";

    /** @var UserManagerInterface */
    private $manager;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

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
     * @required
     *
     * @param FlashBagInterface $flashBag
     */
    public function setFlashBag(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/register-donor", name=RegistrationController::ROUTE_DONOR_REGISTER)
     * @Method({"POST"})
     * @Security("not has_role('ROLE_USER')")
     */
    public function donorRegistrationAction(Request $request)
    {
        $user = $this->manager->makeDonor();
        $form = $this->createForm(RegistrationDonorType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $this->manager->create($user);
            $response = $this->prepareJsonResponse(
                ['donorForm' => $form->createView()],
                '@Public/common/registration/_donor_form.html.twig',
                true,
                $this->getRegistrationCompletedUrl($request)
            );
        } else {
            $response = $this->prepareJsonResponse(
                ['donorForm' => $form->createView()],
                '@Public/common/registration/_donor_form.html.twig'
            );
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/register-student", name=RegistrationController::ROUTE_STUDENT_REGISTER)
     * @Method({"POST"})
     * @Security("not has_role('ROLE_USER')")
     */
    public function studentRegistrationAction(Request $request)
    {
        $user = $this->manager->makeStudent();
        $form = $this->createForm(RegistrationStudentType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $this->manager->create($user);
            $response = $this->prepareJsonResponse(
                ['studentForm' => $form->createView()],
                '@Public/common/registration/_student_form.html.twig',
                true,
                $this->getRegistrationCompletedUrl($request)
            );
        } else {
            $response = $this->prepareJsonResponse(
                ['studentForm' => $form->createView()],
                '@Public/common/registration/_student_form.html.twig'
            );
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/registration-completed", name=RegistrationController::ROUTE_REGISTRATION_COMPLETED)
     */
    public function checkEmailAction(Request $request)
    {
        return parent::checkEmailAction($request);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array|Response
     * @throws \Exception
     * @Route("/confirm/{token}", name=RegistrationController::ROUTE_CONFIRM)
     * @ParamConverter("user", class="DataBundle:User", options={
     *     "repository_method" = "findUserByConfirmationToken",
     *     "mapping": {"token": "token"},
     *     "map_method_signature" = true
     * })
     */
    public function confirmAction(Request $request, $user)
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->manager->confirm($user);
            $this->flashBag->add(FlashExtension::SUCCESS_TYPE, 'fmt.registration.confirmation.success_message');
            
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main',serialize($token));
            
            $response = $this->redirectToRoute(PublicDashboardController::ROUTE_INDEX);
        } else {
            $response = [
                'form' => $form->createView(),
                'email' => $user->getEmail(),
            ];
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return string|null
     */
    private function getRegistrationCompletedUrl(Request $request)
    {
        $referer = $request->headers->get('referer');
        $marketingPageUrl = $this->generateUrl(MarketingPortalController::MARKETING_IFRAME);
        if (substr_count($referer, $marketingPageUrl)) {
            $fosRegisteredEmailKey = 'fos_user_send_confirmation_email/email';
            $message = $this->get('translator')->trans('fmt.registration.check_email', [
                '%email%' => $request->getSession()->get($fosRegisteredEmailKey)
            ]);
            $request->getSession()->remove($fosRegisteredEmailKey);
            $request->getSession()->getFlashBag()->clear();
            $request->getSession()->getFlashBag()->add("success", $message);
            return null;
        } else {
            return $this->generateUrl(self::ROUTE_REGISTRATION_COMPLETED);
        }
    }
}
