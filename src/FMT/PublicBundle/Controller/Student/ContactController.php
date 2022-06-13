<?php

namespace FMT\PublicBundle\Controller\Student;

use Exception;
use FMT\DataBundle\Entity\UserContact;
use FMT\DomainBundle\Service\UserContactManagerInterface;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\Contact\CampaignInvitationType;
use FMT\PublicBundle\FormType\UserContactType;
use FMT\PublicBundle\Traits\ControllerHelperTrait;
use FMT\PublicBundle\Voter\UserContactVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContactController
 * @package FMT\PublicBundle\Controller\Student
 *
 * @Route("/student/contacts")
 * @Template()
 * @Security("has_role('ROLE_STUDENT')")
 */
class ContactController extends AbstractBaseController
{
    use ControllerHelperTrait;

    const ROUTE_INDEX = 'fmt-student-contacts-index';
    const ROUTE_CREATE = 'fmt-student-contacts-create';
    const ROUTE_INVITE_TO_CAMPAIGN = 'fmt-student-contacts-campaign-invite';
    const ROUTE_REMOVE = 'fmt-student-contacts-remove';
    const ROUTE_INVITES_ALL = 'fmt-student-contacts-campaign-invite-all';

    const DEFAULT_CONTACT_PAGINATION_LIMIT = 20;

    /** @var UserManagerInterface */
    private $userManager;

    /** @var UserContactManagerInterface */
    private $contactManager;

    /**
     * ContactController constructor.
     * @param UserManagerInterface $userManager
     * @param UserContactManagerInterface $userContactManager
     */
    public function __construct(UserManagerInterface $userManager, UserContactManagerInterface $userContactManager)
    {
        $this->userManager = $userManager;
        $this->contactManager = $userContactManager;
    }

    /**
     * @Route("/{page}",
     *     name=ContactController::ROUTE_INDEX,
     *     defaults={
     *      "page" = 1
     *     },
     *     requirements={"page"="\d+"}
     * )
     * @param $page
     * @return array
     */
    public function indexAction($page)
    {
        $form = $this->createForm(UserContactType::class);
        $user = $this->getUser();

        return [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'list' => $this->paginate(
                $user->getContacts(),
                $page,
                self::DEFAULT_CONTACT_PAGINATION_LIMIT
            ),
        ];
    }

    /**
     * @Route("/create", methods={"POST"}, name=ContactController::ROUTE_CREATE)
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        $student = $this->getUser();

        $donor = $this->userManager->makeDonor();
        $form = $this->createForm(UserContactType::class, $donor, ['student' => $student]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->userManager->addContact($student, $donor, false);

            $this->clearFlashBag('success');
            $this->addFlashBagNotice('fmt.contacts.success_create');
            return new JsonResponse(['success' => true]);
        }

        return $this->createValidationErrorsResponse($form);
    }

    /**
     * @Route("/{id}/invite-to-campaign", methods={"POST"}, name=ContactController::ROUTE_INVITE_TO_CAMPAIGN)
     * @ParamConverter("product", class="DataBundle:UserContact")
     * @param Request $request
     * @param UserContact $contact
     * @return JsonResponse
     */
    public function inviteToCampaignAction(Request $request, UserContact $contact)
    {
        $student = $this->getUser();

        $form = $this->createForm(CampaignInvitationType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $this->contactManager->inviteContactToCurrentCampaign(
                    $contact,
                    $student,
                    $form->get('personalNote')->getData()
                );

                $this->clearFlashBag('success');
                $this->addFlashBagNotice('fmt.contacts.success_invited');

                return new JsonResponse(['success' => true]);
            } catch (Exception $exception) {
                return $this->createFailureAjaxResponse(['message' => $exception->getMessage()]);
            }
        }

        return $this->createValidationErrorsResponse($form);
    }

    /**
     * @Route("/invite-all-to-campaign", name=ContactController::ROUTE_INVITES_ALL)
     * @param Request $request
     * @return RedirectResponse
     */
    public function inviteAllContactsToCampaignAction(Request $request) {
        $student = $this->getUser();
        try {
            $this->contactManager->inviteAllContactsToCurrentCampaign($student);

            $this->clearFlashBag('success');
            $this->addFlashBagNotice('fmt.contacts.success_invited_all');

            return $this->redirectToRoute(self::ROUTE_INDEX);
        } catch (Exception $exception) {
            $this->addFlashBagError($exception->getMessage());
            return $this->redirectToRoute(self::ROUTE_INDEX);
        }
    }

    /**
     * @Route("/{id}/remove", methods={"POST"}, name=ContactController::ROUTE_REMOVE)
     * @ParamConverter("product", class="DataBundle:UserContact")
     * @param UserContact $contact
     * @return JsonResponse
     */
    public function removeAction(UserContact $contact)
    {
        $this->denyAccessUnlessGranted(UserContactVoter::CAN_DELETE, $contact);

        try {
            $this->contactManager->removeContact($contact);

            $this->clearFlashBag('success');
            $this->addFlashBagNotice('fmt.contacts.success_delete');
        } catch (Exception $e) {
            return $this->createFailureAjaxResponse(['message' => $this->translate('fmt.user.errors.default')]);
        }

        return new JsonResponse(['success' => true]);
    }
}
