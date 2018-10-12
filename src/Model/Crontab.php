<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model;

use Traversable;

class Crontab implements \IteratorAggregate
{
    /**
     * @var CrontabLine[]
     */
    private $crontabLine = [];

    public function addCrontabLine(int $lineNumber, CrontabLine $cron)
    {
        $this->crontabLine[$lineNumber] = $cron;
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->crontabLine);
    }
}
