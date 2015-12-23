<?php

namespace Alius\Injector\Fixtures;

class MultipleArgumentWithFirstOptionalWithNullDefault
{
    public function __construct($argument1 = null, $argument2)
    {
        $this->argument1 = $argument1;
        $this->argument2 = $argument2;
    }

    public static function make($argument1 = null, $argument2)
    {
        return new static($argument1, $argument2);
    }
}
