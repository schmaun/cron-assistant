<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Model\ValueObject;

use MyHammer\CronAssistant\Model\ValueObject\AnySchedule;
use PHPUnit\Framework\TestCase;

class AnyScheduleTest extends TestCase
{
    /**
     * @return array
     * @throws \Exception
     */
    public function provideValue(): array
    {
        return [
            [0],
            [1],
            [2],
            [PHP_INT_MAX],
            [random_int(0, PHP_INT_MAX)],
        ];
    }

    /**
     * @dataProvider provideValue
     * @param int $value
     */
    public function testIsIn(int $value): void
    {
        $schedule = new AnySchedule();
        $this->assertTrue($schedule->isIn($value));
    }

    public function testToString(): void
    {
        $schedule = new AnySchedule();
        $this->assertEquals('*', $schedule->__toString());
    }
}
