<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model\ValueObject;

class SteppedRangeSchedule implements Schedule
{
    /**
     * @var int
     */
    private $step;

    /**
     * @var RangeSchedule
     */
    private $range;

    /**
     * @param int $min
     * @param int $max
     * @param int $step
     */
    public function __construct(int $min, int $max, int $step)
    {
        $this->range = new RangeSchedule($min, $max);
        $this->step = $step;
    }

    /**
     * @param int $value
     * @return bool
     */
    public function isIn(int $value): bool
    {
        if ($this->range->isIn($value)) {
            return (($value - $this->range->getMin()) % $this->step === 0);
        }

        return false;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->range->getMin();
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->range->getMax();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->range->__toString().'/'.$this->step;
    }

    /**
     * @return int
     */
    public function getStep(): int
    {
        return $this->step;
    }
}
