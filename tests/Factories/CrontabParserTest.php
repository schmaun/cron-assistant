<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Factories;

use MyHammer\CronAssistant\Factories\CrontabParser;
use MyHammer\CronAssistant\Factories\ScheduleFactory;
use MyHammer\CronAssistant\Model\Cron;
use MyHammer\CronAssistant\Model\Crontab;
use MyHammer\CronAssistant\Model\CrontabLine;
use PHPUnit\Framework\TestCase;

class CrontabParserTest extends TestCase
{
    public function testParseContent(): void
    {
        $crontabContent = <<<CRONTAB
*/30  * *   * * www-data /usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d) | /usr/bin/awk '{ print $1 }') -lt '512' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*
*/5  * * * * www-data   /usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d) | /usr/bin/awk '{ print $1 }') -lt '512' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*

#*/5  * * * * www-data   /usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d) | /usr/bin/awk '{ print $1 }') -lt '512' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*

3 2-5 1,2,3 * *    rm -rf /
CRONTAB;

        $originalLine1 = '*/30  * *   * * www-data /usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d) | /usr/bin/awk \'{ print $1 }\') -lt \'512\' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*';
        $originalLine2 = '*/5  * * * * www-data   /usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d) | /usr/bin/awk \'{ print $1 }\') -lt \'512\' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*';
        $originalLine4 = '3 2-5 1,2,3 * *    rm -rf /';

        $expectedLines = [];
        $expectedLineNumber = 0;
        $line = new CrontabLine();
        $line->setOriginalLine($originalLine1);
        $line->setLineNumber($expectedLineNumber++);
        $line->setCron(CrontabParser::parseCron($originalLine1));
        $expectedLines[] = $line;

        $line = new CrontabLine();
        $line->setOriginalLine($originalLine2);
        $line->setLineNumber($expectedLineNumber++);
        $line->setCron(CrontabParser::parseCron($originalLine2));
        $expectedLines[] = $line;

        $line = new CrontabLine();
        $line->setLineNumber($expectedLineNumber++);
        $expectedLines[] = $line;

        $line = new CrontabLine();
        $line->setLineNumber($expectedLineNumber++);
        $line->setOriginalLine('#*/5  * * * * www-data   /usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d) | /usr/bin/awk \'{ print $1 }\') -lt \'512\' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*');
        $expectedLines[] = $line;

        $line = new CrontabLine();
        $line->setLineNumber($expectedLineNumber++);
        $expectedLines[] = $line;

        $line = new CrontabLine();
        $line->setOriginalLine($originalLine4);
        $line->setLineNumber($expectedLineNumber);
        $line->setCron(CrontabParser::parseCron($originalLine4));
        $expectedLines[] = $line;

        $expectedCrontab = new Crontab();
        $lineNumber = 0;
        foreach ($expectedLines as $line) {
            $expectedCrontab->addCrontabLine($lineNumber++, $line);
        }

        $actualCrontab = CrontabParser::parseContent($crontabContent);
        $this->assertEquals($expectedCrontab, $actualCrontab);
    }

    /**
     * @dataProvider provideCronlines
     * @param string $cronLine
     * @param Cron $expectedCron
     */
    public function testParseCron(string $cronLine, Cron $expectedCron): void
    {
        $actualCron = CrontabParser::parseCron($cronLine);
        $this->assertEquals($expectedCron, $actualCron);
    }

    public function provideCronlines(): array
    {
        $longLine = (new Cron())
            ->setUser('www-data')
            ->setCommand("/usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d) | /usr/bin/awk '{ print $1 }') -lt '512' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*")
            ->setMinute(ScheduleFactory::parse('*'))
            ->setHour(ScheduleFactory::parse('*'))
            ->setDay(ScheduleFactory::parse('*'))
            ->setMonth(ScheduleFactory::parse('*'))
            ->setWeekDay(ScheduleFactory::parse('*'));

        $longLineWithSomeTabs = clone $longLine;
        $longLineWithSomeTabs->setCommand("/usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)       | /usr/bin/awk '{ print $1 }') -lt '512' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*");

        $longLineWithSomeTabsWithoutUser = (new Cron())
            ->setCommand("/usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)       | /usr/bin/awk '{ print $1 }') -lt '512' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*")
            ->setMinute(ScheduleFactory::parse('*'))
            ->setHour(ScheduleFactory::parse('*'))
            ->setDay(ScheduleFactory::parse('*'))
            ->setMonth(ScheduleFactory::parse('*'))
            ->setWeekDay(ScheduleFactory::parse('*'));

        $simpleLine = (new Cron())
            ->setUser('root')
            ->setCommand('/usr/bin/mail -s "spam"')
            ->setMinute(ScheduleFactory::parse('*'))
            ->setHour(ScheduleFactory::parse('*'))
            ->setDay(ScheduleFactory::parse('*'))
            ->setMonth(ScheduleFactory::parse('*'))
            ->setWeekDay(ScheduleFactory::parse('*'));

        $shortLineWithNamedMonths = (new Cron())
            ->setUser('root')
            ->setCommand('/usr/bin/mail -s "spam"')
            ->setMinute(ScheduleFactory::parse('*'))
            ->setHour(ScheduleFactory::parse('*'))
            ->setDay(ScheduleFactory::parse('*'))
            ->setMonth(ScheduleFactory::parse('JAN-DEC'))
            ->setWeekDay(ScheduleFactory::parse('*'));

        $shortLineWithNamedDays = (new Cron())
            ->setUser('root')
            ->setCommand('/usr/bin/mail -s "spam"')
            ->setMinute(ScheduleFactory::parse('*'))
            ->setHour(ScheduleFactory::parse('*'))
            ->setDay(ScheduleFactory::parse('*'))
            ->setMonth(ScheduleFactory::parse('JAN-DEC'))
            ->setWeekDay(ScheduleFactory::parse('SUN-SAT'));

        return [
            [
                "* * * * * www-data /usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d) | /usr/bin/awk '{ print $1 }') -lt '512' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*",
                $longLine,
            ],

            [
                "* *         * * *  www-data    /usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)       | /usr/bin/awk '{ print $1 }') -lt '512' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*",
                $longLineWithSomeTabs,
            ],

            [
                "* * * * *      /usr/bin/test $(/usr/bin/du -sm /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)       | /usr/bin/awk '{ print $1 }') -lt '512' || /bin/rm -rf /var/www/releases/orchestra/shared/app/logs/details/$(date +%Y-%m/%d)/*",
                $longLineWithSomeTabsWithoutUser,
            ],

            [
                '* * * * * root /usr/bin/mail -s "spam"',
                $simpleLine,
            ],

            [
                '* * * JAN-DEC * root /usr/bin/mail -s "spam"',
                $shortLineWithNamedMonths,
            ],

            [
                '* * * JAN-DEC SUN-SAT root /usr/bin/mail -s "spam"',
                $shortLineWithNamedDays,
            ],
        ];
    }

    /**
     * @dataProvider provideCronlinesWithError
     * @param string $cronLine
     * @param Cron $expectedCron
     */
    public function testParseCron_errors(string $cronLine): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parsing error: "' . $cronLine . '"');
        CrontabParser::parseCron($cronLine);
    }

    public function provideCronlinesWithError(): array
    {
        return [
            ['* * * usr/bin/mail -s "spam"'],
            ['* * * * * * * * root /usr/bin/mail -s "spam"'],
            ['* * * * *'],
            ['a * * * * root /usr/bin/mail -s "spam"'],
            ['* b * * * root /usr/bin/mail -s "spam"'],
            ['* * c * * root /usr/bin/mail -s "spam"'],
            ['* * * d * root /usr/bin/mail -s "spam"'],
            ['* * * * e root /usr/bin/mail -s "spam"'],
            ['/3-wfe2 * * * * root /usr/bin/mail -s "spam"'],
        ];
    }
}

