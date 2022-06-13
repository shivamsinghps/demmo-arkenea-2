<?php

namespace FMT\DomainBundle\Service\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use FMT\DataBundle\Doctrine\Extensions\DBAL\Types\AbstractDateTimeType;
use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Repository\UserRepository;
use FMT\DomainBundle\Service\Synchronizer\MajorSynchronizer;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Client;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper\DepartmentMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper\TermMapper;
use Tests\FMT\InfrastructureBundle\AbstractTest;

/**
 * Class MajorSynchronizer
 * @package FMT\DomainBundle\Service\Manager
 * @coversDefaultClass FMT\DomainBundle\Service\Synchronizer
 */
class MajorSynchronizerTest extends AbstractTest
{
    /**
     * @dataProvider dataProviderForGetUniqueCampusesId
     * @param UserRepository $userMajorRepository
     * @param Client $client
     * @param $count
     * @covers MajorSynchronizer::getCampusIdentifiers
     */
    public function testCampusIdentifiers(UserRepository $userMajorRepository, Client $client, $count)
    {
        $majorSynchronizer = new MajorSynchronizer($userMajorRepository, $client);
        $uniqueId = $this->invokeMethod($majorSynchronizer, 'getCampusIdentifiers');
        $this->assertCount($count, $uniqueId);
    }

    /**
     * @dataProvider dataProviderForGetUniqueCampusesIdWithException
     * @param UserRepository $userMajorRepository
     * @param Client $client
     * @expectedException \Exception
     * @covers MajorSynchronizer::getCampusIdentifiers
     */
    public function testCampusIdentifiersWithException(UserRepository $userMajorRepository, Client $client)
    {
        $majorSynchronizer = new MajorSynchronizer($userMajorRepository, $client);
        $this->invokeMethod($majorSynchronizer, 'CampusIdentifiers');
    }

    /**
     * @dataProvider dataProviderForIsSyncNeeded
     * @param UserRepository $userMajorRepository
     * @param Client $client
     * @param $isExpectedTrue
     * @covers MajorSynchronizer::isSyncNeeded
     */
    public function testIsSyncNeeded(UserRepository $userMajorRepository, Client $client, $isExpectedTrue)
    {
        $majorSynchronizer = new MajorSynchronizer($userMajorRepository, $client);
        $result = $this->invokeMethod($majorSynchronizer, 'isSyncNeeded');
        $this->assertEquals($isExpectedTrue, $result);
    }

    /**
     * @dataProvider dataProviderForSaveMajor
     * @param UserRepository $userMajorRepository
     * @param Client $client
     * @param $departments
     * @covers MajorSynchronizer::saveMajor
     */
    public function testSaveMajor(UserRepository $userMajorRepository, Client $client, $departments)
    {
        $majorSynchronizer = new MajorSynchronizer($userMajorRepository, $client);
        $result = $this->invokeMethod($majorSynchronizer, 'saveMajor', [$departments, new ArrayCollection([])]);
        $this->assertEmpty($result);
    }

