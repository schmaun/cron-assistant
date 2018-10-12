<?php
declare(strict_types=1);

namespace MyHammer\CronAssistant\Model\ValueObject;

class User
{
    /**
     * @var string
     */
    private $name;

    /**
     * User constructor.
     * @param string $name
     */
    public function __construct(?string $name)
    {
        if (null === $name) {
            $name = '';
        }

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->name;
    }
}