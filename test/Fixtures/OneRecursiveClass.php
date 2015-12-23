<?php

namespace Alius\Injector\Fixtures;

class OneRecursiveClass
{
    public function __construct(OneSimpleClass $argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make()
    {
        return new static(OneSimpleClass::make());
    }
}