    /**
     * @dataProvider dataProviderForSynchronize
     * @param MajorSynchronizer $majorSynchronizer
     * @param boolean $expectedResult
     * @covers MajorSynchronizer::synchronize
     */
    public function testSynchronize(MajorSynchronizer $majorSynchronizer, $expectedResult)
    {
        $result = $majorSynchronizer->synchronize();
        $result instanceof ArrayCollection ? $result = $result->count() : null;
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForSynchronize()
    {
        // 1.
        $result = [];
        $major1 = $this->createMock(UserMajor::class);
        $major2 = $this->createMock(UserMajor::class);

        $major1->expects($this->once())->method('setActive')->with(false);
        $major2->expects($this->once())->method('setActive')->with(false);

        $majorsArrayCollection = new ArrayCollection([
            $major1,
            $major2,
        ]);
        $departments = [
            '1' => [
                (new DepartmentMapper())->map([
                    'Id' => 1,
                    'Name' => 'Test',
                ]),
            ],
            '2' => [
                (new DepartmentMapper())->map([
                    'Id' => 2,
                    'Name' => 'Test2',
                ]),
            ],
        ];

        $termsArr = [
            (new TermMapper())->map([
                'AdoptionStatus' => 1,
                'Status' => 1,
                'Campus' => [
                    'Id' => 1,
                ],

            ]),
            (new TermMapper())->map([
                'AdoptionStatus' => 1,
                'Status' => 1,
                'Campus' => [
                    'Id' => 2,
                ],

            ]),
            (new TermMapper())->map([
                'AdoptionStatus' => 1,
                'Status' => 1,
                'Campus' => [
                    'Id' => 3,
                ],

            ]),
        ];
        $userMajorRepository = $this->createMock(UserRepository::class);
        $userMajorRepository
            ->method('getMajors')
            ->willReturn($majorsArrayCollection);
        $userMajorRepository
            ->expects($this->exactly(count($majorsArrayCollection) + count($termsArr)))
            ->method('save');
        $client = $this->createMock(Client::class);
        $client
            ->method('termsGetOpened')
            ->willReturn($termsArr);
        $client
            ->method('departmentsGetByCampusId')
            ->willReturn($departments['1']);
        $majorSynchronizer = $this
            ->getMockBuilder(MajorSynchronizer::class)
            ->setConstructorArgs([$userMajorRepository, $client])
            ->setMethods(['isSyncNeeded', 'getUniqueCampusesId', 'saveMajor'])
            ->getMock();
        $majorSynchronizer
            ->method('isSyncNeeded')
            ->willReturn(true);
        $majorSynchronizer
            ->method('getUniqueCampusesId')
            ->willReturn($termsArr);
        $majorSynchronizer
            ->expects($this->any())
            ->method('saveMajor')
            ->with($departments, $majorsArrayCollection);


        $result[] = [
            $majorSynchronizer,
            3,
        ];

        // 2.
        $userMajorRepository = $this->createMock(UserRepository::class);
        $client = $this->createMock(Client::class);
        $userMajorRepository->expects($this->once())->method('rollback');
        $majorSynchronizer = $this
            ->getMockBuilder(MajorSynchronizer::class)
            ->setConstructorArgs([$userMajorRepository, $client])
            ->setMethods(['isSyncNeeded', 'getMajors'])
            ->getMock();
        $majorSynchronizer
            ->method('isSyncNeeded')
            ->willReturn(true);
        $majorSynchronizer
            ->method('getMajors')
            ->willReturn(null);

        $result[] = [
            $majorSynchronizer,
            null,
        ];

        return $result;
    }

    /**
     * @return array
     */
    public function dataProviderForSaveMajor()
    {
        $result = [];
        // 1.
        $departments = [
            '1' => [
                (new DepartmentMapper())->map([
                    'Id' => 1,
                    'Name' => 'Test',
                ]),
            ],
            '2' => [
                (new DepartmentMapper())->map([
                    'Id' => 2,
                    'Name' => 'Test2',
                ]),
            ],
        ];

        $userMajorRepository = $this->createMock(UserRepository::class);
        $userMajorRepository
            ->expects($this->exactly(count($departments)))
            ->method('save');
        $result[] = [
            $userMajorRepository,
            $this->createMock(Client::class),
            $departments,
        ];

        // 2.
        $departments = [];
        $userMajorRepository = $this->createMock(UserRepository::class);
        $userMajorRepository
            ->expects($this->never())
            ->method('save');
        $result[] = [
            $userMajorRepository,
            $this->createMock(Client::class),
            $departments,
        ];

        //3.
        $departments = new \stdClass();
        $userMajorRepository = $this->createMock(UserRepository::class);
        $userMajorRepository
            ->expects($this->never())
            ->method('save');
        $result[] = [
            $userMajorRepository,
            $this->createMock(Client::class),
            $departments,
        ];

        return $result;
    }

    /**
     * @return array
     */
    public function dataProviderForIsSyncNeeded()
    {
        $result = [];

        $timeZone = new \DateTimeZone(AbstractDateTimeType::DEFAULT_TIME_ZONE);
        $dateForFalseResult = new \DateTime();
        $dateForFalseResult->setTimezone($timeZone);
        $dateForTrueResult = new \DateTime('- 20 days');
        $dateForTrueResult->setTimezone($timeZone);
        $dateIncorrectClass = new \stdClass();

        // 1.
        $userMajorRepository = $this->createMock(UserRepository::class);
        $userMajorRepository
            ->method('getMajorSyncDate')
            ->willReturn($dateForTrueResult);

        $result[] = [
            $userMajorRepository,
            $this->createMock(Client::class),
            true,
        ];

        // 2.
        $userMajorRepository = $this->createMock(UserRepository::class);
        $userMajorRepository
            ->method('getMajorSyncDate')
            ->willReturn($dateForFalseResult);

        $result[] = [
            $userMajorRepository,
            $this->createMock(Client::class),
            false,
        ];

        // 3.
        $userMajorRepository = $this->createMock(UserRepository::class);
        $userMajorRepository
            ->method('getMajorSyncDate')
            ->willReturn($dateIncorrectClass);

        $result[] = [
            $userMajorRepository,
            $this->createMock(Client::class),
            true,
        ];

        return $result;
    }

    /**
     * @return array
     */
    public function dataProviderForGetUniqueCampusesIdWithException()
    {
        $client = $this->createMock(Client::class);
        $client->method('termsGetOpened')->willThrowException(new \Exception('test'));

        return [
            [
                $this->createMock(UserRepository::class),
                $client,
                0,
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataProviderForGetUniqueCampusesId()
    {
        $result = [];

        // 1.
        $termsArr = [
            (new TermMapper())->map([
                'AdoptionStatus' => 1,
                'Status' => 1,
                'Campus' => [
                    'Id' => 1,
                ],

            ]),
            (new TermMapper())->map([
                'AdoptionStatus' => 1,
                'Status' => 1,
                'Campus' => [
                    'Id' => 2,
                ],

            ]),
            (new TermMapper())->map([
                'AdoptionStatus' => 1,
                'Status' => 1,
                'Campus' => [
                    'Id' => 3,
                ],

            ]),
        ];

        $client = $this->createMock(Client::class);
        $client->method('termsGetOpened')->willReturn($termsArr);
        $result[] = [
            $this->createMock(UserRepository::class),
            $client,
            3,
        ];

        //2.
        $termsArr = [
            (new TermMapper())->map([
                'AdoptionStatus' => 1,
                'Status' => 1,
                'Campus' => [
                    'Id' => 1,
                ],

            ]),
            (new TermMapper())->map([
                'AdoptionStatus' => 1,
                'Status' => 1,
                'Campus' => [
                    'Id' => 1,
                ],

            ]),
        ];

        $client = $this->createMock(Client::class);
        $client->method('termsGetOpened')->willReturn($termsArr);
        $result[] = [
            $this->createMock(UserRepository::class),
            $client,
            1,
        ];

        //3.
        $termsArr = [];
        $client = $this->createMock(Client::class);
        $client->method('termsGetOpened')->willReturn($termsArr);
        $result[] = [
            $this->createMock(UserRepository::class),
            $client,
            0,
        ];

        return $result;
    }
}
