<?php

namespace Alius\Injector\Fixtures;

class OneOptionalArrayArgumentWithNullDefault
{
    public function __construct(array $argument1 = null)
    {
        $this->argument1 = $argument1;
    }

    public static function make(array $argument1 = null)
    {
        return new static($argument1);
    }
}
