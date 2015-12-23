<?php

namespace Alius\Injector;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionParameter;
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
     * @param string $class_name
     *
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
     * @param string $interface_name
     *
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
     * @param string $class_name The name of the class
     * @param array  $class_args
     *
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
     * @param string|Closure $class_name
     * @param array          $custom_args
     *
     * @return object
     */
    public function make($class_name, array $custom_args = [])
    {
        // Closure
        if ($class_name instanceof Closure) {
            $reflection = new ReflectionFunction($class_name);
            $params = $reflection->getParameters();
            return call_user_func_array([$class_name, '__invoke'], $this->createArguments($params, $custom_args));
        }

        // Interface
        $reflection = new ReflectionClass($class_name);
        if ($reflection->isInterface()) {
            return $this->get($this->getImplementation($class_name), $custom_args);
        }

        // Class
        $constructor = $reflection->getConstructor();
        if ($constructor !== null) {
            $params = $constructor->getParameters();
            return $reflection->newInstanceArgs($this->createArguments($params, $custom_args));
        }

        return $reflection->newInstanceArgs([]);
    }

    /**
     * Create an argument array
     *
     * @param array $params
     * @param array $custom_args
     *
     * @return array
     */
    protected function createArguments(array $params, array $custom_args = [])
    {
        $arguments = [];

        foreach ($params as $param_key => $param) {
            $arguments[$param_key] = $this->getDefaultValue($param);
            $name = $param->getName();
            $hint = $this->getTypeHint($param);

            // Get the custom argument based on the argument's name or position
            if (array_key_exists($name, $custom_args)) {
                $arguments[$param_key] = $custom_args[$name];
            } elseif (array_key_exists($param_key, $custom_args)) {
                $arguments[$param_key] = $custom_args[$param_key];
            }

            // Invoke the closure or get the hinted instance
            if ($hint !== Closure::class && $arguments[$param_key] instanceof Closure) {
                $arguments[$param_key] = $this->make($arguments[$param_key]);
            } elseif (empty($custom_args) && $hint !== null && ! $param->isOptional()) {
                $arguments[$param_key] = $this->get($hint);
            }
        }

        return $arguments;
    }

    /**
     * Get the default value of the parameter
     *
     * @param ReflectionParameter $param
     *
     * @return mixed
     */
    protected function getDefaultValue(ReflectionParameter $param)
    {
        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }
    }

    /**
     * Get the type hint of the parameter
     *
     * @param ReflectionParameter $param
     *
     * @return string|null
     */
    protected function getTypeHint(ReflectionParameter $param)
    {
        if ($param->getClass() !== null) {
            return $param->getClass()->name;
        }
    }
}
