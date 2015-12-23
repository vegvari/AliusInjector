<?php

namespace Alius\Injector\Fixtures;

use Closure;

class OneClosureArgument
{
    public function __construct(Closure $argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make(Closure $argument1)
    {
        return new static($argument1);
    }
}
