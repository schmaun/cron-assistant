<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Model\ValueObject;

use MyHammer\CronAssistant\Model\ValueObject\SteppedAnySchedule;
use PHPUnit\Framework\TestCase;

class SteppedAnyScheduleTest extends TestCase
{
    /**
     * @return array
     */
    public function provideValueIsIn(): array
    {
        return [
            [new SteppedAnySchedule(1), 0],
            [new SteppedAnySchedule(1), 1],
            [new SteppedAnySchedule(1), 2],
            [new SteppedAnySchedule(1), 3],
            [new SteppedAnySchedule(1), 60],
            [new SteppedAnySchedule(1), random_int(0, PHP_INT_MAX)],
            [new SteppedAnySchedule(1), random_int(PHP_INT_MIN, PHP_INT_MAX)],


            [new SteppedAnySchedule(2), 0],
            [new SteppedAnySchedule(2), 2],
            [new SteppedAnySchedule(2), 4],
            [new SteppedAnySchedule(2), 60],


            [new SteppedAnySchedule(3), 0],
            [new SteppedAnySchedule(3), 3],
            [new SteppedAnySchedule(3), 6],
            [new SteppedAnySchedule(3), 9],
            [new SteppedAnySchedule(3), 39],

            [new SteppedAnySchedule(5), 5],
            [new SteppedAnySchedule(5), 15],
        ];
    }

    /**
     * @dataProvider provideValueIsIn
     * @param SteppedAnySchedule $schedule
     * @param int $testValue
     */
    public function testIsIn_valueIsIn(SteppedAnySchedule $schedule, int $testValue): void
    {
        $this->assertTrue($schedule->isIn($testValue), print_r($schedule) . ': ' . $testValue);
    }

    /**
     * @return array
     */
    public function provideValueIsNotIn(): array
    {
        return [
            [new SteppedAnySchedule(2), 1],
            [new SteppedAnySchedule(2), 3],
            [new SteppedAnySchedule(2), 5],


            [new SteppedAnySchedule(5), 1],
            [new SteppedAnySchedule(5), 2],
            [new SteppedAnySchedule(5), 4],
            [new SteppedAnySchedule(5), 6],
            [new SteppedAnySchedule(5), 7],
            [new SteppedAnySchedule(5), 9],
            [new SteppedAnySchedule(5), 11],
            [new SteppedAnySchedule(5), 12],
            [new SteppedAnySchedule(5), 14],
            [new SteppedAnySchedule(5), 37],
            [new SteppedAnySchedule(5), 39],
            [new SteppedAnySchedule(5), 41],
            [new SteppedAnySchedule(5), 42],
        ];
    }

    /**
     * @dataProvider provideValueIsNotIn
     * @param SteppedAnySchedule $schedule
     * @param int $testValue
     */
    public function testIsIn_valueIsNotIn(SteppedAnySchedule $schedule, int $testValue): void
    {
        $this->assertFalse($schedule->isIn($testValue), print_r($schedule) . ': ' . $testValue);
    }

    /**
     * @return array
     */
    public function provideValuesForTestToString(): array
    {
        return [
            [0],
            [8],
            [9],
            [-1],
            [PHP_INT_MAX],
        ];
    }

    /**
     * @dataProvider provideValuesForTestToString
     * @param int $step
     */
    public function testToString(int $step): void
    {
        $schedule = new SteppedAnySchedule($step);
        $this->assertEquals('*/' . $step, $schedule->__toString());
    }
}
