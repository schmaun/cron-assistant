<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Model;

use MyHammer\CronAssistant\Factories\CrontabParser;
use MyHammer\CronAssistant\Model\Cron;
use MyHammer\CronAssistant\Model\ValueObject\AnySchedule;
use MyHammer\CronAssistant\Model\ValueObject\DateTime;
use MyHammer\CronAssistant\Model\ValueObject\ListSchedule;
use MyHammer\CronAssistant\Model\ValueObject\RangeSchedule;
use MyHammer\CronAssistant\Model\ValueObject\Schedule;
use MyHammer\CronAssistant\Model\ValueObject\SteppedAnySchedule;
use MyHammer\CronAssistant\Model\ValueObject\SteppedRangeSchedule;
use PHPUnit\Framework\TestCase;

class CronTest extends TestCase
{
    /**
     * @dataProvider provideValidMinutes
     * @param Schedule $schedule
     */
    public function testSetMinute_valid(Schedule $schedule): void
    {
        $cron = new Cron();
        $cron->setMinute($schedule);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provideInvalidMinutes
     * @param Schedule $schedule
     */
    public function testSetMinute_inValid(Schedule $schedule): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $cron = new Cron();
        $cron->setMinute($schedule);
    }

    /**
     * @dataProvider provideValidHours
     * @param Schedule $schedule
     */
    public function testSetHour_valid(Schedule $schedule): void
    {
        $cron = new Cron();
        $cron->setHour($schedule);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provideInvalidHours
     * @param Schedule $schedule
     */
    public function testSetHour_inValid(Schedule $schedule): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $cron = new Cron();
        $cron->setHour($schedule);
    }


    /**
     * @dataProvider provideValidDays
     * @param Schedule $schedule
     */
    public function testSetDay_valid(Schedule $schedule): void
    {
        $cron = new Cron();
        $cron->setDay($schedule);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provideInvalidDays
     * @param Schedule $schedule
     */
    public function testSetDay_inValid(Schedule $schedule): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $cron = new Cron();
        $cron->setDay($schedule);
    }


    /**
     * @dataProvider provideValidMonths
     * @param Schedule $schedule
     */
    public function testSetMonth_valid(Schedule $schedule): void
    {
        $cron = new Cron();
        $cron->setMonth($schedule);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provideInvalidMonths
     * @param Schedule $schedule
     */
    public function testSetMonth_inValid(Schedule $schedule): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $cron = new Cron();
        $cron->setMonth($schedule);
    }

    /**
     * @dataProvider provideValidWeekDays
     * @param Schedule $schedule
     */
    public function testSetWeekDay_valid(Schedule $schedule): void
    {
        $cron = new Cron();
        $cron->setWeekDay($schedule);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provideInvalidWeekDays
     * @param Schedule $schedule
     */
    public function testSetWeekDay_inValid(Schedule $schedule): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $cron = new Cron();
        $cron->setWeekDay($schedule);
    }

    public function provideValidMinutes(): array
    {
        return $this->createValidSchedules(0, 59);
    }
    public function provideInvalidMinutes(): array
    {
        return $this->createSchedules(61, 62);
    }


    public function provideValidHours(): array
    {
        return $this->createValidSchedules(0, 23);
    }
    public function provideInvalidHours(): array
    {
        return $this->createSchedules(24, 60);
    }


    public function provideValidDays(): array
    {
        return $this->createValidSchedules(1, 31);
    }
    public function provideInvalidDays(): array
    {
        return $this->createSchedules(32, 60);
    }


    public function provideValidMonths(): array
    {
        return $this->createValidSchedules(1, 12);
    }
    public function provideInvalidMonths(): array
    {
        return $this->createSchedules(13, 60);
    }


    public function provideValidWeekDays(): array
    {
        return $this->createValidSchedules(0, 6);
    }

    public function provideInvalidWeekDays(): array
    {
        return $this->createSchedules(7, 60);
    }

    /**
     * @param int $min
     * @param int $max
     * @return array
     * @throws \Exception
     */
    private function createValidSchedules(int $min, int $max): array
    {
        return array_merge(
            [[new AnySchedule()]],
            $this->createSchedules($min, $max)
        );
    }

