<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model;

use MyHammer\CronAssistant\Model\ValueObject\DateTime;

class CrontabLine
{
    /**
     * @int
     */
    private $lineNumber;

    /**
     * @var string
     */
    private $originalLine;

    /**
     * @var Cron
     */
    private $cron;

    /**
     * @param mixed $originalLine
     * @return CrontabLine
     */
    public function setOriginalLine(string $originalLine): self
    {
        $this->originalLine = $originalLine;

        return $this;
    }

    /**
     * @param Cron $cron
     * @return CrontabLine
     */
    public function setCron(Cron $cron): self
    {
        $this->cron = $cron;

        return $this;
    }

    /**
     * @param int $lineNumber
     * @return CrontabLine
     */
    public function setLineNumber(int $lineNumber): self
    {
        $this->lineNumber = $lineNumber;

        return $this;
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    public function isRunningAt(DateTime $dateTime): bool
    {
        return $this->cron instanceof Cron ? $this->cron->isIn($dateTime): false;
    }

    /**
     * @return string
     */
    public function getOriginalLine(): string
    {
        return $this->originalLine;
    }
}
