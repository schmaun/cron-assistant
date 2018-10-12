<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Model\ValueObject;

use MyHammer\CronAssistant\Model\ValueObject\SteppedRangeSchedule;
use PHPUnit\Framework\TestCase;

class SteppedRangeScheduleTest extends TestCase
{
    /**
     * @return array
     */
    public function provideValueIsIn(): array
    {
        return [
            [new SteppedRangeSchedule(0, 1, 1), 0],
            [new SteppedRangeSchedule(0, 1, 1), 1],
            [new SteppedRangeSchedule(1, 3, 1), 1],
            [new SteppedRangeSchedule(1, 3, 1), 2],
            [new SteppedRangeSchedule(1, 3, 1), 3],
            [new SteppedRangeSchedule(0, 9, 1), 5],
            [new SteppedRangeSchedule(8, 9, 1), 9],
            [new SteppedRangeSchedule(9, 9, 1), 9],
            [new SteppedRangeSchedule(-1, 1, 1), -1],
            [new SteppedRangeSchedule(-1, 1, 1), 0],
            [new SteppedRangeSchedule(-1, 1, 1), 1],
            [new SteppedRangeSchedule(0, PHP_INT_MAX, 1), PHP_INT_MAX],


            [new SteppedRangeSchedule(0, 5, 2), 0],
            [new SteppedRangeSchedule(0, 5, 2), 2],
            [new SteppedRangeSchedule(0, 5, 2), 4],


            [new SteppedRangeSchedule(3, 42, 5), 3],
            [new SteppedRangeSchedule(3, 42, 5), 8],
            [new SteppedRangeSchedule(3, 42, 5), 13],
            [new SteppedRangeSchedule(3, 42, 5), 38],

            [new SteppedRangeSchedule(3, 20, 5), 18],
            [new SteppedRangeSchedule(3, 23, 5), 23],
        ];
    }

    /**
     * @dataProvider provideValueIsIn
     * @param SteppedRangeSchedule $schedule
     * @param int $testValue
     */
    public function testIsIn_valueIsIn(SteppedRangeSchedule $schedule, int $testValue): void
    {
        $this->assertTrue($schedule->isIn($testValue));
    }

    /**
     * @return array
     */
    public function provideValueIsNotIn(): array
    {
        return [
            [new SteppedRangeSchedule(0, 1, 1), 2],
            [new SteppedRangeSchedule(0, 1, 1), 3],
            [new SteppedRangeSchedule(1, 3, 1), -1],
            [new SteppedRangeSchedule(1, 3, 1), 0],
            [new SteppedRangeSchedule(1, 3, 1), 4],
            [new SteppedRangeSchedule(-1, 1, 1), -2],
            [new SteppedRangeSchedule(-1, 1, 1), 2],
            [new SteppedRangeSchedule(0, PHP_INT_MAX - 1, 1), PHP_INT_MAX],


            [new SteppedRangeSchedule(0, 5, 2), 1],
            [new SteppedRangeSchedule(0, 5, 2), 3],
            [new SteppedRangeSchedule(0, 5, 2), 5],


            [new SteppedRangeSchedule(3, 42, 5), 1],
            [new SteppedRangeSchedule(3, 42, 5), 2],
            [new SteppedRangeSchedule(3, 42, 5), 4],
            [new SteppedRangeSchedule(3, 42, 5), 5],
            [new SteppedRangeSchedule(3, 42, 5), 6],
            [new SteppedRangeSchedule(3, 42, 5), 7],
            [new SteppedRangeSchedule(3, 42, 5), 9],
            [new SteppedRangeSchedule(3, 42, 5), 10],
            [new SteppedRangeSchedule(3, 42, 5), 11],
            [new SteppedRangeSchedule(3, 42, 5), 12],
            [new SteppedRangeSchedule(3, 42, 5), 14],
            [new SteppedRangeSchedule(3, 42, 5), 37],
            [new SteppedRangeSchedule(3, 42, 5), 39],
            [new SteppedRangeSchedule(3, 42, 5), 40],
            [new SteppedRangeSchedule(3, 42, 5), 41],
            [new SteppedRangeSchedule(3, 42, 5), 42],
        ];
    }

    /**
     * @dataProvider provideValueIsNotIn
     * @param SteppedRangeSchedule $schedule
     * @param int $testValue
     */
    public function testIsIn_valueIsNotIn(SteppedRangeSchedule $schedule, int $testValue): void
    {
        $this->assertFalse($schedule->isIn($testValue));
    }

    /**
     * @return array
     */
    public function provideValuesAndString(): array
    {
        return [
            [new SteppedRangeSchedule(0, 1, 1), '0-1/1'],
            [new SteppedRangeSchedule(1, 3, 1), '1-3/1'],
            [new SteppedRangeSchedule(0, 9, 1), '0-9/1'],
            [new SteppedRangeSchedule(8, 9, 1), '8-9/1'],
            [new SteppedRangeSchedule(49, 59, 1), '49-59/1'],
            [new SteppedRangeSchedule(-1, 1, 1), '-1-1/1'],
            [new SteppedRangeSchedule(0, PHP_INT_MAX, 1), '0-' . PHP_INT_MAX . '/1'],
            [new SteppedRangeSchedule(0, 5, 2), '0-5/2'],
            [new SteppedRangeSchedule(3, 42, 5), '3-42/5'],
            [new SteppedRangeSchedule(3, 23, 5), '3-23/5'],
        ];
    }

    /**
     * @dataProvider provideValuesAndString
     * @param SteppedRangeSchedule $schedule
     * @param string $expectedString
     */
    public function testToString(SteppedRangeSchedule $schedule, string $expectedString): void
    {
        $this->assertEquals($expectedString, $schedule->__toString());
    }
}