    /**
     * @param int $min
     * @param int $max
     * @return array
     * @throws \Exception
     */
    protected function createSchedules(int $min, int $max): array
    {
        return [
            [new SteppedAnySchedule($min)],
            [new SteppedAnySchedule($max)],
            [new SteppedAnySchedule(random_int($min, $max))],

            [new ListSchedule([$min])],
            [new ListSchedule([$max])],
            [new ListSchedule([$min, $max])],
            [
                new ListSchedule([
                    random_int($min, $min + 2),
                    random_int($min, $min),
                    random_int($max - 2, $max),
                ]),
            ],
            [new ListSchedule(range($min, $max))],

            [new RangeSchedule($min, $max)],
            [new RangeSchedule($min + 1, $min + 1)],
            [new RangeSchedule(random_int($min, $max), random_int($min, $max))],

            [new SteppedRangeSchedule($min, $max, 1)],
            [new SteppedRangeSchedule($min, $max, $max)],
            [new SteppedRangeSchedule(random_int($min, $max), random_int($min, $max), random_int($min, $max))],
        ];
    }

    /**
     * @dataProvider provideIsInDateTimeAndSchedule
     * @param DateTime $dateTime
     * @param string $schedule
     */
    public function testIsIn(DateTime $dateTime, string $schedule)
    {
        $cron = CrontabParser::parseCron($schedule. ' foo bar');
        $this->assertTrue($cron->isIn($dateTime), sprintf('Asserting %s is in %s', $dateTime, $schedule));
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function provideIsInDateTimeAndSchedule(): array
    {
        return [
            [new DateTime(2018, 01, 01), '* * * * *'],

            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31)), '* * * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31)), '* * 1-31 * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31)), '* * 1-31 1-12 *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31)), '* * 1-31 1-12 0-6'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31)), '* * * 1-12 0-6'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31)), '* * 1-31 * 0-6'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31)), '* * * * 0-6'],

            [new DateTime(2017, 12, 31), '* * * * 0'],
            [new DateTime(2018, 01, 01), '* * * * 1'],
            [new DateTime(2018, 01, 02), '* * * * 2'],
            [new DateTime(2018, 01, 03), '* * * * 3'],
            [new DateTime(2018, 01, 04), '* * * * 4'],
            [new DateTime(2018, 01, 05), '* * * * 5'],
            [new DateTime(2018, 01, 06), '* * * * 6'],

            [new DateTime(2018, 03, 04), '* * * * 0'],
            [new DateTime(2018, 03, 05), '* * * * 1'],
            [new DateTime(2018, 03, 06), '* * * * 2'],
            [new DateTime(2018, 03, 07), '* * * * 3'],
            [new DateTime(2018, 03,  8), '* * * * 4'],
            [new DateTime(2018, 03,  9), '* * * * 5'],
            [new DateTime(2018, 03, 10), '* * * * 6'],

            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24)), '* * * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24), random_int(1, 60)), '* * * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24), random_int(1, 60)), '* * 1-31 * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24)), '* * 1-31 1-12 *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24), random_int(1, 60)), '* * 1-31 1-12 0-6'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24)), '* * * 1-12 0-6'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24), random_int(1, 60)), '* * 1-31 * 0-6'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24)), '* * * * 0-6'],

            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24), random_int(5, 15)), '5-15 * * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(2, 4),  random_int(5, 15)), '5-15 2-4 * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24), random_int(5, 15)), '5,6,7,8,9,10,11,12,13,14,15 * * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(2, 4),  random_int(5, 15)), '5,6,7,8,9,10,11,12,13,14,15 2,3,4 * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24), 5), '5-15/2 * * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(1, 24), 7), '5-15/2 * * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(2, 4),  5), '5-15/2 2-4 * * *'],
            [new DateTime(random_int(1, 3000), random_int(1, 12), random_int(1, 31), random_int(2, 4),  7), '5-15/2 2-4 * * *'],
        ];
    }
}
