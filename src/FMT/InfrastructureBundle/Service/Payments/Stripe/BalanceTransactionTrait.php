<?php

namespace FMT\InfrastructureBundle\Service\Payments\Stripe;

use Stripe\BalanceTransaction;
use Stripe\Collection;
use Stripe\Exception\ApiErrorException;

/**
 * Trait BalanceTransactionTrait
 * @package FMT\InfrastructureBundle\Service\Payments\Stripe
 */
trait BalanceTransactionTrait
{
    /**
     * @see https://stripe.com/docs/api/balance_transactions/retrieve
     *
     * @param string $txnId
     * @return BalanceTransaction
     * @throws ApiErrorException
     */
    public function getBalanceTransaction(string $txnId)
    {
        return BalanceTransaction::retrieve($txnId);
    }

    /**
     * @see https://stripe.com/docs/api/balance_transactions/list
     *
     * @param array $params
     * @return Collection
     * @throws ApiErrorException
     */
    public function getAllBalanceTransactions(array $params = []): Collection
    {
        return BalanceTransaction::all($params);
    }
}
