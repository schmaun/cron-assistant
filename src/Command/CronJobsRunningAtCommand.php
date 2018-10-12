<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Command;

use MyHammer\CronAssistant\Factories\CrontabParser;
use MyHammer\CronAssistant\Factories\DateTimeParser;
use MyHammer\CronAssistant\Model\CrontabLine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class CronJobsRunningAtCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;

    protected function configure(): void
    {
        $this->setName('cronjobs:running-at')
            ->setDescription('Tells you which crons would run at the given time.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'Path to where the crontab files live.')
            ->addArgument('dateTime', InputArgument::REQUIRED,
                'The date and/or time in question. You can aks for a date, a date and hour or a date and hour+minutes. Format: YYYY-MM-DD [HH][:MM]');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        try {
            $time = DateTimeParser::parse($input->getArgument('dateTime'));
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<error>Could not parse date/time.</error>');

            return;
        }

        $finder = new Finder();
        $finder->files()->in($input->getOption('path'))->sortByName()->depth(0);

        foreach ($finder as $file) {
            try {
                $crontabFile = CrontabParser::parseContent($file->getContents());
                foreach ($crontabFile as $lineNumber => $cronTabLine) {
                    /** @var CrontabLine $cronTabLine */
                    if ($cronTabLine->isRunningAt($time)) {
                        $this->outputIsRunningAt($file->getRealPath(), $lineNumber, $cronTabLine);
                    }
                }
            } catch (\RuntimeException $exception) {
                $output->writeln(sprintf("<info>Ignoring %s. Doesn't look like a crontab file</info>",
                    $file->getRealPath()));
            }

        }
    }

    /**
     * @param string $filename
     * @param int $lineNumber
     * @param CrontabLine $cronTabLine
     */
    private function outputIsRunningAt(string $filename, int $lineNumber, CrontabLine $cronTabLine): void
    {
        $this->output->writeln(sprintf('%s (%s): %s', $filename, $lineNumber, $cronTabLine->getOriginalLine()));
    }
}