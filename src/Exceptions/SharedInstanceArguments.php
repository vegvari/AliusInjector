<?php

namespace Alius\Injector\Exceptions;

class SharedInstanceArguments extends InjectorException
{
    /**
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct('Arguments passed to shared instance: "' . $class . '"');
    }
}
