<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Factories;

use MyHammer\CronAssistant\Model\Cron;
use MyHammer\CronAssistant\Model\Crontab;
use MyHammer\CronAssistant\Model\CrontabLine;

class CrontabParser
{
    /**
     * @param string $content
     * @return Crontab|CrontabLine[]
     */
    public static function parseContent(string $content): Crontab
    {
        $crontab = new Crontab();

        $lines = preg_split("/((\r?\n)|(\r\n?))/", $content);
        foreach ($lines as $key => $line) {
            $crontabLine = (new CrontabLine())
                ->setLineNumber($key)
                ->setOriginalLine($line);

            if ($line && !self::isNotRelevant($line)) {
                $cron = self::parseCron($line);
                $crontabLine->setCron($cron);
            }

            $crontab->addCrontabLine($key, $crontabLine);
        }

        return $crontab;
    }

    public static function parseCron(string $line): Cron
    {
        $line = trim($line);
        $elements = [];
        $schedulePattern = sprintf('%1$s%1$s%1$s%1$s%1$s', '([0-9*,/\-\w]{1,})\s{1,}');
        preg_match('#^' . $schedulePattern . '([^/\* ]{0,})\s(.*)$#', $line, $elements);
        $elements = self::normalizeCronlineElements($elements);

        $elementCount = \count($elements);
        if ($elementCount !== 7 && $elementCount !== 6) {
            throw new \RuntimeException('Parsing error: "' . $line . '"');
        }

        $cron = new Cron();

        try {
            $cron->setMinute(ScheduleFactory::parse(array_shift($elements)));
            $cron->setHour(ScheduleFactory::parse(array_shift($elements)));
            $cron->setDay(ScheduleFactory::parse(array_shift($elements)));
            $cron->setMonth(ScheduleFactory::parse(array_shift($elements)));
            $cron->setWeekDay(ScheduleFactory::parse(array_shift($elements)));
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Parsing error: "' . $line . '"', 0, $e);
        }

        if (\count($elements) === 2) {
            $cron->setUser(array_shift($elements));
        }
        $cron->setCommand(array_shift($elements));

        return $cron;
    }

    /**
     * @param $elements
     * @return array
     */
    protected static function normalizeCronlineElements($elements): array
    {
        array_shift($elements);
        $elements = array_map('trim', $elements);
        $elements = array_filter($elements, function ($value) {
            return trim($value) !== '';
        });

        return $elements;
    }

    /**
     * @param string $line
     * @return bool
     */
    private static function isNotRelevant(string $line): bool
    {
        return self::isCommented($line) || self::isEnvironmentSetting($line);
    }

    /**
     * @param string $line
     * @return bool
     */
    private static function isCommented(string $line): bool
    {
        return strpos($line, '#') === 0;
    }

    /**
     * @param string $line
     * @return bool
     */
    private static function isEnvironmentSetting(string $line): bool
    {
        return (preg_match("#^([0-9\w_]{1,})( )?=( )?#", $line) > 0);
    }
}