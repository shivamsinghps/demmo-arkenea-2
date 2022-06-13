<?php
/**
 * Author: Anton Orlov
 * Date: 18.05.2018
 * Time: 14:56
 */

namespace Tests\FMT\DomainBundle\Service\Manager;

use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignContact;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserContact;
use FMT\DataBundle\Entity\UserProfile;
use FMT\DomainBundle\Event\UserEvent;
use FMT\DomainBundle\Repository\UserRepositoryInterface;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use FMT\DomainBundle\Service\Manager\UserManager;
use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use PHPUnit\Framework\MockObject\Invocation\ObjectInvocation;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tests\FMT\DomainBundle\AbstractTest;

/**
 * Class UserManagerTest
 * @package Tests\FMT\DomainBundle\Service\Manager
 * @coversDefaultClass FMT\DomainBundle\Service\Manager\UserManager
 */
class UserManagerTest extends AbstractTest
{

    /**
     * @covers ::createOrUpdateUser
     */
    public function testCreateNewUser()
    {
        $expected = $this->createMock(User::class);
        $expected->expects($this->atLeastOnce())->method("setEnabled")->with($this->equalTo(false));

        $fosManager = $this->createMock(FOSUserManager::class);
        $fosManager->method("findUserBy")->willReturn(null);
        $fosManager->expects($this->atLeastOnce())->method("updateUser");

        $manager = new UserManager($fosManager);
        $result = $manager->createOrUpdateUser($expected);

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::createOrUpdateUser
     */
    public function testUpdateExistingUser()
    {
        $expected = $this->createMock(User::class);

        $fosManager = $this->createMock(FOSUserManager::class);
        $fosManager->method("findUserBy")->willReturn($expected);
        $fosManager->expects($this->never())->method("updateUser");

        $manager = new UserManager($fosManager);
        $result = $manager->createOrUpdateUser($expected);

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::addContact
     */
    public function testAddNewContact()
    {
        $expected = $this->createMock(UserContact::class);

        $student = $this->createMock(User::class);
        $student->method("isActiveStudent")->willReturn(true);
        $student->expects($this->once())->method("addContact")->willReturn($expected);

        $donor = $this->createMock(User::class);
        $donor->method("getProfile")->willReturn($this->createMock(UserProfile::class));

        $fosManager = $this->createMock(FOSUserManager::class);
        $fosManager->method("findUserBy")->willReturn($donor);

        $dispatch = $this->createMock(EventDispatcherInterface::class);
        $dispatch->expects($dispatchInvocation = $this->atLeastOnce())->method("dispatch");

        $manager = new UserManager($fosManager);
        $manager->setEventDispatcher($dispatch);

        $result = $manager->addContact($student, $donor);

        $events = array_filter($dispatchInvocation->getInvocations(), function (ObjectInvocation $item) {
            return $item->getParameters()[0] === UserEvent::USER_CONTACT_ADDED;
        });

        $this->assertCount(1, $events);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::addContact
     */
    public function testAddExistingContact()
    {
        $firstName = "Expected";
        $lastName = "Name";

        $expected = $this->createMock(UserContact::class);
        $expected->expects($this->atLeastOnce())->method("setFirstName")->with($this->equalTo($firstName));
        $expected->expects($this->atLeastOnce())->method("setLastName")->with($this->equalTo($lastName));

        $student = $this->createMock(User::class);
        $student->method("isActiveStudent")->willReturn(true);
        $student->method("findContact")->willReturn($expected);
        $student->expects($this->never())->method("addContact");

        $donor = $this->createMock(User::class);
        $donor->method("getProfile")->willReturnCallback(function () use ($firstName, $lastName) {
            $result = $this->createMock(UserProfile::class);
            $result->method("getFirstName")->willReturn($firstName);
            $result->method("getLastName")->willReturn($lastName);
            return $result;
        });

        $fosManager = $this->createMock(FOSUserManager::class);
        $fosManager->method("findUserBy")->willReturn($donor);

        $dispatch = $this->createMock(EventDispatcherInterface::class);
        $dispatch->expects($this->never())->method("dispatch");

        $manager = new UserManager($fosManager);
        $manager->setEventDispatcher($dispatch);

        $result = $manager->addContact($student, $donor);

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::addContact
     * @expectedException \RuntimeException
     */
    public function testAddContactToDonor()
    {
        $donor = $this->createMock(User::class);
        $fosManager = $this->createMock(FOSUserManager::class);
        $manager = new UserManager($fosManager);

        $manager->addContact($manager->makeDonor(), $donor);
    }

    /**
     * @covers ::addContact
     * @expectedException \RuntimeException
     */
    public function testAddContactToInactiveStudent()
    {
        $donor = $this->createMock(User::class);
        $fosManager = $this->createMock(FOSUserManager::class);
        $manager = new UserManager($fosManager);

        $manager->addContact($manager->makeStudent()->setEnabled(false), $donor);
    }

    /**
     * @covers ::addContact
     */
    public function testAddContactAndAssignToCampaign()
    {
        $student = $this->createMock(User::class);
        $student->method("isActiveStudent")->willReturn(true);
        $student->method("findContact")->willReturn($this->createMock(UserContact::class));
        $student->method("getUnfinishedCampaign")->willReturn($this->createMock(Campaign::class));

        $donor = $this->createMock(User::class);
        $donor->method("getProfile")->willReturn($this->createMock(UserProfile::class));

        $campaignManager = $this->createMock(CampaignManagerInterface::class);
        $campaignManager->expects($this->once())
            ->method("assignContact")
            ->willReturn($this->createMock(CampaignContact::class));

        $manager = new UserManager($this->createMock(FOSUserManager::class));
        $manager->setEventDispatcher($this->createMock(EventDispatcherInterface::class));
        $manager->setUserRepository($this->createMock(UserRepositoryInterface::class));
        $manager->setCampaignManager($campaignManager);

        $manager->addContact($student, $donor, true);
    }

    /**
     * @covers ::addContact
     */
    public function testAddContactAndAssignToFinishedCampaign()
    {
        $student = $this->createMock(User::class);
        $student->method("isActiveStudent")->willReturn(true);
        $student->method("findContact")->willReturn($this->createMock(UserContact::class));
        $student->method("getUnfinishedCampaign")->willReturn(null);

        $donor = $this->createMock(User::class);
        $donor->method("getProfile")->willReturn($this->createMock(UserProfile::class));

        $campaignManager = $this->createMock(CampaignManagerInterface::class);
        $campaignManager->expects($this->never())->method("assignContact");

        $manager = new UserManager($this->createMock(FOSUserManager::class));
        $manager->setEventDispatcher($this->createMock(EventDispatcherInterface::class));
        $manager->setUserRepository($this->createMock(UserRepositoryInterface::class));
        $manager->setCampaignManager($campaignManager);

        $manager->addContact($student, $donor, true);
    }

    /**
     * @dataProvider dataProviderForGetUserByEmail
     * @param FosUserManager $fosUserManager
     * @param $isExpectedUserNotEmpty
     * @covers UserManager::getUserByEmail
     */
    public function testGetUserByEmail(
        FosUserManager $fosUserManager,
        $isExpectedUserNotEmpty
    ) {
        $userManager = new UserManager($fosUserManager);
        $user = $userManager->getUserByEmail('test%d@test.test');
        if ($isExpectedUserNotEmpty) {
            $this->assertInstanceOf(User::class, $user);
        } else {
            $this->assertEmpty($user);
        }
    }

    /**
     * @dataProvider dataProviderForConfirm
     * @param UserManager $userManager
     * @param User $user
     * @covers UserManager::confirm
     */
    public function testConfirm(UserManager $userManager, User $user)
    {
        $result = $userManager->confirm($user);
        $this->assertEquals(null, $result);
    }

    /**
     * @dataProvider dataProviderForConfirmWithException
     * @param UserManager $userManager
     * @param User $user
     * @expectedException \Exception
     * @covers UserManager::confirm
     */
    public function testConfirmWithException(UserManager $userManager, User $user)
    {
        $userManager->confirm($user);
    }

    /**
     * @return array
     */
    public function dataProviderForConfirmWithException()
    {
        $result = [];
        $user = new User();
        $userEvent = new UserEvent($user);
        $fosUserManager = $this->createMock(FosUserManager::class);
        $userManager = $this
            ->getMockBuilder(UserManager::class)
            ->setConstructorArgs([
                $fosUserManager,
            ])
            ->setMethods(['dispatch'])
            ->getMock();

        $userManager
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [UserEvent::CONFIRMATION_RECEIVED, $userEvent],
                [UserEvent::CONFIRMATION_FAILED, $userEvent]
            );

        $result[] = [
            $userManager,
            $user,
        ];

        return $result;
    }

    /**
     * @dataProvider dataProviderForCreateWithException
     * @param UserManager $userManager
     * @param User $user
     * @expectedException \Exception
     * @covers UserManager::create
     */
    public function testCreateWithException(UserManager $userManager, User $user)
    {
        $userManager->create($user);
    }

    /**
     * @return array
     */
    public function dataProviderForCreateWithException()
    {
        $result = [];
        $user = new User();
        $this->setDisallowedProperty($user, 'id', 1);
        $userEvent = new UserEvent($user);
        $fosUserManager = $this->createMock(FosUserManager::class);
        $userManager = $this
            ->getMockBuilder(UserManager::class)
            ->setConstructorArgs([
                $fosUserManager,
            ])
            ->setMethods(['dispatch'])
            ->getMock();

        $userManager
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [UserEvent::SIGNUP_STARTED, $userEvent],
                [UserEvent::SIGNUP_FAILED, $userEvent]
            );
        $result[] = [
            $userManager,
            $user,
        ];

        return $result;
    }

    /**
     * @dataProvider dataProviderForCreate
     * @param UserManager $userManager
     * @param User $user
     * @covers UserManager::create
     */
    public function testCreate(UserManager $userManager, User $user)
    {
        $result = $userManager->create($user);
        $this->assertEquals($user, $result);
    }

    /**
     * @dataProvider dataProviderUpdate
     * @param UserManager $userManager
     * @param User $user
     * @covers UserManager::update
     */
    public function testUpdate(UserManager $userManager, User $user)
    {
        $result = $userManager->update($user);
        $this->assertEquals(null, $result);
    }

    /**
     * @dataProvider dataProviderForUpdateWithException
     * @param UserManager $userManager
     * @param User $user
     * @expectedException \Exception
     * @covers UserManager::update
     */
    public function testUpdateWithException(UserManager $userManager, User $user)
    {
        $userManager->update($user);
    }

    /**
     * @covers UserManager::makeStudent
     */
    public function testMakeStudent()
    {
        $userManager = $this->container->get(UserManager::class);
        $user = $userManager->makeStudent();
        $this->assertTrue($user->isIncompleteStudent());
    }

    /**
     * @covers UserManager::makeDonor
     */
    public function testMakeDonor()
    {
        $userManager = $this->container->get(UserManager::class);
        $user = $userManager->makeDonor();
        $this->assertTrue($user->isIncompleteDonor());
    }

    /**
     * @dataProvider dataProviderForCompleteUser
     * @param User $user
     * @param string $expectedRole
     * @covers UserManager::completeUser
     */
    public function testCompleteUser(User $user, $expectedRole)
    {
        $userManager = $this->container->get(UserManager::class);
        $user = $userManager->completeUser($user);
        $this->assertTrue($user->hasRole($expectedRole));
    }

    /**
     * @return array
     */
    public function dataProviderForCompleteUser()
    {
        return [
            [(new User())->setRoles([User::ROLE_INCOMPLETE_STUDENT]), User::ROLE_STUDENT],
            [(new User())->setRoles([User::ROLE_INCOMPLETE_DONOR]), User::ROLE_DONOR]
        ];
    }

    /**
     * @return array
     */
    public function dataProviderForUpdateWithException()
    {
        $result = [];
        $user = new User();
        $this->setDisallowedProperty($user, 'enabled', 0);
        $userEvent = new UserEvent($user);
        $fosUserManager = $this->createMock(FosUserManager::class);
        $userManager = $this
            ->getMockBuilder(UserManager::class)
            ->setConstructorArgs([
                $fosUserManager,
            ])
            ->setMethods(['dispatch'])
            ->getMock();

        $userManager
            ->expects($this->once())
            ->method('dispatch')
            ->with(UserEvent::SIGNUP_FAILED, $userEvent);
        $result[] = [
            $userManager,
            $user,
        ];

        return $result;
    }

    /**
     * @return array
     */
    public function dataProviderUpdate()
    {
        $result = [];
        $user = new User();
        $this->setDisallowedProperty($user, 'enabled', 1);
        $userEvent = new UserEvent($user);
        $fosUserManager = $this->createMock(FosUserManager::class);
        $userManager = $this
            ->getMockBuilder(UserManager::class)
            ->setConstructorArgs([
                $fosUserManager,
            ])
            ->setMethods(['dispatch', 'updateUser'])
            ->getMock();

        $userManager
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [UserEvent::USER_UPDATED, $userEvent],
                [UserEvent::USER_PROFILE_UPDATED, $userEvent]
            );

        $fosUserManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($user);

        $result[] = [
            $userManager,
            $user,
        ];

        return $result;
    }

    /**
     * @return array
     */
    public function dataProviderForCreate()
    {
        $result = [];
        $user = new User();
        $userEvent = new UserEvent($user);
        $fosUserManager = $this->createMock(FosUserManager::class);
        $userManager = $this
            ->getMockBuilder(UserManager::class)
            ->setConstructorArgs([
                $fosUserManager,
            ])
            ->setMethods(['dispatch', 'updateUser'])
            ->getMock();

        $userManager
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [UserEvent::SIGNUP_STARTED, $userEvent],
                [UserEvent::SIGNUP_SUCCESS, $userEvent]
            );

        $fosUserManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($user);

        $result[] = [
            $userManager,
            $user,
        ];

        return $result;
    }

