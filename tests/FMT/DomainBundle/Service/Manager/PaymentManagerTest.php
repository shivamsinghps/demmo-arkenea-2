<?php
/**
 * Author: Anton Orlov
 * Date: 11.05.2018
 * Time: 18:16
 */

namespace Tests\FMT\DomainBundle\Service\Manager;

use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignContact;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserContact;
use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Event\TransactionEvent;
use FMT\DomainBundle\Repository\UserTransactionRepositoryInterface;
use FMT\DomainBundle\Service\Manager\PaymentManager;
use FMT\DomainBundle\Service\PaymentProcessor\ProcessorInterface;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\DomainBundle\Type\Payment\CommissionInterface;
use FMT\DomainBundle\Type\Payment\Donation;
use FMT\DomainBundle\Type\Payment\Settings as PaymentSettings;
use FMT\InfrastructureBundle\Helper\LogHelper;
use PHPUnit\Framework\MockObject\Invocation\ObjectInvocation;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tests\FMT\DomainBundle\AbstractTest;

/**
 * Class PaymentManagerTest
 * @package Tests\FMT\DomainBundle\Service\Manager
 * @coversDefaultClass \FMT\DomainBundle\Service\Manager\PaymentManager
 */
class PaymentManagerTest extends AbstractTest
{
    /** @var PaymentSettings */
    private $settings;

    /** @var MockObject */
    private $dispatcher;

    /** @var MockObject */
    private $repository;

    /** @var MockObject */
    private $userManager;

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
     * @covers ::getTransaction
     */
    public function testTransactionFetch()
    {
        $paymentManager = $this->getFreshPaymentManager();
        $this->repository->expects($this->once())->method("findById")->willReturn(new UserTransaction());

        $result = $paymentManager->getTransaction(100500);

        $this->assertInstanceOf(UserTransaction::class, $result);
    }

    /**
     * @covers ::sendDonation
     * @expectedException \RuntimeException
     */
    public function testDonationInactiveStudent()
    {
        $paymentManager = $this->getFreshPaymentManager();
        $donation = $this->getDonation(true, $this->createEntity(User::class, ["isActiveStudent" => false]));
        $paymentManager->sendDonation($donation);
    }

    /**
     * @covers ::sendDonation
     * @expectedException \RuntimeException
     */
    public function testDonationInactiveCampaign()
    {
        $paymentManager = $this->getFreshPaymentManager();
        $donation = $this->getDonation(true, $this->createEntity(User::class, ["hasUnfinishedCampaign" => false]));
        $paymentManager->sendDonation($donation);
    }

    /**
     * @covers ::sendDonation
     * @expectedException \RuntimeException
     */
    public function testDonationZeroAmount()
    {
        $paymentManager = $this->getFreshPaymentManager();
        $donation = $this->getDonation();
        $donation->setPaymentAmount(0);
        $paymentManager->sendDonation($donation);
    }

    /**
     * @covers ::sendDonation
     * @expectedException \RuntimeException
     */
    public function testDonationNegativeAmount()
    {
        $paymentManager = $this->getFreshPaymentManager();
        $donation = $this->getDonation();
        $donation->setPaymentAmount(-100500);
        $paymentManager->sendDonation($donation);
    }

    /**
     * @covers ::sendDonation
     */
    public function testChargeSucceed()
    {
        $paymentManager = $this->getFreshPaymentManager();
        $this->repository->expects($this->once())->method("beginTransaction");
        $this->repository->expects($this->atLeastOnce())->method("save");
        $this->repository->expects($this->atLeastOnce())->method("commit");
        $this->repository->expects($this->never())->method("rollback");

        $dispatchInvocation = $this->atLeastOnce();
        $this->dispatcher->expects($dispatchInvocation)->method("dispatch");

        $donation = $this->getDonation();
        $donation->getPaymentProcessor()->expects($this->once())->method("charge");
        $paymentManager->sendDonation($donation);

        $started = array_filter($dispatchInvocation->getInvocations(), function (ObjectInvocation $item) {
            return in_array(TransactionEvent::TRANSACTION_STARTED, $item->getParameters());
        });
        $completed = array_filter($dispatchInvocation->getInvocations(), function (ObjectInvocation $item) {
            return in_array(TransactionEvent::TRANSACTION_COMPLETED, $item->getParameters());
        });
        $failed = array_filter($dispatchInvocation->getInvocations(), function (ObjectInvocation $item) {
            return in_array(TransactionEvent::TRANSACTION_FAILED, $item->getParameters());
        });

        $this->assertCount(1, $started);
        $this->assertCount(1, $completed);
        $this->assertCount(0, $failed);
    }

