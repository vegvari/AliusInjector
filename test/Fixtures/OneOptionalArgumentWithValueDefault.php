<?php

namespace Alius\Injector\Fixtures;

class OneOptionalArgumentWithValueDefault
{
    public function __construct($argument1 = 'test')
    {
        $this->argument1 = $argument1;
    }

    public static function make($argument1 = 'test')
    {
        return new static($argument1);
    }
}
