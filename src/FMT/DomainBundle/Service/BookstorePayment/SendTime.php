<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

use DateInterval;
use DateTime;
use DateTimeZone;

/**
 * Class SendTime
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class SendTime
{
    /**
     * @var string
     */
    private $pause;

    /**
     * @var string
     */
    private $sendDate;

    /**
     * @var string
     */
    private $sendTime;

    /**
     * @var int
     */
    private $sendTimezone;

    /**
     * @var string
     */
    private $errorTime;

    /**
     * @param string $pause
     * @param string $sendDate
     * @param string $sendTime
     * @param int    $sendTimezone
     * @param string $errorTime
     */
    public function __construct(
        string $pause,
        string $sendDate,
        string $sendTime,
        int $sendTimezone,
        string $errorTime
    ) {
        $this->pause = $pause;
        $this->sendDate = $sendDate;
        $this->sendTime = $sendTime;
        $this->sendTimezone = $sendTimezone;
        $this->errorTime = $errorTime;
    }

    /**
     * @return DateInterval
     */
    public function getPauseInterval(): DateInterval
    {
        return DateInterval::createFromDateString($this->pause . ' - ' . $this->errorTime);
    }

    /**
     * @param DateTime $startDateTime
     *
     * @return DateTime
     */
    public function getTime(DateTime $startDateTime): DateTime
    {
        $timeParts = explode(':', $this->sendTime);

        $time = $startDateTime;
        $time
            ->setTimezone($this->getTimeZone())
            ->add(DateInterval::createFromDateString($this->sendDate))
            ->setTime((int) $timeParts[0], (int) $timeParts[1])
            ->setTimezone(new DateTimeZone(date_default_timezone_get()))
            ->sub(DateInterval::createFromDateString($this->errorTime))
        ;

        return $time;
    }

    /**
     * @return DateTimeZone
     */
    protected function getTimeZone(): DateTimeZone
    {
        $sign = ($this->sendTimezone < 0 ? '-' : '+');
        $strTimezone = $sign . str_pad((string) abs($this->sendTimezone), 2, '0', STR_PAD_LEFT) . '00';

        return new DateTimeZone($strTimezone);
    }
}
