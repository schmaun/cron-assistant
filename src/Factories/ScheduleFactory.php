<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Factories;

use MyHammer\CronAssistant\Model\ValueObject\AnySchedule;
use MyHammer\CronAssistant\Model\ValueObject\ListSchedule;
use MyHammer\CronAssistant\Model\ValueObject\RangeSchedule;
use MyHammer\CronAssistant\Model\ValueObject\Schedule;
use MyHammer\CronAssistant\Model\ValueObject\SteppedAnySchedule;
use MyHammer\CronAssistant\Model\ValueObject\SteppedRangeSchedule;

class ScheduleFactory
{
    private const NAMED_VALUE_2_VALUE = [
        'SUN' => 0,
        'MON' => 1,
        'TUE' => 2,
        'WEN' => 3,
        'THU' => 4,
        'FRI' => 5,
        'SAT' => 6,

        'JAN' => 1,
        'FEB' => 2,
        'MAR' => 3,
        'APR' => 4,
        'MAY' => 5,
        'JUN' => 6,
        'JUL' => 7,
        'AUG' => 8,
        'SEP' => 9,
        'OCT' => 10,
        'NOV' => 11,
        'DEC' => 12,
    ];

    /**
     * @param string $value
     * @return Schedule
     */
    public static function parse(string $value): Schedule
    {
        if ('*' === $value) {
            return new AnySchedule();
        }

        $value = self::normalizeNamedValues($value);

        if (ctype_digit($value)) {
            return new ListSchedule([(int)$value]);
        }

        $matches = [];
        preg_match('#^\*\/(\d{1,2})$#', $value, $matches);
        if (\count($matches) === 2) {
            return new SteppedAnySchedule((int)$matches[1]);
        }

        if (strpos($value, '-') !== false) {
            $matches = [];
            preg_match('#^(\d{1,2})-(\d{1,2})(?:\/(\d{1,2}))?$#', $value, $matches);

            if (\count($matches) === 3) {
                return new RangeSchedule((int)$matches[1], (int)$matches[2]);
            }
            if (\count($matches) === 4) {
                return new SteppedRangeSchedule((int)$matches[1], (int)$matches[2], (int)$matches[3]);
            }
        }

        if (strpos($value, ',') !== false) {
            $matches = explode(',', $value);
            $matches = self::normalizeMatches($matches);

            if (\count($matches) > 1) {
                return new ListSchedule($matches);
            }
        }

        throw new \RuntimeException(sprintf('Could not parse: "%s"', $value));
    }

    private static function normalizeNamedValues(string $value)
    {
        return str_replace(array_keys(self::NAMED_VALUE_2_VALUE), self::NAMED_VALUE_2_VALUE, $value);
    }

    /**
     * @param array $matches
     * @return array
     */
    protected static function normalizeMatches(array $matches): array
    {
        $matches = array_map('trim', $matches);
        $matches = array_filter($matches, function ($value) {
            return $value !== '' && ctype_digit($value);
        });
        $matches = array_map(function ($value) {
            return (int)$value;
        }, $matches);

        return $matches;
    }
}