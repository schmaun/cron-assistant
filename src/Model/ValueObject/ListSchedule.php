<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model\ValueObject;

class ListSchedule implements Schedule
{
    /**
     * @var array
     */
    private $values;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @param int $value
     * @return bool
     */
    public function isIn(int $value): bool
    {
        return \in_array($value, $this->values, true);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return implode(',', $this->values);
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
