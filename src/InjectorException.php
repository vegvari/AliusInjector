<?php

namespace Alius\Injector;

use Exception;

class InjectorException extends Exception
{
    /**
     * Class is already shared
     *
     * @param  string $class_name
     * @return this
     */
    public static function alreadyShared($class_name)
    {
        return new static('This class is already shared: "' . $class_name . '"');
    }

    /**
     * Interface implementation already set
     *
     * @param  string $interface_name
     * @param  string $class_name
     * @return this
     */
    public static function implementationAlreadySet($interface_name, $class_name)
    {
        return new static(
            'Interface implementation is already set for this interface: "' . $interface_name .
            '", defined class: "' . $class_name . '"'
        );
    }

    /**
     * No implementation found for this interface
     *
     * @param  string $interface_name
     * @return this
     */
    public static function noImplementation($interface_name)
    {
        throw new static('No implementation found for this interface: "' . $interface_name . '"');
    }

    /**
     * Tried to pass arguments when getting a shared instance
     *
     * @param  string $class_name
     * @return this
     */
    public static function argumentsForShared($class_name)
    {
        throw new static('Arguments passed to shared instance: "' . $class_name . '"');
    }
}
