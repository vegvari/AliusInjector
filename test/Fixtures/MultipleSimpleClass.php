<?php

namespace Alius\Injector\Fixtures;

class MultipleSimpleClass
{
    public function __construct(WithoutConstructor $argument1, WithConstructor $argument2)
    {
        $this->argument1 = $argument1;
        $this->argument2 = $argument2;
    }

    public static function make()
    {
        return new static(
            WithoutConstructor::make(),
            WithConstructor::make()
        );
    }
}
