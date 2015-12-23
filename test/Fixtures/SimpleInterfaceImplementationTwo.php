<?php

namespace Alius\Injector\Fixtures;

class SimpleInterfaceImplementationTwo implements SimpleInterface
{
    public static function make()
    {
        return new static();
    }
}
