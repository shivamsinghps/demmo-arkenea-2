<?php

namespace FMT\PublicBundle\Controller\Common;

use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Service\PaymentProcessor\ProcessorInterface;
use FMT\DomainBundle\Service\Pdf\ReceiptDto;
use FMT\DomainBundle\Service\Pdf\ReceiptItemDto;
use FMT\DomainBundle\Service\ReceiptHelperInterface;
use FMT\InfrastructureBundle\Helper\CurrencyHelper;
use FMT\PublicBundle\Controller\AbstractBaseController;
use FMT\PublicBundle\Voter\TransactionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PdfController
 *
 * @var Response
 * @var Route
 * @var Template
 * @var Security
 * @var Method
 * @var ParamConverter
 *
 * @package FMT\PublicBundle\Controller
 * @Route("/pdf")
 * @Template()
 */
class PdfController extends AbstractBaseController
{
    const ROUTE_RECEIPT = "fmt-pdf-receipt";

    /** @var ProcessorInterface */
    private $service;

    /** @var ReceiptHelperInterface */
    private $receiptHelper;

    /**
     * @param ReceiptHelperInterface $receiptHelper
     * @required
     */
    public function setReceiptHelper(ReceiptHelperInterface $receiptHelper)
    {
        $this->receiptHelper = $receiptHelper;
    }

    /**
     * @param ProcessorInterface $service
     * @required
     */
    public function setPaymentProcessor(ProcessorInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param UserTransaction $transaction
     * @return Response
     * @throws ApiErrorException
     * @Route("/receipt/{id}", name=PdfController::ROUTE_RECEIPT)
     * @Security("has_role('ROLE_USER')")
     */
    public function receiptAction(UserTransaction $transaction)
    {
        $this->denyAccessUnlessGranted(TransactionVoter::CAN_VIEW_RECEIPT, $transaction);

        $charge = $this->service->getCharge($transaction->getExternalId());
        if (!$charge) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();

        $info = new ReceiptDto();
        $info->number = $charge->receipt_number;
        $info->date = $transaction->getDate();

        if ($user->isDonor()) {
            $info->paymentMethod = sprintf("%s - %s", $charge->source->brand, $charge->source->last4);
        }

        $info->amount = CurrencyHelper::priceFilter($transaction->getSpendTotal());


        $items = [];

        if ($transaction->getType() == UserTransaction::TXN_DONATION) {
            $item = new ReceiptItemDto();
            $from = $user->isDonor() ?
                sprintf("from %s ", $transaction->getSender()->getFullName()) :
                '';
            $to = $transaction->getRecipient()->getProfile()->getFullName();
            $description = sprintf("Donation %sto %s", $from, $to);
            $item->description = $description;
            $item->amount = CurrencyHelper::priceFilter($transaction->getSpendTotal());
            $item->qty = 1;
            $items[] = $item;
        } else {
            // TODO: update after purchase
            $item = new ReceiptItemDto();
            $from = $user->isDonor() ?
                sprintf("from %s ", $transaction->getSender()->getFullName()) :
                '';
            $to = $transaction->getRecipient()->getProfile()->getFullName();
            $description = sprintf("Buy book %sto %s", $from, $to);
            $item->description = $description;
            $item->amount = CurrencyHelper::priceFilter($transaction->getSpendTotal());
            $item->qty = 1;
            $items[] = $item;
        }

        $receipt = $this->receiptHelper->getReceipt($info, $items);

        return new Response($receipt, Response::HTTP_OK, ['Content-Type' => 'application/pdf']);
    }
}
