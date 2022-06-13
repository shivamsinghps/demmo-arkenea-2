<?php

namespace FMT\InfrastructureBundle\Assumptions\Stripe;

use FMT\InfrastructureBundle\Service\Payments\Stripe\StripeClient;
use Stripe\ApiResource;
use Stripe\Balance;
use Stripe\BalanceTransaction;
use Stripe\Charge;
use Stripe\Collection;
use Stripe\Customer;
use Stripe\Event;
use Stripe\Invoice;
use Stripe\Payout;
use Stripe\Refund;
use Stripe\StripeObject;
use Tests\FMT\InfrastructureBundle\AbstractTest;

class ApiTest extends AbstractTest
{
    private static $isStripeDbPrepared = false;

    /**
     * @var Customer[]
     */
    private static $customers = [];

    /**
     * @var StripeClient
     */
    private $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = $this->container->get('test.infrastructure.service.payments.stripe.client');

        $secretKey = $this->container->getParameter('stripe_secret_key');
        if (!preg_match('/sk_test_/', $secretKey)) {
            throw new \Exception('Test Stripe credentials must be used to run Unit tests!');
        }

        if (!self::$isStripeDbPrepared) {
            $this->prepareStripeDb();
            self::$isStripeDbPrepared = true;
        }
    }

    private function prepareStripeDb()
    {
        $customers = $this->client->getAllCustomers();

        /** @var Customer $customer */
        foreach ($customers->data as $customer) {
            $customer->delete();
        }

        $allPayouts = $this->client->getAllPayouts();

        foreach ($allPayouts as $payout) {
            if(Payout::STATUS_PENDING === $payout->status) {
                $payout->cancel();
            }
        }
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        foreach (self::$customers as $customer) {
            $customer->delete();
        }
    }

    public function testCreateCharge()
    {
        $data = [
            'amount' => 2000,
            'currency' => 'usd',
            'source' => 'tok_bypassPending',
            'description' => 'Charge for zoey.miller@example.com'
        ];

        $resultCreateCharge = $this->client->createCharge($data);

        $resultGetCharge = $this->client->getCharge($resultCreateCharge->id);

        $this->assertInstanceOf(Charge::class, $resultGetCharge);
        $this->assertEquals($resultCreateCharge->amount, $resultGetCharge->amount);
        $this->assertEquals($resultCreateCharge->currency, $resultGetCharge->currency);
        $this->assertEquals($resultCreateCharge->source, $resultGetCharge->source);
        $this->assertEquals($resultCreateCharge->description, $resultGetCharge->description);

        return $resultCreateCharge;
    }

    /**
     * @depends testCreateCharge
     *
     * @param Charge $charge
     * @return Charge
     */
    public function testUpdateCharge(Charge $charge)
    {
        $data = [
            'description' => 'New Charge for zoey.miller@example.com'
        ];

        $resultUpdateCharge = $this->client->updateCharge($charge, $data);
        $resultGetCharge = $this->client->getCharge($resultUpdateCharge->id);

        $this->assertEquals($data['description'], $resultGetCharge->description);

        $this->assertEquals($resultUpdateCharge->amount, $resultGetCharge->amount);
        $this->assertEquals($resultUpdateCharge->currency, $resultGetCharge->currency);
        $this->assertEquals($resultUpdateCharge->source, $resultGetCharge->source);
        $this->assertEquals($resultUpdateCharge->description, $resultGetCharge->description);

        return $resultUpdateCharge;
    }

    /**
     * @depends testUpdateCharge
     *
     * @param Charge $charge
     * @return Charge[]
     */
    public function testGetAllCharges(Charge $charge)
    {
        $data = [
            'amount' => 5000,
            'currency' => 'usd',
            'source' => 'tok_mastercard',
            'description' => 'Another Charge for zoey.miller@example.com'
        ];

        $resultCreateCharge = $this->client->createCharge($data);
        $resultGetAllCharges = $this->client->getAllCharges();

        $needles = $allCreatedTestCharges = [
            $charge->id => true,
            $resultCreateCharge->id => true,
        ];

        $allCreatedTestCharges = [$charge, $resultCreateCharge];

        foreach ($resultGetAllCharges->data as $charge) {
            $this->assertInstanceOf(Charge::class, $charge);

            if (array_key_exists($charge->id, $needles)) {
                unset($needles[$charge->id]);
            }
        }

        $this->assertEmpty($needles);

        return $allCreatedTestCharges;
    }

    public function testGetBalance()
    {
        $resultGetBalance = $this->client->getBalance();

        $this->assertInstanceOf(Balance::class, $resultGetBalance);
    }

    /**
     * @return Collection
     */
    public function testGetAllBalanceTransactions()
    {
        $resultAllBalanceTransactions = $this->client->getAllBalanceTransactions();

        /** @var BalanceTransaction $transaction */
        foreach ($resultAllBalanceTransactions->data as $transaction) {
            $this->assertInstanceOf(BalanceTransaction::class, $transaction);
        }

        return $resultAllBalanceTransactions;
    }

    /**
     * @depends testGetAllBalanceTransactions
     *
     * @param Collection $getAllBalanceTransactions
     */
    public function testGetReceiptDownloadLink(Collection $getAllBalanceTransactions)
    {
        /** @var BalanceTransaction $balanceTransaction */
        $balanceTransaction = $getAllBalanceTransactions->data[0];
        $this->assertInstanceOf(BalanceTransaction::class, $balanceTransaction);

        //ToDo: there is receipt_url field in Charge object. It provides possibility to get receipt, but with total amount only. Invoice shows positions in it
        $this->assertEquals(BalanceTransaction::TYPE_CHARGE, $balanceTransaction->reporting_category);
        // The Stripe object to which this transaction is related. So source (that`s Charge id) shouldn`t be null
        $this->assertNotNull($balanceTransaction->source);

        $getCharge = $this->client->getCharge($balanceTransaction->source);
        $this->assertInstanceOf(Charge::class, $getCharge);
        //invoice can be null

        if(!is_null($getCharge->invoice)) {
            $invoice = $getCharge->invoice;
            $getInvoice = $this->client->getInvoice($invoice);
            $this->assertInstanceOf(Invoice::class, $getInvoice);
            $this->assertNotNull($getInvoice->invoice_pdf);
        }
    }

    /**
     * @return Customer
     */
    public function testCreateGetCustomer()
    {
        $data = [
            'email' => sprintf('test.%d@gmail.com', microtime(true)),
            'description' => 'test description for Customer'
        ];

        $resultCreate = $this->client->createCustomer($data);

        $this->assertEquals($resultCreate->email, $data['email']);
        $this->assertEquals($resultCreate->description, $data['description']);

        $resultGet = $this->client->getCustomer($resultCreate->id);

        $this->assertNotEmpty($resultGet);
        $this->assertEquals($resultCreate->email, $data['email']);
        $this->assertEquals($resultCreate->description, $data['description']);

        self::$customers[] = $resultGet;

        return $resultGet;
    }

    /**
     * @depends testCreateGetCustomer
     *
     * @param Customer $customer
     * @return Customer
     */
    public function testUpdateCustomer(Customer $customer)
    {
        $data = [
            'email' => sprintf('test2.%d@gmail.com', microtime(true)),
            'description' => 'updated test description for Customer'
        ];

        $resultUpdate = $this->client->updateCustomer($customer, $data);

        $this->assertEquals($resultUpdate->email, $data['email']);
        $this->assertEquals($resultUpdate->description, $data['description']);

        $resultGet = $this->client->getCustomer($resultUpdate->id);

        $this->assertNotEmpty($resultGet);
        $this->assertEquals($resultUpdate->email, $data['email']);
        $this->assertEquals($resultUpdate->description, $data['description']);

        return $resultUpdate;
    }

    public function testGetAllCustomers()
    {
        $data = [
            'email' => sprintf('test3.%d@gmail.com', microtime(true)),
            'description' => 'another test description for Customer'
        ];

        $resultCreateCustomer = $this->client->createCustomer($data);
        self::$customers[] = $resultCreateCustomer;

        $resultGetAllCustomers = $this->client->getAllCustomers();

        $this->assertEquals(count(self::$customers), count($resultGetAllCustomers->data));

        foreach ($resultGetAllCustomers->data as $customer) {
            $this->assertInstanceOf(Customer::class, $customer);
        }

        return $resultGetAllCustomers;
    }

    public function testGetAllEvents()
    {
        $resultGetAllEvents = $this->client->getAllEvents();

        foreach ($resultGetAllEvents->data as $event) {
            $this->assertInstanceOf(Event::class, $event);

            $resultGelEvent = $this->client->getEvent($event->id);

            $this->assertEquals($event->data, $resultGelEvent->data);
        }
    }

    /**
     * @return ApiResource|Payout
     */
    public function testCreatePayout()
    {
        $data = [
            'amount' => 500,
            'currency' => 'usd',
            'source_type' => 'card',
            'metadata' => [
                'k1' => 'v1',
                'k2' => 'v2',
            ],
        ];

        $resultCreatePayout = $this->client->createPayout($data);
        $resultGetPayout = $this->client->getPayout($resultCreatePayout->id);

        $this->assertEquals($data['amount'], $resultGetPayout->amount);
        $this->assertEquals($data['currency'], $resultGetPayout->currency);
        $this->assertEquals($data['metadata']['k1'], $resultGetPayout->metadata->k1);
        $this->assertEquals($data['metadata']['k2'], $resultGetPayout->metadata->k2);

        return $resultCreatePayout;
    }

    /**
     * @depends testCreatePayout
     *
     * @param Payout $payout
     * @return Payout
     */
    public function testUpdatePayout(Payout $payout)
    {
        $data = [
            'metadata' => [
                'k1' => 'new-v1',
                'k2' => 'new-v2',
            ],
        ];

        $resultUpdatePayout = $this->client->updatePayout($payout, $data);
        $resultGetPayout = $this->client->getPayout($resultUpdatePayout->id);

        $this->assertEquals($payout->amount, $resultGetPayout->amount);
        $this->assertEquals($payout->currency, $resultGetPayout->currency);

        $this->assertEquals($data['metadata']['k1'], $resultGetPayout->metadata->k1);
        $this->assertEquals($data['metadata']['k2'], $resultGetPayout->metadata->k2);

        return $resultUpdatePayout;
    }

    /**
     * @depends testUpdatePayout
     *
     * @param Payout $payout
     * @return Collection
     */
    public function testGetAllPayouts(Payout $payout)
    {
        $data = [
            'amount' => 300,
            'currency' => 'usd'
        ];

        $resultCreatePayout = $this->client->createPayout($data);
        $resultGetAllPayouts = $this->client->getAllPayouts();

        $needles = [
            $payout->id => true,
            $resultCreatePayout->id => true,
        ];

        foreach ($resultGetAllPayouts->data as $payout) {
            $this->assertInstanceOf(Payout::class, $payout);

            if (array_key_exists($payout->id, $needles)) {
                unset($needles[$payout->id]);
            }
        }

        $this->assertEmpty($needles);

        return $resultGetAllPayouts;
    }

    /**
     * @depends testUpdateCharge
     *
     * @param Charge $charge
     * @return Refund|StripeObject
     */
    public function testCreateRefund(Charge $charge)
    {
        $data = [
            'charge' => $charge->id,
            'amount' => 10,
        ];

        $resultCreateRefund = $this->client->createRefund($data);

        $resultGetRefund = $this->client->getRefund($resultCreateRefund->id);
        $this->assertInstanceOf(Refund::class, $resultGetRefund);
        $this->assertEquals($data['charge'], $resultGetRefund->charge);
        $this->assertEquals($data['amount'], $resultGetRefund->amount);

        return $resultGetRefund;
    }

    /**
     * @depends testCreateRefund
     *
     * @param Refund $refund
     * @return Refund|StripeObject
     */
    public function testUpdateRefund(Refund $refund)
    {
        $data = [
            'metadata' => [
                'k1' => 'v1',
                'k2' => 'v2',
            ],
        ];

        $resultUpdateRefund = $this->client->updateRefund($refund, $data);
        $resultGetRefund = $this->client->getRefund($resultUpdateRefund->id);

        $this->assertEquals($data['metadata']['k1'], $resultGetRefund->metadata->k1);
        $this->assertEquals($data['metadata']['k2'], $resultGetRefund->metadata->k2);

        return $resultUpdateRefund;
    }

    /**
     * @depends testGetAllCharges
     * @depends testUpdateRefund
     *
     * @param array $chargesCollection
     * @param Refund $refund
     * @return Collection
     */
    public function testGetAllRefunds(array $chargesCollection, Refund $refund)
    {
        $refunds = [$refund->id => true];
        $amount = 20;

        foreach ($chargesCollection as $charge) {
            if ($charge->id != $refund->charge) {
                $resultCreateRefund = $this->client->createRefund([
                    'charge' => $charge->id,
                    'amount' => $amount++
                ]);

                $refunds[$resultCreateRefund->id] = true;
            }
        }

        $resultGetAllRefunds = $this->client->getAllRefunds();

        foreach ($resultGetAllRefunds->data as $refund) {
            $this->assertInstanceOf(Refund::class, $refund);

            if (array_key_exists($refund->id, $refunds)) {
                unset($refunds[$refund->id]);
            }
        }

        $this->assertEmpty($refunds);

        return $resultGetAllRefunds;
    }
}
