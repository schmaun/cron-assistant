<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model\ValueObject;

class RangeSchedule implements Schedule
{
    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    /**
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @param int $value
     * @return bool
     */
    public function isIn(int $value): bool
    {
        return ($value >= $this->min && $value <= $this->max);
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->min . '-' . $this->max;
    }
}
