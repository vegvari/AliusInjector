<?php

namespace Alius\Test;

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
    public function __construct(WithoutConstructor $a1)
    {
        $this->a1 = $a1;
    }

    public static function make()
    {
        return new static(WithoutConstructor::make());
    }
}

class MultipleSimpleClass
{
    public function __construct(WithoutConstructor $a1, WithConstructor $a2)
    {
        $this->a1 = $a1;
        $this->a2 = $a2;
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
    public function __construct(OneSimpleClass $a1)
    {
        $this->a1 = $a1;
    }

    public static function make()
    {
        return new static(OneSimpleClass::make());
    }
}

class MultipleRecursiveClass
{
    public function __construct(OneSimpleClass $a1, OneRecursiveClass $a2)
    {
        $this->a1 = $a1;
        $this->a2 = $a2;
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
    public function __construct(SimpleInterface $a1)
    {
        $this->a1 = $a1;
    }

    public static function make()
    {
        return new static();
    }
}

class OneArgument
{
    public function __construct($a1)
    {
        $this->a1 = $a1;
    }

    public static function make($a1)
    {
        return new static($a1);
    }
}

class OneOptionalArgumentWithNullDefault
{
    public function __construct($a1 = null)
    {
        $this->a1 = $a1;
    }

    public static function make($a1 = null)
    {
        return new static($a1);
    }
}

class OneOptionalArgumentWithArrayDefault
{
    public function __construct($a1 = [])
    {
        $this->a1 = $a1;
    }

    public static function make($a1 = [])
    {
        return new static($a1);
    }
}

class OneOptionalArgumentWithValueDefault
{
    public function __construct($a1 = 'test')
    {
        $this->a1 = $a1;
    }

    public static function make($a1 = 'test')
    {
        return new static($a1);
    }
}

class OneOptionalArgumentWithConstantDefault
{
    public function __construct($a1 = PHP_INT_MAX)
    {
        $this->a1 = $a1;
    }

    public static function make($a1 = PHP_INT_MAX)
    {
        return new static($a1);
    }
}

class OneArrayArgument
{
    public function __construct(array $a1)
    {
        $this->a1 = $a1;
    }

    public static function make(array $a1)
    {
        return new static($a1);
    }
}

class OneOptionalArrayArgumentWithNullDefault
{
    public function __construct(array $a1 = null)
    {
        $this->a1 = $a1;
    }

    public static function make(array $a1 = null)
    {
        return new static($a1);
    }
}

class OneOptionalArrayArgumentWithEmptyArrayDefault
{
    public function __construct(array $a1 = [])
    {
        $this->a1 = $a1;
    }

    public static function make(array $a1 = [])
    {
        return new static($a1);
    }
}

class OneOptionalArrayArgumentWithNotEmptyArrayDefault
{
    public function __construct(array $a1 = ['test'])
    {
        $this->a1 = $a1;
    }

    public static function make(array $a1 = ['test'])
    {
        return new static($a1);
    }
}

class OneOptionalClassArgument
{
    public function __construct(WithoutConstructor $a1 = null)
    {
        $this->a1 = $a1;
    }

    public static function make(WithoutConstructor $a1 = null)
    {
        return new static($a1);
    }
}

class OneClosureArgument
{
    public function __construct(Closure $a1)
    {
        $this->a1 = $a1;
    }

    public static function make(Closure $a1)
    {
        return new static($a1);
    }
}

class OneOptionalClosureArgument
{
    public function __construct(Closure $a1 = null)
    {
        $this->a1 = $a1;
    }

    public static function make(Closure $a1 = null)
    {
        return new static($a1);
    }
}

class MultipleArgument
{
    public function __construct($a1, $a2)
    {
        $this->a1 = $a1;
        $this->a2 = $a2;
    }

    public static function make($a1, $a2)
    {
        return new static($a1, $a2);
    }
}

class MultipleArgumentWithFirstOptionalWithNullDefault
{
    public function __construct($a1 = null, $a2)
    {
        $this->a1 = $a1;
        $this->a2 = $a2;
    }

    public static function make($a1 = null, $a2)
    {
        return new static($a1, $a2);
    }
}
