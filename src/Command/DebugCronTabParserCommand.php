<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Command;

use MyHammer\CronAssistant\Factories\CrontabParser;
use MyHammer\CronAssistant\Utils\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugCronTabParserCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;

    protected function configure(): void
    {
        $this->setName('cronjobs:debug:crontab-parser')
            ->setDescription('Helps to debug the parser.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to where the crontab files live.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $errorCounter = 0;

        foreach (Filesystem::findFiles($input->getArgument('path')) as $file) {
            try {
                CrontabParser::parseContent($file->getContents());
            } catch (\RuntimeException $exception) {
                $output->writeln($file->getRealPath());
                $output->writeln($exception);
                $output->writeln('---------------------------------------------');
                $output->writeln('');
                $errorCounter++;
            }
        }

        if ($errorCounter > 0) {
            $output->writeln(sprintf("<error>%d files couldn't be parsed.</error>", $errorCounter));
        } else {
            $output->writeln('<info>No errors!</info>');
        }
    }
}