    /**
     * @covers ::sendDonation
     * @expectedException \RuntimeException
     */
    public function testChargeFailed()
    {
        $paymentManager = $this->getFreshPaymentManager();
        $this->repository->expects($this->once())->method("beginTransaction");
        $this->repository->expects($this->never())->method("commit");
        $this->repository->expects($this->once())->method("rollback");

        $dispatchInvocation = $this->atLeastOnce();
        $this->dispatcher->expects($dispatchInvocation)->method("dispatch");

        $donation = $this->getDonation();
        $donation->getPaymentProcessor()->method("charge")
            ->willThrowException(new \RuntimeException("Test save error"));
        $paymentManager->sendDonation($donation);

        $started = array_filter($dispatchInvocation->getInvocations(), function (ObjectInvocation $item) {
            return in_array(TransactionEvent::TRANSACTION_STARTED, $item->getParameters());
        });
        $completed = array_filter($dispatchInvocation->getInvocations(), function (ObjectInvocation $item) {
            return in_array(TransactionEvent::TRANSACTION_COMPLETED, $item->getParameters());
        });
        $failed = array_filter($dispatchInvocation->getInvocations(), function (ObjectInvocation $item) {
            return in_array(TransactionEvent::TRANSACTION_FAILED, $item->getParameters());
        });

        $this->assertCount(1, $started);
        $this->assertCount(0, $completed);
        $this->assertCount(1, $failed);
    }

    /**
     * @covers ::sendDonation
     */
    public function testAnonymousDonation()
    {
        $paymentManager = $this->getFreshPaymentManager();
        $this->userManager->expects($this->never())->method("addContact");

        $donation = $this->getDonation();
        $donation->setAnonymous(true);
        $paymentManager->sendDonation($donation);
    }

    /**
     * @covers ::sendDonation
     */
    public function testNamedDonation()
    {
        $paymentManager = $this->getFreshPaymentManager();
        $this->userManager->expects($this->once())->method("addContact");

        $donation = $this->getDonation();
        $donation->setFirstName("Unit");
        $donation->setLastName("Test");
        $donation->setAnonymous(false);
        $paymentManager->sendDonation($donation);
    }

    /**
     * @return PaymentManager
     */
    private function getFreshPaymentManager()
    {
        $this->settings = new PaymentSettings();
        $this->settings->application = $this->createMock(CommissionInterface::class);
        $this->settings->paymentService = $this->createMock(CommissionInterface::class);
        $this->settings->currency = "usd";
        $this->settings->live = true;
        $this->settings->publicKey = uniqid();

        $userContact = $this->createMock(UserContact::class);
        $userContact->method("getCampaignContact")->willReturn($this->createMock(CampaignContact::class));

        $this->repository = $this->createMock(UserTransactionRepositoryInterface::class);
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->userManager->method("createOrUpdateUser")->willReturn($this->createMock(User::class));
        $this->userManager->method("addContact")->willReturn($userContact);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);

        $paymentManager = new PaymentManager($this->settings);
        $paymentManager->setRepository($this->repository);
        $paymentManager->setUserManager($this->userManager);
        $paymentManager->setEventDispatcher($this->dispatcher);

        return $paymentManager;
    }

    /**
     * @param bool $anonymous
     * @param User $recipient
     * @return Donation
     */
    private function getDonation($anonymous = true, $recipient = null)
    {
        if (!$recipient) {
            $campaign = $this->createMock(Campaign::class);
            $campaign->method("isPaused")->willReturn(false);

            $recipient = $this->createMock(User::class);
            $recipient->method("isActiveStudent")->willReturn(true);
            $recipient->method("hasUnfinishedCampaign")->willReturn(true);
            $recipient->method("getUnfinishedCampaign")->willReturn($campaign);
        }

        $result = new Donation($recipient);
        $result->setEmail(uniqid("test@") . ".com");
        $result->setPaymentAmount(100500);
        $result->setPaymentProcessor($this->createMock(ProcessorInterface::class));
        $result->setAnonymous($anonymous);

        return $result;
    }
}
