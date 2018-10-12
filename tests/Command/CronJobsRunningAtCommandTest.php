<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Tests\Command;

use MyHammer\CronAssistant\Command\CronJobsRunningAtCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CronJobsRunningAtCommandTest extends TestCase
{
    /**
     * @dataProvider provideDateAndOutput
     * @param string $dateTime
     * @param string $expectedOutput
     */
    public function testExecute(string $dateTime, string $expectedOutput): void
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new CronJobsRunningAtCommand());

        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cron.d';

        $command = $application->find('cronjobs:running-at');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            '--path' => $path,
            'dateTime' => $dateTime,
        ));

        $output = $commandTester->getDisplay();

        $this->assertEquals($expectedOutput, $output);
    }

    public function testExecute_directoryEmpty(): void
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new CronJobsRunningAtCommand());

        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cron.d-empty';

        $command = $application->find('cronjobs:running-at');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            '--path' => $path,
            'dateTime' => '2019-09-28',
        ));

        $output = $commandTester->getDisplay();
        $this->assertEmpty($output);
    }

    public function testExecute_invalidAndIgnoredFiles()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new CronJobsRunningAtCommand());

        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cron.d-with-invalid-files';

        $command = $application->find('cronjobs:running-at');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            '--path' => $path,
            'dateTime' => '2019-09-28',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains(sprintf("Ignoring %s/to-be-ignored. Doesn't look like a crontab file", $path), $output);
        $this->assertContains(sprintf("Ignoring %s/to-be-ignored2. Doesn't look like a crontab file", $path), $output);
    }

    public function testExecute_dateNotParsable()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new CronJobsRunningAtCommand());

        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cron.d';

        $command = $application->find('cronjobs:running-at');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            '--path' => $path,
            'dateTime' => '11111',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Could not parse date/time.', $output);
    }

    public function testExecute_wrongPath()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "gibs-net" directory does not exist.');

        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new CronJobsRunningAtCommand());


        $command = $application->find('cronjobs:running-at');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            '--path' => 'gibs-net',
            'dateTime' => '2019-01-01',
        ));
    }

    public function provideDateAndOutput()
    {
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cron.d' . DIRECTORY_SEPARATOR;
        return [
            ['2018-01-01', <<<CRONS
{$path}cronjobs-1 (0): */2 * * * * www-data /usr/bin/test -d
{$path}cronjobs-1 (1): * * * * * root /usr/bin/mail -s "spam"
{$path}cronjobs-2 (0): * * * * * www-data /usr/bin/test -d
{$path}cronjobs-2 (2): 17-29/1 2,4,6 * * * root /usr/bin/mail -s "spam"

CRONS
            ],
            ['2018-01-01 14:33', <<<CRONS
{$path}cronjobs-1 (1): * * * * * root /usr/bin/mail -s "spam"
{$path}cronjobs-2 (0): * * * * * www-data /usr/bin/test -d

CRONS
            ],
            ['2018-01-01 04', <<<CRONS
{$path}cronjobs-1 (0): */2 * * * * www-data /usr/bin/test -d
{$path}cronjobs-1 (1): * * * * * root /usr/bin/mail -s "spam"
{$path}cronjobs-2 (0): * * * * * www-data /usr/bin/test -d
{$path}cronjobs-2 (2): 17-29/1 2,4,6 * * * root /usr/bin/mail -s "spam"

CRONS
            ],
            ['2018-01-01 04:21', <<<CRONS
{$path}cronjobs-1 (1): * * * * * root /usr/bin/mail -s "spam"
{$path}cronjobs-2 (0): * * * * * www-data /usr/bin/test -d
{$path}cronjobs-2 (2): 17-29/1 2,4,6 * * * root /usr/bin/mail -s "spam"

CRONS
            ],
        ];
    }
}
