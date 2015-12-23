<?php

namespace Alius\Injector\Fixtures;

class MultipleArgument
{
    public function __construct($argument1, $argument2)
    {
        $this->argument1 = $argument1;
        $this->argument2 = $argument2;
    }

    public static function make($argument1, $argument2)
    {
        return new static($argument1, $argument2);
    }
}
