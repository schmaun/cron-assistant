<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Factories;

use MyHammer\CronAssistant\Factories\DateTimeParser;
use MyHammer\CronAssistant\Model\ValueObject\DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeParserTest extends TestCase
{
    /**
     * @dataProvider provideValidDates
     * @param string $dateTime
     * @param DateTime $expectedDateTime
     */
    public function testParse_valid(string $dateTime, DateTime $expectedDateTime): void
    {
        $actual = DateTimeParser::parse($dateTime);
        $this->assertEquals($expectedDateTime, $actual);
    }

    /**
     * @dataProvider provideInvalidDates
     * @param string $dateTime
     */
    public function testParse_invalid(string $dateTime): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DateTimeParser::parse($dateTime);
    }

    /**
     * @return array
     */
    public function provideInvalidDates(): array
    {
        return [
            ['foo'],
            ['2018'],
            ['2018-02'],
            ['2018-02-2'],
            ['2018-2-2'],
            ['208-01-01'],
            ['01-01-01'],
            ['fo-0b-ar'],
            ['2018-01-01 1'],
            ['2018-01-01 111'],
            ['2018-01-01 11:1'],
            ['2018-01-01 11:111'],
        ];
    }

    public function provideValidDates()
    {
        return [
            ['2018-01-02', new DateTime(2018, 01, 02)],
            ['2018-01-02 03', new DateTime(2018, 01, 02, 03)],
            ['2018-01-02 03:04', new DateTime(2018, 01, 02, 03, 04)],
        ];
    }
}
