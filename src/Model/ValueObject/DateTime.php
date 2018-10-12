<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model\ValueObject;

class DateTime
{
    public $year;
    public $month;
    public $day;

    public $hour;
    public $minute;

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param null|int $hour
     * @param null|int $minute
     */
    public function __construct(int $year, int $month, int $day, ?int $hour = null, ?int $minute = null)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return print_r($this, true);
    }
}
