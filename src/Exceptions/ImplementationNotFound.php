<?php

namespace Alius\Injector\Exceptions;

class ImplementationNotFound extends InjectorException
{
    /**
     * @param string $interface
     */
    public function __construct($interface)
    {
        parent::__construct('No implementation found for this interface: "' . $interface . '"');
    }
}
