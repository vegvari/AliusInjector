<?php

namespace Alius\Injector\Fixtures;

class OneSimpleClass
{
    public function __construct(WithoutConstructor $argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make()
    {
        return new static(WithoutConstructor::make());
    }
}
