<?php

namespace Alius\Injector\Fixtures;

class WithoutConstructor
{
    public static function make()
    {
        return new static();
    }
}
