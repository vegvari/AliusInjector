<?php

namespace Alius\Injector\Fixtures;

class OneOptionalClassArgument
{
    public function __construct(WithoutConstructor $argument1 = null)
    {
        $this->argument1 = $argument1;
    }

    public static function make(WithoutConstructor $argument1 = null)
    {
        return new static($argument1);
    }
}
