<?php

namespace MyHammer\CronAssistant\Utils;

use Symfony\Component\Finder\Finder;

class Filesystem
{
    /**
     * @param string $directory
     * @return Finder
     */
    public static function findFiles(string $directory): Finder
    {
        $finder = new Finder();

        return $finder->files()->in($directory)->sortByName()->depth(0);
    }
}
