<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model\ValueObject;

class SteppedAnySchedule implements Schedule
{
    /**
     * @var int
     */
    private $step;

    /**
     * @var AnySchedule
     */
    private $any;

    /**
     * @param int $step
     */
    public function __construct(int $step)
    {
        $this->any = new AnySchedule();
        $this->step = $step;
    }

    /**
     * @param int $value
     * @return bool
     */
    public function isIn(int $value): bool
    {
        if ($this->any->isIn($value)) {
            return ($value % $this->step === 0);
        }

        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->any->__toString() . '/' . $this->step;
    }

    /**
     * @return int
     */
    public function getStep(): int
    {
        return $this->step;
    }
}
