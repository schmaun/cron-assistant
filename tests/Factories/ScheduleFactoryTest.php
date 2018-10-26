<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Factories;

use MyHammer\CronAssistant\Factories\ScheduleFactory;
use MyHammer\CronAssistant\Model\ValueObject\AnySchedule;
use MyHammer\CronAssistant\Model\ValueObject\ListSchedule;
use MyHammer\CronAssistant\Model\ValueObject\RangeSchedule;
use MyHammer\CronAssistant\Model\ValueObject\Schedule;
use MyHammer\CronAssistant\Model\ValueObject\SteppedAnySchedule;
use MyHammer\CronAssistant\Model\ValueObject\SteppedRangeSchedule;
use PHPUnit\Framework\TestCase;

class ScheduleFactoryTest extends TestCase
{
    /**
     * @dataProvider provideValuesAndSchedules
     * @param string $value
     * @param Schedule $expected
     */
    public function testParse(string $value, Schedule $expected): void
    {
        $this->assertEquals($expected, ScheduleFactory::parse($value));
    }

    /**
     * @return array
     */
    public function provideValuesAndSchedules(): array
    {
        return [
            ['*', new AnySchedule()],
            ['0', new ListSchedule([0])],
            ['02', new ListSchedule([2])],
            ['2', new ListSchedule([2])],
            ['2,3,4,5', new ListSchedule([2, 3, 4, 5])],
            ['0,3,4,5', new ListSchedule([0, 3, 4, 5])],
            ['2-5', new RangeSchedule(2, 5)],
            ['0-5', new RangeSchedule(0, 5)],
            ['2-5/2', new SteppedRangeSchedule(2, 5, 2)],
            ['0-5/2', new SteppedRangeSchedule(0, 5, 2)],
            ['0-0/2', new SteppedRangeSchedule(0, 0, 2)],
            ['0-24/2', new SteppedRangeSchedule(0, 24, 2)],
            ['MON-FRI/2', new SteppedRangeSchedule(1, 5, 2)],
            ['MON,FRI', new ListSchedule([1, 5])],
            ['MON,FRI,SAT', new ListSchedule([1, 5,6])],
            ['*/1', new SteppedAnySchedule(1)],
            ['*/2', new SteppedAnySchedule(2)],
            ['*/99', new SteppedAnySchedule(99)],
        ];
    }

    /**
     * @dataProvider provideToBeNormalizedValuesAndListSchedules
     * @param string $value
     * @param ListSchedule $expected
     */
    public function testParse_ListScheduleNormalizing(string $value, ListSchedule $expected): void
    {
        /** @var ListSchedule $actual */
        $actual = ScheduleFactory::parse($value);

        $this->assertEquals($expected, $actual);
        $this->assertSame($expected->getValues(), $actual->getValues());
    }

    /**
     * @return array
     */
    public function provideToBeNormalizedValuesAndListSchedules(): array
    {
        return [
            ['0', new ListSchedule([0])],
            ['02', new ListSchedule([2])],
            ['2', new ListSchedule([2])],
            ['2,3,4,5', new ListSchedule([2, 3, 4, 5])],
            ['0,3,4,5', new ListSchedule([0, 3, 4, 5])],
            ['MON,FRI', new ListSchedule([1, 5])],
            ['MON,FRI,SAT', new ListSchedule([1, 5,6])],
        ];
    }

    /**
     * @dataProvider provideToBeNormalizedValuesAndSteppedAnySchedules
     * @param string $value
     * @param SteppedAnySchedule $expected
     */
    public function testParse_SteppedAnyScheduleNormalizing(string $value, SteppedAnySchedule $expected): void
    {
        /** @var SteppedAnySchedule $actual */
        $actual = ScheduleFactory::parse($value);

        $this->assertEquals($expected, $actual);
        $this->assertSame($expected->getStep(), $actual->getStep());
    }

    /**
     * @return array
     */
    public function provideToBeNormalizedValuesAndSteppedAnySchedules(): array
    {
        return [
            ['*/1', new SteppedAnySchedule(1)],
            ['*/2', new SteppedAnySchedule(2)],
            ['*/99', new SteppedAnySchedule(99)],
        ];
    }

