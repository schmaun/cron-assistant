#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use MyHammer\CronAssistant\Command\CronJobsRunningAtCommand;
use Symfony\Component\Console\Application;

$command = new CronJobsRunningAtCommand();

$application = new Application();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();
