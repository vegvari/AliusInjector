<?php

namespace Alius\Injector\Fixtures;

class OneOptionalArrayArgumentWithEmptyArrayDefault
{
    public function __construct(array $argument1 = [])
    {
        $this->argument1 = $argument1;
    }

    public static function make(array $argument1 = [])
    {
        return new static($argument1);
    }
}
