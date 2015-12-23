<?php

namespace Alius\Injector\Fixtures;

class MultipleRecursiveClass
{
    public function __construct(OneSimpleClass $argument1, OneRecursiveClass $argument2)
    {
        $this->argument1 = $argument1;
        $this->argument2 = $argument2;
    }

    public static function make()
    {
        return new static(
            OneSimpleClass::make(),
            OneRecursiveClass::make()
        );
    }
}
