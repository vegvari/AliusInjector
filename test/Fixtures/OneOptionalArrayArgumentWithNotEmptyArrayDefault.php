<?php

namespace Alius\Injector\Fixtures;

class OneOptionalArrayArgumentWithNotEmptyArrayDefault
{
    public function __construct(array $argument1 = ['test'])
    {
        $this->argument1 = $argument1;
    }

    public static function make(array $argument1 = ['test'])
    {
        return new static($argument1);
    }
}
