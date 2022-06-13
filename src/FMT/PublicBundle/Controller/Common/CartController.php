<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\DataBundle\Entity\CampaignBook;
use FMT\DomainBundle\Service\CartManagerInterface;
use FMT\DomainBundle\Service\PaymentManagerInterface;
use FMT\DomainBundle\Type\Payment\Settings;
use FMT\DomainBundle\Type\Payment\Donation;
use FMT\DomainBundle\Exception\CartActionException;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\FormType\PaymentType;
use FMT\PublicBundle\Voter\TransactionVoter;
use FMT\PublicBundle\Traits\ControllerHelperTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CartController
 * @package FMT\PublicBundle\Controller\Common
 * @Route("/cart")
 * @Template()
 */
class CartController extends AbstractBaseController
{
    use ControllerHelperTrait;

    const TRANSACTION_SESSION_KEY = "thank_you_txn_id";

    const ROUTE_ADD = 'fmt-cart-add';
    const ROUTE_REMOVE = 'fmt-cart-remove';
    const ROUTE_INDEX = 'fmt-cart-index';
    const ROUTE_CHECKOUT_THANK_YOU = "fmt-checkout-thank-you";

    /** @var string */
    private $token;

    /** @var PaymentManagerInterface */
    private $manager;

    /**
     * CartController constructor.
     * @param Settings $settings
     */
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
     * @param CampaignBook $product
     * @param CartManagerInterface $cartManager
     *
     * @ParamConverter("product", class="DataBundle:CampaignBook")
     * @Method({"POST"})
     * @Route("/add/{product}",
     *     name=CartController::ROUTE_ADD,
     *     requirements={"product"="\d+"},
     *     options={"expose"=true},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @return JsonResponse
     */
    public function addAction(CampaignBook $product, CartManagerInterface $cartManager)
    {
        if ($cartManager->hasProduct($product)) {
            $this->addFlashBagError('fmt.cart.product.add.duplicate_item');

            return $this->createFailureAjaxResponse();
        }

        if (!$cartManager->canAddProduct($product)) {
            $this->addFlashBagError('fmt.cart.product.add.can_not_add_item');

            return $this->createFailureAjaxResponse();
        }

        $cartManager->addProduct($product);
        $cartManager->save();
        $this->addFlashBagNotice('fmt.cart.product.add.success');

        return $this->createSuccessAjaxResponse([
            'summary' => $cartManager->getSummary()->getFormattedArray()
        ]);
    }

    /**
     * @param CampaignBook $product
     * @param CartManagerInterface $cartManager
     *
     * @ParamConverter("product", class="DataBundle:CampaignBook")
     * @Route("/remove/{product}",
     *     name=CartController::ROUTE_REMOVE,
     *     requirements={"product"="\d+"},
     *     options={"expose"=true},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @return JsonResponse
     */
    public function removeAction(CampaignBook $product, CartManagerInterface $cartManager)
    {
        if (!$cartManager->hasProduct($product)) {
            $this->addFlashBagError('fmt.cart.product.remove.absent_item');

            return $this->createFailureAjaxResponse();
        }

        $cartManager->removeProduct($product);
        $cartManager->save();
        $this->addFlashBagNotice('fmt.cart.product.remove.success');

        return $this->createSuccessAjaxResponse([
            'summary' => $cartManager->getSummary()->getFormattedArray()
        ]);
    }

    /**
     * @param Request $request
     * @param CartManagerInterface $cartManager
     *
     * @Route("/", name=CartController::ROUTE_INDEX)
     * @return array|RedirectResponse
     */
    public function cartAction(Request $request, CartManagerInterface $cartManager)
    {
        if ($this->getUser()) {
            $this->denyAccessUnlessGranted(['ROLE_DONOR', 'ROLE_STUDENT', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        }

        $cart = $cartManager->get();
        $donation = new Donation($cart->getCampaign() ? $cart->getCampaign()->getUser() : $this->getUser());
        $donation->setDonor($this->getUser());

        $attributes = [
            "action" => $this->generateUrl(self::ROUTE_INDEX),
            "attr" => [
                "data-checkout" => true,
                "data-token" => $this->token
            ]
        ];
                
        $form = $this->createForm(PaymentType::class, $donation, $attributes);
        $form->handleRequest($request);
        $response = [
            "form" => $form->createView(),
            "cart" => $cart,
        ];

        if ($form->isValid()) {
            try {
                $orderInfo = $cartManager->sendDonationOrder($donation, $cart);
                $orderExternalId = $orderInfo["orderExternalId"];
                $transaction = $orderInfo["transaction"];

                if (!$orderExternalId) {
                    $this->addFlashBagError('fmt.cart.errors.send_order');
                    return $response;
                }

                if (!$transaction) {
                    $this->addFlashBagError('fmt.cart.errors.send_payment');
                    return $response;
                }

                $this->setSessionVariable(self::TRANSACTION_SESSION_KEY, $transaction->getId());
            } catch (CartActionException $exception) {
                $this->addFlashBagError($exception->getMessage());
                return $response;
            }

            return $this->redirectToRoute(self::ROUTE_CHECKOUT_THANK_YOU);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/thank-you-for-checkout", name=CartController::ROUTE_CHECKOUT_THANK_YOU)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function checkoutThankYouAction(Request $request, CartManagerInterface $cartManager)
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
