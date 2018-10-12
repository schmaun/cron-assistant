<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model;

use MyHammer\CronAssistant\Model\ValueObject\AnySchedule;
use MyHammer\CronAssistant\Model\ValueObject\DateTime;
use MyHammer\CronAssistant\Model\ValueObject\ListSchedule;
use MyHammer\CronAssistant\Model\ValueObject\RangeSchedule;
use MyHammer\CronAssistant\Model\ValueObject\Schedule;
use MyHammer\CronAssistant\Model\ValueObject\SteppedAnySchedule;
use MyHammer\CronAssistant\Model\ValueObject\SteppedRangeSchedule;

class Cron
{
    /**
     * @var Schedule
     */
    private $minute;

    /**
     * @var Schedule
     */
    private $hour;

    /**
     * @var Schedule
     */
    private $day;

    /**
     * @var Schedule
     */
    private $month;

    /**
     * @var Schedule
     */
    private $weekDay;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $command;

    public function isIn(DateTime $dateTime): bool
    {
        if(!$this->isInDate($dateTime)) {
            return false;
        }

        if ($dateTime->minute === null && $dateTime->day === null) {
            return true;
        }

        return $this->isInTime($dateTime);
    }

    /**
     * @param Schedule $minute
     * @return Cron
     */
    public function setMinute(Schedule $minute): Cron
    {
        $this->validate($minute, 0, 59);
        $this->minute = $minute;

        return $this;
    }

    /**
     * @param Schedule $hour
     * @return Cron
     */
    public function setHour(Schedule $hour): Cron
    {
        $this->validate($hour, 0, 23);
        $this->hour = $hour;

        return $this;
    }

    /**
     * @param Schedule $day
     * @return Cron
     */
    public function setDay(Schedule $day): Cron
    {
        $this->validate($day, 1, 31);
        $this->day = $day;

        return $this;
    }

    /**
     * @param Schedule $month
     * @return Cron
     */
    public function setMonth(Schedule $month): Cron
    {
        $this->validate($month, 1, 12);
        $this->month = $month;

        return $this;
    }

    /**
     * @param Schedule $weekDay
     * @return Cron
     */
    public function setWeekDay(Schedule $weekDay): Cron
    {
        $this->validate($weekDay, 0, 6);
        $this->weekDay = $weekDay;

        return $this;
    }

    /**
     * @param string $user
     * @return Cron
     */
    public function setUser(string $user): Cron
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $command
     * @return Cron
     */
    public function setCommand(string $command): Cron
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @param Schedule $schedule
     * @param $min
     * @param $max
     * @throws \InvalidArgumentException
     */
    private function validate(Schedule $schedule, $min, $max): void
    {
        if (!$this->isValid($schedule, $min, $max)) {
            throw new \InvalidArgumentException(sprintf('%s(%s) is not valid for (%s - %s)', \get_class($schedule), $schedule, $min, $max));
        }
    }

    /**
     * @param Schedule $schedule
     * @param int $min
     * @param int $max
     * @return bool
     */
    private function isValid(Schedule $schedule, int $min, int $max): bool
    {
        if ($schedule instanceof AnySchedule) {
            return true;
        }

        if ($schedule instanceof SteppedAnySchedule) {
            return $schedule->getStep() >= $min && $schedule->getStep() <= $max;
        }

        if ($schedule instanceof ListSchedule) {
            $values = $schedule->getValues();

            return (min($values) >= $min && max($values) <= $max);
        }

        if ($schedule instanceof RangeSchedule) {
            return ($schedule->getMin() >= $min && $schedule->getMax() <= $max);
        }

        if ($schedule instanceof SteppedRangeSchedule) {
            return ($schedule->getMin() >= $min && $schedule->getMax() <= $max);
        }

        return false;
    }

    private function isInDate(DateTime $dateTime): bool
    {
        $date = new \DateTimeImmutable($dateTime->year . '-'. $dateTime->month . '-' . $dateTime->day);
        $weekDay = (int)$date->format('w');

        if ($this->month->isIn($dateTime->month)
            && $this->day->isIn($dateTime->day)
            && $this->weekDay->isIn($weekDay)
        ) {
            return true;
        }

        return false;
    }

    private function isInTime(DateTime $dateTime): bool
    {
        $isInMinute = $dateTime->minute !== null ? $this->minute->isIn($dateTime->minute) : true;
        $isInHour = $dateTime->hour !== null ? $this->hour->isIn($dateTime->hour) : true;

        return $isInMinute && $isInHour;
    }
}
