<?php

namespace Alius\Injector\Fixtures;

class OneOptionalArgumentWithConstantDefault
{
    public function __construct($argument1 = PHP_INT_MAX)
    {
        $this->argument1 = $argument1;
    }

    public static function make($argument1 = PHP_INT_MAX)
    {
        return new static($argument1);
    }
}
