<?php

namespace Alius\Injector\Exceptions;

class ImplementationAlreadySet extends InjectorException
{
    /**
     * @param string $interface
     * @param string $class
     */
    public function __construct($interface, $class)
    {
        parent::__construct('Interface implementation is already set for this interface: "' . $interface . '", defined class: "' . $class . '"');
    }
}
