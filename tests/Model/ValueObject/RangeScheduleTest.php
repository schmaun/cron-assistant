<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Model\ValueObject;

use MyHammer\CronAssistant\Model\ValueObject\RangeSchedule;
use PHPUnit\Framework\TestCase;

class RangeScheduleTest extends TestCase
{
    /**
     * @return array
     */
    public function provideValueIsIn(): array
    {
        return [
            [0, 1, 0],
            [0, 1, 1],
            [0, 9, 5],
            [8, 9, 9],
            [9, 9, 9],
            [-1, 1, -1],
            [-1, 1, 0],
            [-1, 1, 1],
            [0, PHP_INT_MAX, PHP_INT_MAX],
        ];
    }

    /**
     * @dataProvider provideValueIsIn
     * @param int $min
     * @param int $max
     * @param int $testValue
     */
    public function testIsIn_valueIsIn(int $min, int $max, int $testValue): void
    {
        $schedule = new RangeSchedule($min, $max);
        $this->assertTrue($schedule->isIn($testValue));
    }

    /**
     * @return array
     */
    public function provideValueIsNotIn(): array
    {
        return [
            [0, 1, 2],
            [0, 1, -1],
            [10, 11, 9],
            [0, PHP_INT_MAX - 1, PHP_INT_MAX],
        ];
    }

    /**
     * @dataProvider provideValueIsNotIn
     * @param int $min
     * @param int $max
     * @param int $testValue
     */
    public function testIsIn_valueIsNotIn(int $min, int $max, int $testValue): void
    {
        $schedule = new RangeSchedule($min, $max);
        $this->assertFalse($schedule->isIn($testValue));
    }


    /**
     * @return array
     */
    public function provideValuesAndString(): array
    {
        return [
            [0, 1, '0-1'],
            [0, 9, '0-9'],
            [8, 9, '8-9'],
            [9, 9, '9-9'],
            [-1, 1, '-1-1'],
            [0, PHP_INT_MAX, '0-' . PHP_INT_MAX],
        ];
    }

    /**
     * @dataProvider provideValuesAndString
     * @param int $min
     * @param int $max
     * @param string $expectedString
     */
    public function testToString(int $min, int $max, string $expectedString): void
    {
        $schedule = new RangeSchedule($min, $max);
        $this->assertEquals($expectedString, $schedule->__toString());
    }
}
