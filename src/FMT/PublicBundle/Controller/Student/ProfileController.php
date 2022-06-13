<?php

namespace FMT\PublicBundle\Controller\Student;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserAvatar;
use FMT\DomainBundle\Service\FileManagerInterface;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\StudentProfileType;
use FMT\PublicBundle\Voter\UserVoter;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProfileController
 * @package FMT\PublicBundle\Controller\Student
 * @Route("/student/profile")
 * @Template()
 * @Security("has_role('ROLE_STUDENT') or has_role('ROLE_INCOMPLETE_STUDENT')")
 */
class ProfileController extends AbstractBaseController
{
    const ROUTE_INDEX = 'fmt-student-profile-index';
    const ROUTE_DISABLE = 'fmt-student-profile-disable';

    /** @var FileManagerInterface */
    private $fileManager;

    /** @var UserManagerInterface */
    private $userManager;

    /**
     * @required
     * @param FileManagerInterface $fileManager
     */
    public function setFileManager(FileManagerInterface $fileManager)
    {
        $this->fileManager = $fileManager;
    }

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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function indexAction(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $avatar = $user->getProfile()->getAvatar();
        $oldAvatarName = $avatar instanceof UserAvatar ? $avatar->getFilename() : null;
        $form = $this->createForm(StudentProfileType::class, $user, [
            'oldAvatarName' => $oldAvatarName,
        ]);
        $clientFile = $request->files->get('student_profile')['profile']['avatar']['filename'] ?? null;

        if ($request->getMethod() === Request::METHOD_POST) {
            $isUsePresavedAvatar = empty($clientFile) && $clientFile = $this->fileManager->getTempAvatar($user);
            if ($isUsePresavedAvatar) {
                $request->files->set('student_profile', ['profile' => ['avatar' => ['filename' => $clientFile]]]);
            }
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->userManager->completeUser($user);
            $user->setRoles([User::ROLE_STUDENT]);
            $this->fileManager->uploadAvatar($user, $clientFile, $oldAvatarName);
            $this->userManager->update($user);
            $this->addFlashBagNotice('fmt.user.profile.profile_update_success');

            return $this->redirectToRoute(self::ROUTE_INDEX);
        } elseif ($form->isSubmitted() && !empty($clientFile) && empty($isUsePresavedAvatar)) {
            $this->fileManager->uploadTempAvatar($user, $clientFile);
        }

        if ($oldAvatarName) {
            $avatar->setFilename($oldAvatarName);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @return JsonResponse
     * @Route("/disable",
     *     methods={"POST"},
     *     name=ProfileController::ROUTE_DISABLE,
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function disableAction()
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if ($this->isGranted(UserVoter::CAN_DELETE_ACCOUNT, $user)) {
            $this->userManager->disableAccount($this->getUser());

            return $this->createSuccessAjaxResponse([
                'message' => $this->translate('fmt.user.disable_profile.success'),
                'redirect' => $this->generateUrl('fos_user_security_logout'),
            ]);
        }

        return $this->createFailureAjaxResponse([
            'message' => $this->translate('fmt.user.disable_profile.cant_delete')
        ]);
    }
}
