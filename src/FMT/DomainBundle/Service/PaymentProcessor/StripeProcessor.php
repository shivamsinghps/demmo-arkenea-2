<?php
/**
 * Author: Anton Orlov
 * Date: 04.05.2018
 * Time: 10:45
 */

namespace FMT\DomainBundle\Service\PaymentProcessor;

use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Exception\PaymentException;
use FMT\DomainBundle\Type\Payment\Settings;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FMT\InfrastructureBundle\Service\Payments\Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;

class StripeProcessor implements ProcessorInterface
{
    /** @var StripeClient */
    private $client;

    /** @var Settings */
    private $settings;

    /** @var string */
    private $token;

    public function __construct(StripeClient $client, Settings $settings)
    {
        $this->client = $client;
        $this->settings = $settings;
    }

    /**
     * Method checks if current processor supports provided descriptor
     *
     * @param string $descriptor
     * @return bool
     */
    public function isSupport(string $descriptor)
    {
        $result = false;
        if (substr($descriptor, 0, 1) === "{" || substr($descriptor, -1) === "}") {
            $data = json_decode($descriptor, true);
            if (is_array($data) && isset($data["token"]) && isset($data["token"]["id"])) {
                $this->token = $data["token"]["id"];
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Method charges client using implementation of corresponding payment processor
     *
     * @param UserTransaction $transaction
     * @return UserTransaction
     * @throws PaymentException
     */
    public function charge(UserTransaction $transaction)
    {
        $charge = $transaction->getSpendTotal();

        $metadata = [
            "txn_id" => $transaction->getId(),
            "source" => $transaction->getSender()->getEmail(),
            "target" => $transaction->getRecipient()->getEmail(),
            "wire" => $transaction->getNet(),
            "fee" => $transaction->getFee(),
            "calculatedStripeFee" => $transaction->getPaymentSystemFee()
        ];



        $description = sprintf(
            "FMT transaction #%d from %s to %s",
            $metadata["txn_id"],
            $metadata["source"],
            $metadata["target"]
        );

        LogHelper::info("%s: %d cents", $description, $transaction->getAmount());

        try {
            $result = $this->client->createCharge([
                "source" => $this->token,
                "amount" => $charge,
                "currency" => $this->settings->currency,
                "capture" => true,
                "description" => $description,
                "metadata" => $metadata
            ]);
        } catch (CardException $exception) {
            throw new PaymentException($exception->getMessage());
        } catch (InvalidRequestException $exception) {
            throw new PaymentException($exception->getMessage());
        }

        if ($this->settings->live && !$result->livemode) {
            LogHelper::debug($result);
            throw new \RuntimeException(
                "It looks like Stripe works in test mode, but the application requires live mode"
            );
        }

        if ($result->status !== "succeeded") {
            LogHelper::debug($result);
            throw new \RuntimeException(sprintf("Unexpected status of the charge: %s", $result->status));
        }

        LogHelper::debug($result->outcome);

        if ($result->amount !== $charge || $result->currency !== $this->settings->currency) {
            LogHelper::debug($result);
            throw new \RuntimeException(sprintf(
                "Created (C)harge is not equal (O)riginal transaction (C vs O: %d %s vs %d %s)",
                $result->amount,
                $result->currency,
                $charge,
                $this->settings->currency
            ));
        }

        $transaction->setExternalId($result->balance_transaction);

        return $transaction;
    }

    /**
     * @param string $txnId
     * @return \Stripe\Charge|null
     * @throws ApiErrorException
     */
    public function getCharge(string $txnId)
    {
        $balanceTransaction = $this->client->getBalanceTransaction($txnId);
        if ($balanceTransaction) {
            return $this->client->getCharge($balanceTransaction->source);
        }

        return null;
    }
}
