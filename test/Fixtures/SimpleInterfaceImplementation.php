<?php

namespace Alius\Injector\Fixtures;

class SimpleInterfaceImplementation implements SimpleInterface
{
    public static function make()
    {
        return new static();
    }
}
