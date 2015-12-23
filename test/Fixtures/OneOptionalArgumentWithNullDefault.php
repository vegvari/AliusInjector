<?php

namespace Alius\Injector\Fixtures;

class OneOptionalArgumentWithNullDefault
{
    public function __construct($argument1 = null)
    {
        $this->argument1 = $argument1;
    }

    public static function make($argument1 = null)
    {
        return new static($argument1);
    }
}
