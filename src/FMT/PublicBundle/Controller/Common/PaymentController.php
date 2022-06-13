<?php
/**
 * Author: Anton Orlov
 * Date: 06.04.2018
 * Time: 17:49
 */

namespace FMT\PublicBundle\Controller\Common;

use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Exception\InvalidDonationException;
use FMT\DomainBundle\Exception\PaymentException;
use FMT\DomainBundle\Service\PaymentManagerInterface;
use FMT\DomainBundle\Type\Payment\Donation;
use FMT\DomainBundle\Type\Payment\Settings;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\PaymentType;
use FMT\PublicBundle\Voter\TransactionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @var Route
 * @var Template
 * @var ParamConverter
 *
 * Class CheckoutController
 * @package FMT\PublicBundle\Controller\Common
 * @Route("/payment")
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @Template()
 */
class PaymentController extends AbstractBaseController
{
    const TRANSACTION_SESSION_KEY = "thank_you_txn_id";

    const ROUTE_DONATE = "fmt-donate";
    const ROUTE_DONATE_THANK_YOU = "fmt-donate-thank-you";
    const ROUTE_NOT_COMPLETED_DONOR = "fmt-donor-profile-index";
    const ROUTE_NOT_COMPLETED_STUDENT = "fmt-student-profile-index";

    /** @var string */
    private $token;

    /** @var PaymentManagerInterface */
    private $manager;

    public function __construct(Settings $settings)
    {
        $this->token = $settings->publicKey;
    }

    /**
     * @param PaymentManagerInterface $manager
     * @required
     */
    public function setPaymentManager(PaymentManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @param User $student
     * @return array|RedirectResponse
     * @Route("/donate/{id}", name=PaymentController::ROUTE_DONATE)
     * @ParamConverter("student", class="DataBundle:User", options={"active_campaign" = true})
     * @throws \Exception
     */
    public function donateAction(Request $request, User $student)
    {
        if ($user = $this->getUser()) {
            if ($user->isIncompleteDonor()) {
                return $this->redirectToRoute(self::ROUTE_NOT_COMPLETED_DONOR);
            } elseif ($user->isIncompleteStudent()) {
                return $this->redirectToRoute(self::ROUTE_NOT_COMPLETED_STUDENT);
            }
        }
        $donation = new Donation($student);
        $donation->setDonor($user);
        $attributes = [
            "action" => $this->generateUrl(self::ROUTE_DONATE, ["id" => $student->getId()]),
            "attr" => [
                "data-checkout" => false,
                "data-token" => $this->token
            ]
        ];
        $form = $this->createForm(PaymentType::class, $donation, $attributes);
        $form->handleRequest($request);
        $response = [
            'form' => $form->createView(),
            'student' => $student,
        ];

        if ($form->isValid()) {
            try {
                $transaction = $this->manager->sendDonation($donation);
                $this->setSessionVariable(self::TRANSACTION_SESSION_KEY, $transaction->getId());
            } catch (PaymentException | InvalidDonationException $exception) {
                $this->addFlashBagError($exception->getMessage());
                return $response;
            }

            return $this->redirectToRoute(self::ROUTE_DONATE_THANK_YOU);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/thank-you-for-donation", name=PaymentController::ROUTE_DONATE_THANK_YOU)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function donateThankYouAction(Request $request)
    {
        if (!$this->hasSessionVariable(self::TRANSACTION_SESSION_KEY)) {
            throw new NotFoundHttpException("Transaction is not defined for this page");
        }

        $transaction = $this->manager->getTransaction($this->getSessionVariable(self::TRANSACTION_SESSION_KEY));

        $this->denyAccessUnlessGranted(TransactionVoter::CAN_VIEW_TRANSACTION, $transaction);

        return [
            "student" => $transaction->getRecipient()->getProfile()->getFirstName()
        ];
    }
}
