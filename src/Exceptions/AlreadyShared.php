<?php

namespace Alius\Injector\Exceptions;

class AlreadyShared extends InjectorException
{
    /**
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct('This class is already shared: "' . $class . '"');
    }
}
