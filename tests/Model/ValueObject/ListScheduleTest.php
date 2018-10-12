<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Model\ValueObject;

use MyHammer\CronAssistant\Model\ValueObject\ListSchedule;
use PHPUnit\Framework\TestCase;

class ListScheduleTest extends TestCase
{
    /**
     * @return array
     */
    public function provideValueIsIn(): array
    {
        return [
            [[1, 2, 3], 1],
            [[1, 2, 3], 2],
            [[1, 2, 3], 3],
            [[0, 4, 8], 0],
            [[0, 4, 8], 4],
            [[0, 4, 8], 8],
            [[0, 4, PHP_INT_MAX], PHP_INT_MAX],
        ];
    }

    /**
     * @dataProvider provideValueIsIn
     * @param array $values
     * @param int $testValue
     */
    public function testIsIn_valueIsIn(array $values, int $testValue): void
    {
        $listSchedule = new ListSchedule($values);
        $this->assertTrue($listSchedule->isIn($testValue));
    }

    /**
     * @return array
     */
    public function provideValueIsNotIn(): array
    {
        return [
            [[1, 2, 3], 0],
            [[1, 2, 3], 4],
            [[1, 2, 3], 9],
            [[0, 4, 8], 1],
            [[0, 4, 8], 2],
            [[0, 4, 8], 7],
            [[0, 4, PHP_INT_MAX - 1], PHP_INT_MAX],
        ];
    }

    /**
     * @dataProvider provideValueIsNotIn
     * @param array $values
     * @param int $testValue
     */
    public function testIsIn_valueIsNotIn(array $values, int $testValue): void
    {
        $listSchedule = new ListSchedule($values);
        $this->assertFalse($listSchedule->isIn($testValue));
    }

    /**
     * @return array
     */
    public function provideValuesAndString(): array
    {
        return [
            [[1, 2, 3], '1,2,3'],
            [[0, 4, 8], '0,4,8'],
            [[0], '0'],
            [[99], '99'],
            [[0, PHP_INT_MAX], '0,' . PHP_INT_MAX],
        ];
    }

    /**
     * @dataProvider provideValuesAndString
     * @param array $values
     * @param string $expectedString
     */
    public function testToString(array $values, string $expectedString): void
    {
        $schedule = new ListSchedule($values);
        $this->assertEquals($expectedString, $schedule->__toString());
    }
}
