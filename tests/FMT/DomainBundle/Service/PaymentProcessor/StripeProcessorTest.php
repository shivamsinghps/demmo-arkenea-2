<?php
/**
 * Author: Anton Orlov
 * Date: 17.05.2018
 * Time: 19:34
 */

namespace Test\FMT\DomainBundle\Service\PaymentProcessor;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Service\PaymentProcessor\StripeProcessor;
use FMT\DomainBundle\Type\Payment\CommissionInterface;
use FMT\DomainBundle\Type\Payment\Settings as PaymentSettings;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FMT\InfrastructureBundle\Service\Payments\Stripe\StripeClient;
use PHPUnit\Framework\MockObject\MockObject;
use Stripe\Charge;
use Tests\FMT\DomainBundle\AbstractTest;

/**
 * Class StripeProcessorTest
 * @package Test\FMT\DomainBundle\Service\PaymentProcessor
 * @coversDefaultClass \FMT\DomainBundle\Service\PaymentProcessor\StripeProcessor
 */
class StripeProcessorTest extends AbstractTest
{
    /** @var MockObject */
    private $client;

    /** @var PaymentSettings */
    private $settings;

    public function setUp()
    {
        parent::setUp();

        $reflection = new \ReflectionClass(LogHelper::class);
        if ($reflection->hasProperty("logger")) {
            $property = $reflection->getProperty("logger");
            $property->setAccessible(true);
            $property->setValue(null);
        }
    }

    /**
     * @param string $descriptor
     * @param bool $result
     *
     * @covers ::isSupport
     * @dataProvider getDataDescriptors
     */
    public function testSupportedDescriptors($descriptor, $result)
    {
        $processor = $this->getFreshStripeProcessor();
        $this->assertEquals($result, $processor->isSupport($descriptor));
    }

    /**
     * @covers ::charge
     */
    public function testSuccessfulCharge()
    {
        $amount = 100500;
        $transactionId = uniqid("txn_");
        $invocation = $this->once();

        $transaction = $this->getFreshTransaction($amount);
        $transaction->expects($invocation)->method("setExternalId");

        $response = $this->getChargeResponse([
            "amount" => $amount,
            "balance_transaction" => $transactionId
        ]);

        $processor = $this->getFreshStripeProcessor($response);
        $processor->charge($transaction);

        $this->assertEquals($transactionId, $invocation->getInvocations()[0]->getParameters()[0]);
    }

    /**
     * @covers ::charge
     * @expectedException \RuntimeException
     */
    public function testChargeError()
    {
        $transaction = $this->getFreshTransaction();
        $transaction->expects($this->never())->method("setExternalId");

        $response = $this->getChargeResponse([
            "status" => "failed"
        ]);

        $processor = $this->getFreshStripeProcessor($response);
        $processor->charge($transaction);
    }

    /**
     * @covers ::charge
     * @expectedException \RuntimeException
     */
    public function testChargeLiveMode()
    {
        $transaction = $this->getFreshTransaction();
        $transaction->expects($this->never())->method("setExternalId");

        $response = $this->getChargeResponse([
            "livemode" => false
        ]);

        $processor = $this->getFreshStripeProcessor($response);
        $this->settings->live = true;
        $processor->charge($transaction);
    }

    /**
     * @covers ::charge
     * @expectedException \RuntimeException
     */
    public function testChargeCurrencyInconsistency()
    {
        $transaction = $this->getFreshTransaction();
        $transaction->expects($this->never())->method("setExternalId");

        $response = $this->getChargeResponse([
            "currency" => "eur"
        ]);

        $processor = $this->getFreshStripeProcessor($response);
        $this->settings->currency = "usd";
        $processor->charge($transaction);
    }

    /**
     * @covers ::charge
     * @expectedException \RuntimeException
     */
    public function testChargeAmountInconsistency()
    {
        $transaction = $this->getFreshTransaction();
        $transaction->expects($this->never())->method("setExternalId");

        $response = $this->getChargeResponse([
            "amount" => 500100
        ]);

        $processor = $this->getFreshStripeProcessor($response);
        $processor->charge($transaction);
    }

    /**
     * @return array
     */
    public function getDataDescriptors()
    {
        return [
            ["Not a json", false],
            [json_encode(["test" => true]), false],
            [1234, false],
            [json_encode(["token" => ["id" => 1234]]), true]
        ];
    }

    /**
     * @param Charge $response
     * @return StripeProcessor
     */
    private function getFreshStripeProcessor($response = null)
    {
        $this->client = $this->createMock(StripeClient::class);

        if ($response) {
            $this->client->expects($this->once())->method("createCharge")->willReturn($response);
        }

        $this->settings = new PaymentSettings();
        $this->settings->application = $this->createMock(CommissionInterface::class);
        $this->settings->application->method("charge")->willReturn(100500);
        $this->settings->paymentService = $this->createMock(CommissionInterface::class);
        $this->settings->paymentService->method("charge")->willReturn(100500);
        $this->settings->currency = $response ? $response->currency : "usd";
        $this->settings->publicKey = uniqid("key_");
        $this->settings->live = $response ? $response->livemode : true;

        return new StripeProcessor($this->client, $this->settings);
    }

    /**
     * @return MockObject
     */
    private function getFreshTransaction()
    {
        $result = $this->createMock(UserTransaction::class);
        $result->method("getAmount")->willReturn(100500);
        $result->method("getNet")->willReturn(10500);
        $result->method("getFee")->willReturn(10050);
        $result->method("getSender")->willReturn($this->createMock(User::class));
        $result->method("getRecipient")->willReturn($this->createMock(User::class));
        return $result;
    }

    /**
     * @param array $data
     * @return MockObject
     */
    private function getChargeResponse($data = [])
    {
        static $default = [
            "livemode" => true,
            "status" => "succeeded",
            "currency" => "usd",
            "amount" => 100500,
            "balance_transaction" => "txn_bfoauyegfuy9317y182ddsa",
            "outcome" => 100500
        ];

        $result = $this->createMock(Charge::class);
        $result->method("__get")->willReturnCallback(function ($property) use ($default, $data) {
            $result = null;
            if (array_key_exists($property, $data)) {
                $result = $data[$property];
            } elseif (array_key_exists($property, $default)) {
                $result = $default[$property];
            }
            return $result;
        });

        return $result;
    }
}
