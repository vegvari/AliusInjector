<?php

namespace Alius\Injector\Fixtures;

class WithConstructor
{
    public function __construct()
    {
    }

    public static function make()
    {
        return new static();
    }
}
