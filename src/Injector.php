<?php

namespace Alius\Injector;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use Alius\Injector\Exceptions\AlreadyShared;
use Alius\Injector\Exceptions\ImplementationNotFound;
use Alius\Injector\Exceptions\SharedInstanceArguments;
use Alius\Injector\Exceptions\ImplementationAlreadySet;

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
     * @param array $implementations
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
            throw new AlreadyShared($class_name);
        }

        $this->shared[$class_name]['instance'] = null;
        $this->shared[$class_name]['args'] = $class_args;

        foreach (class_implements($class_name) as $value) {
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
            throw new ImplementationAlreadySet($interface_name, $this->interfaces[$interface_name]);
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

        throw new ImplementationNotFound($interface_name);
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
                throw new SharedInstanceArguments($class_name);
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
     * @param  array          $custom_args
     * @return object
     */
    public function make($class_name, array $custom_args = [])
    {
        if ($class_name instanceof Closure) {
            $reflection = new ReflectionFunction($class_name);
            $params = $reflection->getParameters();
            return call_user_func_array([$class_name, '__invoke'], $this->createArguments($params, $custom_args));
        }

        $reflection = new ReflectionClass($class_name);
        if ($reflection->isInterface()) {
            return $this->get($this->getImplementation($class_name), $custom_args);
        }

        $constructor = $reflection->getConstructor();
        if ($constructor !== null) {
            $params = $constructor->getParameters();
            return $reflection->newInstanceArgs($this->createArguments($params, $custom_args));
        }

        return $reflection->newInstanceArgs($this->createArguments([], $custom_args));
    }

    /**
     * Create an argument array
     *
     * @param array $params
     * @param array $custom_args
     *
     * @return array
     */
    public function createArguments(array $params, array $custom_args = [])
    {
        $arguments = [];

        foreach ($params as $param_key => $param) {
            // Set the argument to the null or the default value
            $arguments[$param_key] = null;
            if ($param->isDefaultValueAvailable()) {
                $arguments[$param_key] = $param->getDefaultValue();
            }

            // Get the argument's name and typehint
            $name = $param->getName();
            $hint = $param->getClass();

            // We have typehint
            if ($hint !== null) {
                $hint = $param->getClass()->name;
            }

            // Get the custom argument based on the argument's name or position
            $custom_arg_defined = false;
            if (array_key_exists($name, $custom_args)) {
                $arguments[$param_key] = $custom_args[$name];
                $custom_arg_defined = true;
            } elseif (array_key_exists($param_key, $custom_args)) {
                $arguments[$param_key] = $custom_args[$param_key];
                $custom_arg_defined = true;
            }

            // If the type hint is not Closure, we invoke the passed Closure
            if ($custom_arg_defined && $hint !== Closure::class && $arguments[$param_key] instanceof Closure) {
                $arguments[$param_key] = $this->make($arguments[$param_key]);
            } elseif (! $custom_arg_defined && $hint !== null) {
                if ($hint !== Closure::class && ! $param->isOptional()) {
                    $arguments[$param_key] = $this->get($hint);
                }
            }
        }

        return $arguments;
    }
}
