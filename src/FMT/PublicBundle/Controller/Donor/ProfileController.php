<?php

namespace FMT\PublicBundle\Controller\Donor;

use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\DonorProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProfileController
 * @package FMT\PublicBundle\Controller\Donor
 * @Route("/donor/profile")
 * @Template()
 * @Security("has_role('ROLE_DONOR') or has_role('ROLE_INCOMPLETE_DONOR')")
 */
class ProfileController extends AbstractBaseController
{
    const ROUTE_INDEX = 'fmt-donor-profile-index';
    const ROUTE_DISABLE = 'fmt-donor-profile-disable';

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param UserManagerInterface $manager
     * @required
     */
    public function setUserManager(UserManagerInterface $manager)
    {
        $this->userManager = $manager;
    }

    /**
     * @param Request $request
     * @return null
     * @Route("/", name=ProfileController::ROUTE_INDEX)
     */
    public function indexAction(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $form = $this->createForm(DonorProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->userManager->completeUser($user);
            $this->userManager->update($user);
            $this->addFlashBagNotice('fmt.user.profile.profile_update_success');

            return $this->redirectToRoute(self::ROUTE_INDEX);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/disable",
     *     name=ProfileController::ROUTE_DISABLE,
     *     methods={"POST"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function disableAction()
    {
        $this->userManager->disableAccount($this->getUser());

        return $this->createSuccessAjaxResponse([
            'message' => $this->translate('fmt.user.disable_profile.success'),
            'redirect' => $this->generateUrl('fos_user_security_logout'),
        ]);
    }
}
