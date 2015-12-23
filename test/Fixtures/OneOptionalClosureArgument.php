<?php

namespace Alius\Injector\Fixtures;

use Closure;

class OneOptionalClosureArgument
{
    public function __construct(Closure $argument1 = null)
    {
        $this->argument1 = $argument1;
    }

    public static function make(Closure $argument1 = null)
    {
        return new static($argument1);
    }
}
