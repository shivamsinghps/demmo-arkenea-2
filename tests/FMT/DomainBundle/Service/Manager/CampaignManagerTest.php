<?php

namespace Tests\FMT\DomainBundle\Service\Manager;

use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignBook;
use FMT\DataBundle\Entity\CampaignContact;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserContact;
use FMT\DomainBundle\Event\CampaignEvent;
use FMT\DomainBundle\Repository\CampaignRepositoryInterface;
use FMT\DomainBundle\Service\BookManagerInterface;
use FMT\DomainBundle\Service\CartManagerInterface;
use FMT\DomainBundle\Service\Manager\CampaignManager;
use PHPUnit\Framework\MockObject\Invocation\ObjectInvocation;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tests\FMT\DomainBundle\AbstractTest;

/**
 * Class CampaignManagerTest
 * @package Tests\FMT\DomainBundle\Service\Manager
 *
 * @coversDefaultClass FMT\DomainBundle\Service\Manager\CampaignManager
 */
class CampaignManagerTest extends AbstractTest
{
    /** @var int */
    protected $shippingCodeId;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->shippingCodeId = $this->container->getParameter('nebook_default_shipping_id');
    }

    /**
     *
     */
    public function testPrepareNew()
    {
        $shippingCodeId = $this->container->getParameter('nebook_default_shipping_id');

        $user = new User();
        $user->setEmail('test@gmail.com');

        $campaign = new Campaign();
        $campaign->setUser($user);
        $campaign->setShippingOption($shippingCodeId);

        $service = $this->container->get(CampaignManager::class);
        $result = $service->prepareNew($user);

        $this->assertEquals($result, $campaign);
    }


    /**
     * @dataProvider createUpdateDataProvider
     * @param Campaign $campaign
     * @param CampaignManager $service
     * @throws \Exception
     */
    public function testCreate(Campaign $campaign, CampaignManager $service)
    {
        $dispatcher = $this->createMock(EventDispatcher::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(CampaignEvent::CAMPAIGN_CREATED), $this->equalTo(new CampaignEvent($campaign)));

        /** @var CampaignManager $service */
        $service->setEventDispatcher($dispatcher);

        $result = $service->create($campaign);

        $this->assertTrue($result);
    }

    /**
     * @dataProvider createUpdateDataProvider
     * @param Campaign $campaign
     * @param CampaignManager $service
     */
    public function testUpdate(Campaign $campaign, CampaignManager $service)
    {
        $dispatcher = $this->createMock(EventDispatcher::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(CampaignEvent::CAMPAIGN_UPDATED), $this->equalTo(new CampaignEvent($campaign)));

        $service->setEventDispatcher($dispatcher);

        $result = $service->update($campaign);

        $this->assertTrue($result);
    }

    /**
     * @return array
     */
    public function createUpdateDataProvider()
    {
        $user = new User();
        $user->setEmail('test@gmail.com');

        $campaign = new Campaign();
        $campaign->setUser($user);
        $campaign->setEstimatedCost(100);

        $repository = $this->createMock(CampaignRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('save');

        $bookManager = $this->createMock(BookManagerInterface::class);
        $cartManager = $this->createMock(CartManagerInterface::class);

        $service = $this->getMockBuilder(CampaignManager::class)
            ->setConstructorArgs([$repository, $bookManager, $cartManager, $this->shippingCodeId])
            ->setMethods(['updateBooksInfo'])
            ->getMock();

        return [
            [
                'campaign' => $campaign,
                'service' => $service,
            ]
        ];
    }


    /**
     * @dataProvider createUpdateWithExceptionDataProvider
     * @param Campaign $campaign
     * @param CampaignManager $service
     * @expectedException \Exception
     */
    public function testCreateWithException(Campaign $campaign, CampaignManager $service)
    {
        $dispatcher = $this->createMock(EventDispatcher::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(CampaignEvent::CAMPAIGN_FAILED), $this->equalTo(new CampaignEvent($campaign)));

        /** @var CampaignManager $service */
        $service->setEventDispatcher($dispatcher);
        $service->create($campaign);
    }

    /**
     * @dataProvider createUpdateWithExceptionDataProvider
     * @param Campaign $campaign
     * @param CampaignManager $service
     * @expectedException \Exception
     */
    public function testUpdateWithException(Campaign $campaign, CampaignManager $service)
    {
        $dispatcher = $this->createMock(EventDispatcher::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(CampaignEvent::CAMPAIGN_FAILED), $this->equalTo(new CampaignEvent($campaign)));

        /** @var CampaignManager $service */
        $service->setEventDispatcher($dispatcher);
        $service->update($campaign);
    }

    /**
     * @return array
     */
    public function createUpdateWithExceptionDataProvider()
    {
        $campaign = new Campaign();
        $campaign->setEstimatedCost(100);

        $repository = $this->createMock(CampaignRepositoryInterface::class);
        $repository
            ->expects($this->never())
            ->method('save');

        $bookManager = $this->createMock(BookManagerInterface::class);
        $cartManager = $this->createMock(CartManagerInterface::class);

        $service = $this->getMockBuilder(CampaignManager::class)
            ->setConstructorArgs([$repository, $bookManager, $cartManager, $this->shippingCodeId])
            ->setMethods(['updateBooksInfo'])
            ->getMock();

        $service
            ->expects($this->once())
            ->method('updateBooksInfo')
            ->will($this->throwException(new \Exception()));

        return [
            [
                'campaign' => $campaign,
                'service' => $service,
            ]
        ];
    }

    /**
     *
     */
    public function testGetRepository()
    {
        $repository = $this->createMock(CampaignRepositoryInterface::class);
        $bookManager = $this->createMock(BookManagerInterface::class);
        $cartManager = $this->createMock(CartManagerInterface::class);

        $service = new CampaignManager($repository, $bookManager, $cartManager, $this->shippingCodeId);

        $result = $service->getRepository();

        $this->assertEquals($result, $repository);
    }


    /**
     *
     */
    public function testUpdateBooksInfo()
    {
        $book1 = new CampaignBook();
        $book1->setSku(123);

        $book2 = new CampaignBook();
        $book2->setSku(456);

        $campaign = new Campaign();
        $campaign
            ->addBook($book1)
            ->addBook($book2);

        $repository = $this->createMock(CampaignRepositoryInterface::class);

        $bookManager = $this->createMock(BookManagerInterface::class);
        $bookManager
            ->expects($this->exactly(2))
            ->method('update')
            ->withConsecutive(
                [$this->equalTo($book1)],
                [$this->equalTo($book2)]
            );


        $cartManager = $this->createMock(CartManagerInterface::class);

        $service = new CampaignManager($repository, $bookManager, $cartManager, $this->shippingCodeId);

        $result = $this->invokeMethod($service, 'updateBooksInfo', [&$campaign]);

        $this->assertTrue($result);
    }


    /**
     * @expectedException \Exception
     */
    public function testUpdateBooksInfoWithException()
    {
        $book = new CampaignBook();
        $book->setSku(123);

        $campaign = new Campaign();
        $campaign->addBook($book);

        $repository = $this->createMock(CampaignRepositoryInterface::class);

        $bookManager = $this->createMock(BookManagerInterface::class);
        $bookManager
            ->expects($this->once())
            ->method('update')
            ->will($this->throwException(new \Exception()));

        $cartManager = $this->createMock(CartManagerInterface::class);

        $service = new CampaignManager($repository, $bookManager, $cartManager, $this->shippingCodeId);

        $this->invokeMethod($service, 'updateBooksInfo', [&$campaign]);
    }

    /**
     * @covers ::assignContact
     */
    public function testAssignNewContact()
    {
        $expected = $this->createMock(CampaignContact::class);

        $dispatch = $this->createMock(EventDispatcherInterface::class);
        $dispatch->expects($dispatchInvocation = $this->once())->method("dispatch");

        $repository = $this->createMock(CampaignRepositoryInterface::class);
        $repository->expects($this->once())->method("save");

        $bookManager = $this->createMock(BookManagerInterface::class);

        $campaign = $this->createMock(Campaign::class);
        $campaign->expects($this->atLeastOnce())->method("isActive")->willReturn(true);
        $campaign->method("findContact")->willReturn(null);
        $campaign->expects($this->once())->method("addContact")->willReturn($expected);

        $cartManager = $this->createMock(CartManagerInterface::class);

        $manager = new CampaignManager($repository, $bookManager, $cartManager, $this->shippingCodeId);
        $manager->setEventDispatcher($dispatch);

        $result = $manager->assignContact($campaign, $this->createMock(UserContact::class));

        $events = array_filter($dispatchInvocation->getInvocations(), function (ObjectInvocation $item) {
            return $item->getParameters()[0] === CampaignEvent::CAMPAIGN_CONTACT_ADDED;
        });

        $this->assertCount(1, $events);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::assignContact
     */
    public function testAssignExistingContact()
    {
        $expected = $this->createMock(CampaignContact::class);

        $dispatch = $this->createMock(EventDispatcherInterface::class);
        $dispatch->expects($this->never())->method("dispatch");

        $repository = $this->createMock(CampaignRepositoryInterface::class);
        $repository->expects($this->never())->method("save");

        $bookManager = $this->createMock(BookManagerInterface::class);

        $campaign = $this->createMock(Campaign::class);
        $campaign->expects($this->atLeastOnce())->method("isActive")->willReturn(true);
        $campaign->method("findContact")->willReturn($expected);
        $campaign->expects($this->never())->method("addContact");

        $cartManager = $this->createMock(CartManagerInterface::class);

        $manager = new CampaignManager($repository, $bookManager, $cartManager, $this->shippingCodeId);
        $manager->setEventDispatcher($dispatch);

        $result = $manager->assignContact($campaign, $this->createMock(UserContact::class));

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::assignContact
     * @expectedException \RuntimeException
     */
    public function testAssignContactToInactiveCampaign()
    {
        $dispatch = $this->createMock(EventDispatcherInterface::class);
        $dispatch->expects($this->never())->method("dispatch");

        $repository = $this->createMock(CampaignRepositoryInterface::class);
        $repository->expects($this->never())->method("save");

        $bookManager = $this->createMock(BookManagerInterface::class);

        $campaign = $this->createMock(Campaign::class);
        $campaign->method("isActive")->willReturn(false);
        $campaign->expects($this->never())->method("addContact");

        $cartManager = $this->createMock(CartManagerInterface::class);

        $manager = new CampaignManager($repository, $bookManager, $cartManager, $this->shippingCodeId);
        $manager->setEventDispatcher($dispatch);

        $manager->assignContact($campaign, $this->createMock(UserContact::class));
    }
}
