<?php

namespace Alius\Injector;

use Closure;
use ReflectionClass;
use ReflectionFunction;

class Injector
{
    /**
     * @var array
     */
    protected $shared = [];

    /**
     * @var array
     */
    protected $shared_interfaces = [];

    /**
     * @var array
     */
    protected $interfaces = [];

    /**
     * @param array $shared
     */
    public function __construct(array $shared = [], array $implementations = [])
    {
        foreach ($shared as $key => $value) {
            $this->shared($value);
        }

        foreach ($implementations as $key => $value) {
            $this->setImplementation($key, $value);
        }
    }

    /**
     * Define a shared class
     *
     * @param string $class_name
     * @param array  $class_args
     */
    public function shared($class_name, array $class_args = [])
    {
        if ($this->isShared($class_name)) {
            throw InjectorException::alreadyShared($class_name);
        }

        $this->shared[$class_name]['instance'] = null;
        $this->shared[$class_name]['args'] = $class_args;

        foreach (class_implements($class_name) as $key => $value) {
            if (! isset($this->shared_interfaces[$value])) {
                $this->shared_interfaces[$value] = $class_name;
            }
        }
    }

    /**
     * Is this class shared?
     *
     * @param  string $class_name
     * @return bool
     */
    public function isShared($class_name)
    {
        return array_key_exists($class_name, $this->shared);
    }

    /**
     * Set an interface implementation
     *
     * @param string $interface_name
     * @param string $class_name
     */
    public function setImplementation($interface_name, $class_name)
    {
        if (isset($this->interfaces[$interface_name])) {
            throw InjectorException::implementationAlreadySet($interface_name, $this->interfaces[$interface_name]);
        }

        $this->interfaces[$interface_name] = $class_name;
    }

    /**
     * Get the class name of the registered implementation
     *
     * @param  string $interface_name
     * @return string
     */
    public function getImplementation($interface_name)
    {
        if (isset($this->interfaces[$interface_name])) {
            return $this->interfaces[$interface_name];
        } elseif (isset($this->shared_interfaces[$interface_name])) {
            return $this->shared_interfaces[$interface_name];
        }

        throw InjectorException::noImplementation($interface_name);
    }

    /**
     * Get a shared or new instance
     *
     * @param  string $class_name The name of the class
     * @param  array  $class_args
     * @return object
     */
    public function get($class_name, array $class_args = [])
    {
        if ($this->isShared($class_name)) {
            if (! empty($class_args)) {
                throw InjectorException::argumentsForShared($class_name);
            }

            if ($this->shared[$class_name]['instance'] === null) {
                $this->shared[$class_name]['instance'] = $this->make($class_name, $this->shared[$class_name]['args']);
            }

            return $this->shared[$class_name]['instance'];
        }

        return $this->make($class_name, $class_args);
    }

    /**
     * Create a new instance or invoke a Closure
     *
     * @param  string|Closure $class_name
     * @param  array          $class_args
     * @return object
     */
    public function make($class_name, array $class_args = [])
    {
        $closure = $class_name instanceof Closure ? true : false;
        $reflection_params = [];
        $calling_arguments = [];

        if ($closure) {
            $reflection = new ReflectionFunction($class_name);
            $reflection_params = $reflection->getParameters();
        } else {
            $reflection = new ReflectionClass($class_name);

            // Return the registered implementation of this interface
            if ($reflection->isInterface()) {
                return $this->get($this->getImplementation($class_name), $class_args);
            }

            $constructor = $reflection->getConstructor();
            if ($constructor !== null) {
                $reflection_params = $constructor->getParameters();
            }
        }

        foreach ($reflection_params as $param_key => $param) {
            $name = $param->getName();
            $hint = $param->getClass();

            if ($hint !== null) {
                $hint = $param->getClass()->name;
            }

            $calling_arguments[$param_key] = null;
            if ($param->isDefaultValueAvailable()) {
                $calling_arguments[$param_key] = $param->getDefaultValue();
            }

            // Use $custom_arg_defined because custom arguments can be null
            $custom_arg_defined = false;
            if (array_key_exists($name, $class_args)) {
                $calling_arguments[$param_key] = $class_args[$name];
                $custom_arg_defined = true;
            } elseif (array_key_exists($param_key, $class_args)) {
                $calling_arguments[$param_key] = $class_args[$param_key];
                $custom_arg_defined = true;
            }

            // If the type hint is not Closure, we invoke the passed Closure
            if ($custom_arg_defined && $hint !== Closure::class && $calling_arguments[$param_key] instanceof Closure) {
                $calling_arguments[$param_key] = $this->make($calling_arguments[$param_key]);
            } elseif (! $custom_arg_defined && $hint !== null) {
                if ($hint !== Closure::class && ! $param->isOptional()) {
                    $calling_arguments[$param_key] = $this->get($hint);
                }
            }
        }

        if ($closure) {
            return call_user_func_array([$class_name, '__invoke'], $calling_arguments);
        } else {
            return $reflection->newInstanceArgs($calling_arguments);
        }
    }
}