    /**
     * @return array
     */
    public function dataProviderForConfirm()
    {
        $result = [];
        $user = new User();
        $userEvent = new UserEvent($user);
        $this->setDisallowedProperty($user, 'id', 1);
        $fosUserManager = $this->createMock(FosUserManager::class);
        $userManager = $this
            ->getMockBuilder(UserManager::class)
            ->setConstructorArgs([
                $fosUserManager,
            ])
            ->setMethods(['dispatch', 'updateUser'])
            ->getMock();

        $userManager
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [UserEvent::CONFIRMATION_RECEIVED, $userEvent],
                [UserEvent::CONFIRMATION_SUCCESS, $userEvent]
            );

        $fosUserManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($user);

        $result[] = [
            $userManager,
            $user,
        ];

        return $result;
    }

    /**
     * @return array
     */
    public function dataProviderForGetUserByEmail()
    {
        $result = [];

        // 1.
        $fosUserManager = $this->createMock(FosUserManager::class);
        $fosUserManager
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with('test%d@test.test')
            ->willReturn(new User());

        $result[] = [
            $fosUserManager,
            true,
        ];

        // 2.
        $fosUserManager = $this->createMock(FosUserManager::class);
        $fosUserManager
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with('test%d@test.test')
            ->willReturn(null);

        $result[] = [
            $fosUserManager,
            false,
        ];

        return $result;
    }
}
