<?php

namespace Tests\FMT\DomainBundle\Service\BookstorePayment;

use DateTime;
use FMT\DomainBundle\Service\BookstorePayment\SendTime;
use PHPUnit\Framework\TestCase;

/**
 * Class SendTimeTest
 */
class SendTimeTest extends TestCase
{
    /**
     * @param SendTime $sendTime
     * @param DateTime $currentDateTime
     * @param array    $expected
     *
     * @dataProvider getSendTimeProvider
     */
    public function testGetPauseInterval(SendTime $sendTime, DateTime $currentDateTime, array $expected): void
    {
        $this->assertEquals($expected['pauseEnded'], $currentDateTime->add($sendTime->getPauseInterval()));
    }

    /**
     * @param SendTime $sendTime
     * @param DateTime $currentDateTime
     * @param array    $expected
     *
     * @dataProvider getSendTimeProvider
     */
    public function testGetTime(SendTime $sendTime, DateTime $currentDateTime, array $expected): void
    {
        $this->assertEquals($expected['time'], $sendTime->getTime($currentDateTime));
    }

    /**
     * @return array
     */
    public function getSendTimeProvider(): array
    {
        return [
            [
                'sendTime' => $this->createSendTime('8 days', 'every monday', '03:00', 0, '2 minutes'),
                'currentDateTime' => new DateTime('2021-12-20 12:00:00'),
                'expected' =>[
                    'pauseEnded' => new DateTime('2021-12-28 11:58:00'),
                    'time' => new DateTime('2021-12-20 02:58:00'),
                ],
            ],
            [
                'sendTime' => $this->createSendTime('4 days + 2 minutes', 'every monday', '03:00', 3, '2 minutes'),
                'currentDateTime' => new DateTime('2021-12-20 12:00:00'),
                'expected' =>[
                    'pauseEnded' => new DateTime('2021-12-24 12:00:00'),
                    'time' => new DateTime('2021-12-19 23:58:00'),
                ],
            ],
            [
                'sendTime' => $this->createSendTime('5 hours', 'every sunday', '11:00', -3, '0 minutes'),
                'currentDateTime' => new DateTime('2021-12-20 12:00:00'),
                'expected' =>[
                    'pauseEnded' => new DateTime('2021-12-20 17:00:00'),
                    'time' => new DateTime('2021-12-26 14:00:00'),
                ],
            ],
        ];
    }

    /**
     * @param string $pause
     * @param string $sendDate
     * @param string $sendTime
     * @param int    $sendTimezone
     * @param string $errorTime
     *
     * @return SendTime
     */
    private function createSendTime(
        string $pause,
        string $sendDate,
        string $sendTime,
        int $sendTimezone,
        string $errorTime
    ): SendTime {
        return new SendTime($pause, $sendDate, $sendTime, $sendTimezone, $errorTime);
    }
}
