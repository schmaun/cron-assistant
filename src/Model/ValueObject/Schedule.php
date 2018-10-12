<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model\ValueObject;

interface Schedule
{
    /**
     * @param int $value
     * @return bool
     */
    public function isIn(int $value): bool;

    /**
     * @return string
     */
    public function __toString(): string;
}
