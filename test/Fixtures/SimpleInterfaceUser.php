<?php

namespace Alius\Injector\Fixtures;

class SimpleInterfaceUser
{
    public function __construct(SimpleInterface $argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make()
    {
        return new static();
    }
}
