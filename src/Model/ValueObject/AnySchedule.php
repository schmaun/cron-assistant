<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model\ValueObject;

class AnySchedule implements Schedule
{
    /**
     * @param int $value
     * @return bool
     */
    public function isIn(int $value): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return '*';
    }
}