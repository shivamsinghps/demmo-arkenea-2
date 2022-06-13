<?php

namespace FMT\PublicBundle\Controller\Student;

use Exception;
use FMT\DataBundle\Entity\Campaign;
use FMT\DomainBundle\Repository\UserTransactionRepositoryInterface;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\Campaign\CampaignType;
use FMT\PublicBundle\FormType\Transaction\ThanksType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CampaignController
 *
 * @var Response
 * @var Route
 * @var Template
 * @var Security
 * @var Method
 * @var ParamConverter
 *
 * @package FMT\PublicBundle\Controller
 * @Route("/student/campaign")
 * @Template()
 * @Security("has_role('ROLE_STUDENT')")
 */
class CampaignController extends AbstractBaseController
{
    const ROUTE_ADD = "fmt-student-campaign-add";
    const ROUTE_EDIT = "fmt-student-campaign-edit";
    const ROUTE_TOGGLE_PAUSE_STATUS = "fmt-student-campaign-toggle-pause";
    const ROUTE_RECALCULATE = "fmt-student-campaign-recalculate";

    /** @var CampaignManagerInterface */
    protected $manager;

    /**
     * @required
     * @param CampaignManagerInterface $manager
     * @return $this
     */
    public function setManager(CampaignManagerInterface $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @param Request $request
     * @return array|RedirectResponse
     * @Route("/add", defaults={"id" = null}, name=CampaignController::ROUTE_ADD)
     * @Security("is_granted('canAddCampaign', user)")
     * @Template("PublicBundle:student/campaign:add_edit.html.twig")
     * @throws Exception
     */
    public function addAction(Request $request)
    {
        $campaign = $this->manager->prepareNew($this->getUser());

        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->manager->create($campaign);

            $this->addFlashBagNotice('fmt.campaign.textbooks.form.result.success');

            return $this->redirectToRoute(self::ROUTE_EDIT, ['id' => $campaign->getId()]);
        }

        return [
            'campaign' => $campaign,
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param Campaign $campaign
     * @param UserTransactionRepositoryInterface $userTransactionRepository
     * @param int $thanksTransactionId
     * @return array|RedirectResponse
     * @throws Exception
     * @Route("/{id}/edit", requirements={"id":"\d+"}, name=CampaignController::ROUTE_EDIT)
     * @ParamConverter("campaign", class="DataBundle:Campaign")
     * @Security("is_granted('canEdit', campaign)")
     * @Template("PublicBundle:student/campaign:add_edit.html.twig")
     */
    public function editAction(
        Request $request,
        Campaign $campaign,
        UserTransactionRepositoryInterface $userTransactionRepository,
        int $thanksTransactionId
    ) {
        if (!$campaign) {
            throw $this->createNotFoundException();
        }
        $campaignForm = $this->createForm(CampaignType::class, $campaign);
        $thanksTransaction = $thanksTransactionId ?
            $userTransactionRepository->findById($thanksTransactionId) :
            null;
        $thanksForm = $this->createForm(ThanksType::class, $thanksTransaction);
        if ($request->request->has($campaignForm->getName())) {
            $campaignForm->handleRequest($request);

            if ($campaignForm->isValid()) {
                $this->manager->update($campaign);

                $this->addFlashBagNotice('fmt.campaign.textbooks.form.result.success');

                return $this->redirectToRoute(self::ROUTE_EDIT, ['id' => $campaign->getId()]);
            }
        } elseif ($request->request->has($thanksForm->getName())) {
            $thanksForm->handleRequest($request);
            if ($thanksForm->isSubmitted() && $thanksForm->isValid()) {
                $userTransactionRepository->save($thanksTransaction);
            } else {
                $userTransactionRepository->getEm()->refresh($thanksTransaction);
            }
        }

        return [
            'campaign' => $campaign,
            'campaignTransactions' => $userTransactionRepository->getTransactionsOfCampaignWithSender($campaign),
            'form'     => $campaignForm->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param Campaign $campaign
     * @return JsonResponse
     * @Route("/toggle-pause-status/{id}",
     *     requirements={"id":"\d+"},
     *     name=CampaignController::ROUTE_TOGGLE_PAUSE_STATUS
     * )
     * @ParamConverter("campaign", class="DataBundle:Campaign")
     * @Security("is_granted('canEdit', campaign)")
     * @Method("POST")
     */
    public function pauseStatusUpdateAction(Request $request, Campaign $campaign)
    {
        $this->checkAjaxRequest($request);

        $this->manager->togglePauseStatus($campaign);

        if (!$campaign->isPaused()) {
            $this->addFlashBagNotice('fmt.campaign.status.restarted');
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @Route(
     *     "/{id}/recalculate",
     *      requirements={"id":"\d+"},
     *      name=CampaignController::ROUTE_RECALCULATE)
     * @Method("Post")
     */
    public function recalculateAction(Request $request, int $id)
    {
        $campaign = $this->manager->findOrCreate($id);

        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        $this->manager->updateTotals($campaign);

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form, $this->normalizeObjectForAjax($campaign));
        }

        return $this->createSuccessAjaxResponse($this->normalizeObjectForAjax($campaign));
    }
}
