<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Factories;

use MyHammer\CronAssistant\Model\ValueObject\DateTime;

class DateTimeParser
{
    /**
     * @param string $dateTime
     * @return DateTime
     */
    public static function parse(string $dateTime): DateTime
    {
        $matches = [];
        preg_match('#^(\d{4})-(\d{2})-(\d{2})(?: )?(\d{2})?(?::(\d{2}))?$#', $dateTime, $matches);

        if (\count($matches) === 4) {
            return new DateTime((int)$matches[1], (int)$matches[2], (int)$matches[3]);
        }
        if (\count($matches) === 5) {
            return new DateTime((int)$matches[1], (int)$matches[2], (int)$matches[3], (int)$matches[4]);
        }
        if (\count($matches) === 6) {
            return new DateTime((int)$matches[1], (int)$matches[2], (int)$matches[3], (int)$matches[4], (int)$matches[5]);
        }

        throw new \InvalidArgumentException(sprintf("Couldn't parse '%s'", $dateTime));
    }

}