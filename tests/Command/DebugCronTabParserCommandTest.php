<?php

namespace MyHammer\CronAssistant\Tests\Command;

use MyHammer\CronAssistant\Command\DebugCronTabParserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DebugCronTabParserCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $path = __DIR__. DIRECTORY_SEPARATOR . 'cron.d-with-invalid-files';

        $commandTester = $this->createCommandTester($path);

        $output = $commandTester->getDisplay();

        $this->assertContains("2 files couldn't be parsed.", $output);
        $this->assertContains('RuntimeException: Could not parse: "this"', $output);
        $this->assertContains('Next RuntimeException: Parsing error: "this is not a valid cronfile and should be ignored."', $output);
    }

    public function testExecute_noParseErrors(): void
    {
        $path = __DIR__. DIRECTORY_SEPARATOR . 'cron.d';

        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new DebugCronTabParserCommand());

        $command = $application->find('cronjobs:debug:crontab-parser');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'path' => $path,
        ));

        $output = $commandTester->getDisplay();

        $this->assertContains('No errors!', $output);
    }

    public function testExecute_emptyDir(): void
    {
        $path = __DIR__. DIRECTORY_SEPARATOR . 'cron.d-empty';

        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new DebugCronTabParserCommand());

        $command = $application->find('cronjobs:debug:crontab-parser');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'path' => $path,
        ));

        $output = $commandTester->getDisplay();

        $this->assertContains('No errors!', $output);
    }

    /**
     * @param string $path
     * @return CommandTester
     */
    private function createCommandTester(string $path): CommandTester
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new DebugCronTabParserCommand());

        $command = $application->find('cronjobs:debug:crontab-parser');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'path' => $path,
            )
        );

        return $commandTester;
    }
}
