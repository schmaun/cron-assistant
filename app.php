#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use MyHammer\CronAssistant\Command\CronJobsRunningAtCommand;
use MyHammer\CronAssistant\Command\DebugCronTabParserCommand;
use Symfony\Component\Console\Application;

$command = new CronJobsRunningAtCommand();

$application = new Application();
$application->add($command);
$application->add(new DebugCronTabParserCommand());
$application->setDefaultCommand($command->getName());

$application->run();
