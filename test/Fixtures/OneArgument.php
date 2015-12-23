<?php

namespace Alius\Injector\Fixtures;

class OneArgument
{
    public function __construct($argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make($argument1)
    {
        return new static($argument1);
    }
}
