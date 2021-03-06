<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Command;

use MyHammer\CronAssistant\Factories\CrontabParser;
use MyHammer\CronAssistant\Factories\DateTimeParser;
use MyHammer\CronAssistant\Utils\Filesystem;
use MyHammer\CronAssistant\Model\CrontabLine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->addArgument('path', InputArgument::REQUIRED, 'Path to where the crontab files live.')
            ->addArgument(
                'dateTime',
                InputArgument::REQUIRED,
                'The date and/or time in question. You can ask for a date, a date and hour or a date and hour+minutes. Format: YYYY-MM-DD [HH][:MM]'
            );
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

        foreach (Filesystem::findFiles($input->getArgument('path')) as $file) {
            try {
                $cronTabFile = CrontabParser::parseContent($file->getContents());
                foreach ($cronTabFile as $lineNumber => $cronTabLine) {
                    if ($cronTabLine->isRunningAt($time)) {
                        $this->outputIsRunningAt($file->getRealPath(), $lineNumber, $cronTabLine);
                    }
                }
            } catch (\RuntimeException $exception) {
                $output->writeln(
                    sprintf(
                        "<info>Ignoring %s. Doesn't look like a crontab file</info>",
                        $file->getRealPath()
                    )
                );
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