    /**
     * @dataProvider provideToBeNormalizedValuesAndRangeSchedules
     * @param string $value
     * @param RangeSchedule $expected
     */
    public function testParse_RangeScheduleNormalizing(string $value, RangeSchedule $expected): void
    {
        /** @var RangeSchedule $actual */
        $actual = ScheduleFactory::parse($value);

        $this->assertEquals($expected, $actual);
        $this->assertSame($expected->getMin(), $actual->getMin());
        $this->assertSame($expected->getMax(), $actual->getMax());
    }

    /**
     * @return array
     */
    public function provideToBeNormalizedValuesAndRangeSchedules(): array
    {
        return [
            ['2-5', new RangeSchedule(2, 5)],
            ['0-5', new RangeSchedule(0, 5)],
        ];
    }


    /**
     * @dataProvider provideToBeNormalizedValuesAndSteppedRangeSchedules
     * @param string $value
     * @param SteppedRangeSchedule $expected
     */
    public function testParse_SteppedRangeScheduleNormalizing(string $value, SteppedRangeSchedule $expected): void
    {
        /** @var SteppedRangeSchedule $actual */
        $actual = ScheduleFactory::parse($value);

        $this->assertEquals($expected, $actual);
        $this->assertSame($expected->getMin(), $actual->getMin());
        $this->assertSame($expected->getMax(), $actual->getMax());
        $this->assertSame($expected->getStep(), $actual->getStep());
    }

    /**
     * @return array
     */
    public function provideToBeNormalizedValuesAndSteppedRangeSchedules(): array
    {
        return [
            ['2-5/2', new SteppedRangeSchedule(2, 5, 2)],
            ['0-5/2', new SteppedRangeSchedule(0, 5, 2)],
            ['0-0/2', new SteppedRangeSchedule(0, 0, 2)],
            ['0-24/2', new SteppedRangeSchedule(0, 24, 2)],
            ['MON-FRI/2', new SteppedRangeSchedule(1, 5, 2)],
        ];
    }

    /**
     * @dataProvider provideInvalidValues
     * @param mixed $value
     */
    public function testParse_notParseable($value): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Could not parse: "%s"', $value));
        ScheduleFactory::parse($value);
    }

    /**
     * @return array
     */
    public function provideInvalidValues(): array
    {
        return [
            ['foo'],
            [','],
            [',1'],
            ['1,'],
            ['1-'],
            ['1-/'],
            ['-1'],
            ['false'],
            ['true'],
            ['23.5'],
            ['/'],
            ['2/'],
            ['2/*'],
            ['*/*'],
            ['FOO'],
            ['MOZ'],
            ['FOO-BAR'],
            ['FOO,BAR'],
        ];
    }

    /**
     * @dataProvider provideNamedValuesAndSchedules
     * @param string $value
     * @param Schedule $expected
     */
    public function testParse_namedValues(string $value, Schedule $expected)
    {
        $this->assertEquals($expected, ScheduleFactory::parse($value));
    }

    public function provideNamedValuesAndSchedules(): array
    {
        return [
            ['SUN', new ListSchedule([0])],
            ['MON', new ListSchedule([1])],
            ['TUE', new ListSchedule([2])],
            ['WEN', new ListSchedule([3])],
            ['THU', new ListSchedule([4])],
            ['FRI', new ListSchedule([5])],
            ['SAT', new ListSchedule([6])],

            ['JAN', new ListSchedule([1])],
            ['FEB', new ListSchedule([2])],
            ['MAR', new ListSchedule([3])],
            ['APR', new ListSchedule([4])],
            ['MAY', new ListSchedule([5])],
            ['JUN', new ListSchedule([6])],
            ['JUL', new ListSchedule([7])],
            ['AUG', new ListSchedule([8])],
            ['SEP', new ListSchedule([9])],
            ['OCT', new ListSchedule([10])],
            ['NOV', new ListSchedule([11])],
            ['DEC', new ListSchedule([12])],
        ];
    }
}
