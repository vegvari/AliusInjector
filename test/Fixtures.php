<?php

namespace Alius\Injector;

use Closure;

class WithoutConstructor
{
    public static function make()
    {
        return new static();
    }
}

class WithConstructor
{
    public function __construct()
    {
    }

    public static function make()
    {
        return new static();
    }
}

class OneSimpleClass
{
    public function __construct(WithoutConstructor $argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make()
    {
        return new static(WithoutConstructor::make());
    }
}

class MultipleSimpleClass
{
    public function __construct(WithoutConstructor $argument1, WithConstructor $argument2)
    {
        $this->argument1 = $argument1;
        $this->argument2 = $argument2;
    }

    public static function make()
    {
        return new static(
            WithoutConstructor::make(),
            WithConstructor::make()
        );
    }
}

class OneRecursiveClass
{
    public function __construct(OneSimpleClass $argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make()
    {
        return new static(OneSimpleClass::make());
    }
}

class MultipleRecursiveClass
{
    public function __construct(OneSimpleClass $argument1, OneRecursiveClass $argument2)
    {
        $this->argument1 = $argument1;
        $this->argument2 = $argument2;
    }

    public static function make()
    {
        return new static(
            OneSimpleClass::make(),
            OneRecursiveClass::make()
        );
    }
}

class SimpleInterfaceImplementation implements SimpleInterface
{
    public static function make()
    {
        return new static();
    }
}

class SimpleInterfaceImplementation2 implements SimpleInterface
{
    public static function make()
    {
        return new static();
    }
}

interface SimpleInterface
{
}

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

class OneArgument
{
    public function __construct($argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make($argument1)
    {
        return new static($argument1);
    }
}

class OneOptionalArgumentWithNullDefault
{
    public function __construct($argument1 = null)
    {
        $this->argument1 = $argument1;
    }

    public static function make($argument1 = null)
    {
        return new static($argument1);
    }
}

class OneOptionalArgumentWithArrayDefault
{
    public function __construct($argument1 = [])
    {
        $this->argument1 = $argument1;
    }

    public static function make($argument1 = [])
    {
        return new static($argument1);
    }
}

class OneOptionalArgumentWithValueDefault
{
    public function __construct($argument1 = 'test')
    {
        $this->argument1 = $argument1;
    }

    public static function make($argument1 = 'test')
    {
        return new static($argument1);
    }
}

class OneOptionalArgumentWithConstantDefault
{
    public function __construct($argument1 = PHP_INT_MAX)
    {
        $this->argument1 = $argument1;
    }

    public static function make($argument1 = PHP_INT_MAX)
    {
        return new static($argument1);
    }
}

class OneArrayArgument
{
    public function __construct(array $argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make(array $argument1)
    {
        return new static($argument1);
    }
}

class OneOptionalArrayArgumentWithNullDefault
{
    public function __construct(array $argument1 = null)
    {
        $this->argument1 = $argument1;
    }

    public static function make(array $argument1 = null)
    {
        return new static($argument1);
    }
}

class OneOptionalArrayArgumentWithEmptyArrayDefault
{
    public function __construct(array $argument1 = [])
    {
        $this->argument1 = $argument1;
    }

    public static function make(array $argument1 = [])
    {
        return new static($argument1);
    }
}

class OneOptionalArrayArgumentWithNotEmptyArrayDefault
{
    public function __construct(array $argument1 = ['test'])
    {
        $this->argument1 = $argument1;
    }

    public static function make(array $argument1 = ['test'])
    {
        return new static($argument1);
    }
}

class OneOptionalClassArgument
{
    public function __construct(WithoutConstructor $argument1 = null)
    {
        $this->argument1 = $argument1;
    }

    public static function make(WithoutConstructor $argument1 = null)
    {
        return new static($argument1);
    }
}

class OneClosureArgument
{
    public function __construct(Closure $argument1)
    {
        $this->argument1 = $argument1;
    }

    public static function make(Closure $argument1)
    {
        return new static($argument1);
    }
}

class OneOptionalClosureArgument
{
    public function __construct(Closure $argument1 = null)
    {
        $this->argument1 = $argument1;
    }

    public static function make(Closure $argument1 = null)
    {
        return new static($argument1);
    }
}

class MultipleArgument
{
    public function __construct($argument1, $argument2)
    {
        $this->argument1 = $argument1;
        $this->argument2 = $argument2;
    }

    public static function make($argument1, $argument2)
    {
        return new static($argument1, $argument2);
    }
}

class MultipleArgumentWithFirstOptionalWithNullDefault
{
    public function __construct($argument1 = null, $argument2)
    {
        $this->argument1 = $argument1;
        $this->argument2 = $argument2;
    }

    public static function make($argument1 = null, $argument2)
    {
        return new static($argument1, $argument2);
    }
}
