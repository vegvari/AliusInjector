<?php

namespace Alius\Injector;

use Closure;
use PHPUnit_Framework_TestCase;
use Alius\Injector\Fixtures\OneArgument;
use Alius\Injector\Fixtures\OneSimpleClass;
use Alius\Injector\Fixtures\SimpleInterface;
use Alius\Injector\Fixtures\WithConstructor;
use Alius\Injector\Fixtures\OneArrayArgument;
use Alius\Injector\Fixtures\MultipleArgument;
use Alius\Injector\Fixtures\OneClosureArgument;
use Alius\Injector\Fixtures\OneRecursiveClass;
use Alius\Injector\Fixtures\WithoutConstructor;
use Alius\Injector\Fixtures\MultipleSimpleClass;
use Alius\Injector\Fixtures\SimpleInterfaceUser;
use Alius\Injector\Fixtures\MultipleRecursiveClass;
use Alius\Injector\Fixtures\OneOptionalClassArgument;
use Alius\Injector\Fixtures\OneOptionalClosureArgument;
use Alius\Injector\Fixtures\SimpleInterfaceImplementation;
use Alius\Injector\Fixtures\SimpleInterfaceImplementationTwo;
use Alius\Injector\Fixtures\OneOptionalArgumentWithNullDefault;
use Alius\Injector\Fixtures\OneOptionalArgumentWithArrayDefault;
use Alius\Injector\Fixtures\OneOptionalArgumentWithValueDefault;
use Alius\Injector\Fixtures\OneOptionalArgumentWithConstantDefault;
use Alius\Injector\Fixtures\OneOptionalArrayArgumentWithNullDefault;
use Alius\Injector\Fixtures\OneOptionalArrayArgumentWithEmptyArrayDefault;
use Alius\Injector\Fixtures\OneOptionalArrayArgumentWithNotEmptyArrayDefault;
use Alius\Injector\Fixtures\MultipleArgumentWithFirstOptionalWithNullDefault;

use Alius\Injector\Exceptions\AlreadyShared;
use Alius\Injector\Exceptions\ImplementationNotFound;
use Alius\Injector\Exceptions\SharedInstanceArguments;
use Alius\Injector\Exceptions\ImplementationAlreadySet;

class InjectorTest extends PHPUnit_Framework_TestCase
{
    protected $injector;

    public function setUp()
    {
        $this->injector = new Injector();
    }

    public function testInjectorConstruct()
    {
        $injector = new Injector([
            WithoutConstructor::class,
        ], [
            SimpleInterface::class => SimpleInterfaceImplementation::class,
        ]);

        $this->assertTrue($injector->isShared(WithoutConstructor::class));
        $this->assertSame($injector->getImplementation(SimpleInterface::class), SimpleInterfaceImplementation::class);
    }

    public function testWithoutConstructor()
    {
        $instance1 = WithoutConstructor::make();
        $instance2 = $this->injector->get(WithoutConstructor::class);
        $instance3 = $this->injector->get(WithoutConstructor::class);

        $this->assertEquals($instance1, $instance2);
        $this->assertNotSame($instance1, $instance2);
        $this->assertEquals($instance2, $instance3);
        $this->assertNotSame($instance2, $instance3);
    }

    public function testWithConstructor()
    {
        $instance1 = WithConstructor::make();
        $instance2 = $this->injector->get(WithConstructor::class);
        $instance3 = $this->injector->get(WithConstructor::class);

        $this->assertEquals($instance1, $instance2);
        $this->assertEquals($instance2, $instance3);
        $this->assertNotSame($instance2, $instance3);
    }

    public function testOneSimpleClass()
    {
        $instance1 = OneSimpleClass::make();
        $this->assertInstanceOf(WithoutConstructor::class, $instance1->argument1);

        $instance2 = $this->injector->get(OneSimpleClass::class);
        $instance3 = $this->injector->get(OneSimpleClass::class);

        $this->assertEquals($instance1, $instance2);
        $this->assertEquals($instance2, $instance3);
        $this->assertNotSame($instance2, $instance3);
    }

    public function testMultipleSimpleClass()
    {
        $instance1 = MultipleSimpleClass::make();
        $this->assertInstanceOf(WithoutConstructor::class, $instance1->argument1);

        $instance2 = $this->injector->get(MultipleSimpleClass::class);
        $instance3 = $this->injector->get(MultipleSimpleClass::class);

        $this->assertEquals($instance1, $instance2);
        $this->assertEquals($instance2, $instance3);
        $this->assertNotSame($instance2, $instance3);
    }

    public function testOneRecursiveClass()
    {
        $instance1 = OneRecursiveClass::make();
        $this->assertInstanceOf(OneSimpleClass::class, $instance1->argument1);

        $instance2 = $this->injector->get(OneRecursiveClass::class);
        $instance3 = $this->injector->get(OneRecursiveClass::class);

        $this->assertEquals($instance1, $instance2);
        $this->assertEquals($instance2, $instance3);
        $this->assertNotSame($instance2, $instance3);
    }

    public function testMultipleRecursiveClass()
    {
        $instance1 = MultipleRecursiveClass::make();
        $this->assertInstanceOf(OneSimpleClass::class, $instance1->argument1);
        $this->assertInstanceOf(OneRecursiveClass::class, $instance1->argument2);

        $instance2 = $this->injector->get(MultipleRecursiveClass::class);
        $instance3 = $this->injector->get(MultipleRecursiveClass::class);

        $this->assertEquals($instance1, $instance2);
        $this->assertEquals($instance2, $instance3);
        $this->assertNotSame($instance2, $instance3);
    }

    public function testSharedInstance()
    {
        $this->assertSame(false, $this->injector->isShared(WithoutConstructor::class));
        $this->injector->shared(WithoutConstructor::class);
        $this->assertSame(true, $this->injector->isShared(WithoutConstructor::class));
        $this->assertSame($this->injector->get(WithoutConstructor::class), $this->injector->get(WithoutConstructor::class));
    }

    public function testSharedInstanceTryToShareAgain()
    {
        $this->setExpectedException(AlreadyShared::class);
        $this->injector->shared(WithoutConstructor::class);
        $this->injector->shared(WithoutConstructor::class);
    }

    public function testClassUsingSharedInstance()
    {
        $this->injector->shared(WithoutConstructor::class);
        $instance1 = $this->injector->get(OneSimpleClass::class);
        $this->assertSame($this->injector->get(WithoutConstructor::class), $instance1->argument1);
    }

    public function testSharedUsingSharedInstance()
    {
        $this->injector->shared(WithoutConstructor::class);
        $this->injector->shared(OneSimpleClass::class);
        $instance1 = $this->injector->get(MultipleRecursiveClass::class);
        $this->assertSame($this->injector->get(OneSimpleClass::class), $instance1->argument1);
        $this->assertSame($this->injector->get(WithoutConstructor::class), $instance1->argument1->argument1);
        $this->assertNotSame($this->injector->get(OneRecursiveClass::class), $instance1->argument2);
    }

    public function testSharedWithArguments()
    {
        $this->injector->shared(OneArgument::class, ['test1']);
        $this->assertSame($this->injector->get(OneArgument::class), $this->injector->get(OneArgument::class));
        $this->assertSame($this->injector->get(OneArgument::class)->argument1, 'test1');
    }

    public function testSharedWithArgumentsFail()
    {
        $this->setExpectedException(SharedInstanceArguments::class);
        $this->injector->shared(OneArgument::class, ['test1']);
        $this->injector->get(OneArgument::class, ['test2']);
    }

    public function testSharedNewInstance()
    {
        $this->injector->shared(WithoutConstructor::class);
        $this->assertEquals($this->injector->get(OneSimpleClass::class), $this->injector->make(OneSimpleClass::class));
        $this->assertNotSame($this->injector->get(OneSimpleClass::class), $this->injector->make(OneSimpleClass::class));

        $this->injector->shared(OneArgument::class, ['test1']);
        $instance = $this->injector->make(OneArgument::class, ['test2']);

        $this->assertSame($this->injector->get(OneArgument::class)->argument1, 'test1');
        $this->assertSame($instance->argument1, 'test2');
    }

    public function testReplacingSharedInstance()
    {
        $this->injector->shared(WithoutConstructor::class);
        $instance = $this->injector->get(OneSimpleClass::class, [WithoutConstructor::make()]);
        $this->assertNotSame($instance->argument1, $this->injector->get(WithoutConstructor::class));
    }

    public function testInterfaceImplementationUsingShared()
    {
        $this->injector->shared(SimpleInterfaceImplementation::class);
        $this->assertSame($this->injector->getImplementation(SimpleInterface::class), SimpleInterfaceImplementation::class);
        $this->assertEquals($this->injector->get(SimpleInterfaceUser::class), $this->injector->get(SimpleInterfaceUser::class));
    }

    public function testInterfaceImplementationUsingSharedFirstWins()
    {
        $this->injector->shared(SimpleInterfaceImplementation::class);
        $this->injector->shared(SimpleInterfaceImplementationTwo::class);
        $this->assertTrue($this->injector->get(SimpleInterfaceUser::class)->argument1 instanceof SimpleInterfaceImplementation);
    }

    public function testInterfaceImplementationExplicitWins()
    {
        $this->injector->shared(SimpleInterfaceImplementation::class);
        $this->injector->setImplementation(SimpleInterface::class, SimpleInterfaceImplementationTwo::class);
        $this->assertSame($this->injector->getImplementation(SimpleInterface::class), SimpleInterfaceImplementationTwo::class);
        $this->assertTrue($this->injector->get(SimpleInterfaceUser::class)->argument1 instanceof SimpleInterfaceImplementationTwo);
    }

    public function testInterfaceImplementationExplicitFail()
    {
        $this->setExpectedException(ImplementationAlreadySet::class);
        $this->injector->setImplementation(SimpleInterface::class, SimpleInterfaceImplementation::class);
        $this->injector->setImplementation(SimpleInterface::class, SimpleInterfaceImplementation::class);
    }

    public function testInterfaceImplementationFail()
    {
        $this->setExpectedException(ImplementationNotFound::class);
        $this->injector->get(SimpleInterfaceUser::class);
    }

    public function testOneArgument()
    {
        $instance1 = OneArgument::make('test1');
        $this->assertSame($instance1->argument1, 'test1');

        // named
        $instance2 = $this->injector->get(OneArgument::class, ['argument1' => 'test1']);
        $this->assertEquals($instance1, $instance2);

        // indexed
        $instance2 = $this->injector->get(OneArgument::class, ['test1']);
        $this->assertEquals($instance1, $instance2);

        // testing with Closure
        $instance2 = $this->injector->get(OneArgument::class, [function () {
            return 'test1';
        }]);
        $this->assertEquals($instance1, $instance2);

        // testing with null
        $instance1 = OneArgument::make(null);
        $this->assertSame($instance1->argument1, null);

        $instance2 = $this->injector->get(OneArgument::class, [null]);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneOptionalArgumentWithNullDefault()
    {
        $instance1 = OneOptionalArgumentWithNullDefault::make();
        $this->assertSame($instance1->argument1, null);

        $instance2 = $this->injector->get(OneOptionalArgumentWithNullDefault::class);
        $this->assertEquals($instance1, $instance2);

        $instance2 = $this->injector->get(OneOptionalArgumentWithNullDefault::class, [null]);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneOptionalArgumentWithArrayDefault()
    {
        $instance1 = OneOptionalArgumentWithArrayDefault::make();
        $this->assertSame($instance1->argument1, []);

        $instance2 = $this->injector->get(OneOptionalArgumentWithArrayDefault::class);
        $this->assertEquals($instance1, $instance2);

        $instance2 = $this->injector->get(OneOptionalArgumentWithArrayDefault::class, []);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneOptionalArgumentWithValueDefault()
    {
        $instance1 = OneOptionalArgumentWithValueDefault::make();
        $this->assertSame($instance1->argument1, 'test');

        $instance2 = $this->injector->get(OneOptionalArgumentWithValueDefault::class);
        $this->assertEquals($instance1, $instance2);

        $instance2 = $this->injector->get(OneOptionalArgumentWithValueDefault::class, ['test']);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneOptionalArgumentWithConstantDefault()
    {
        $instance1 = OneOptionalArgumentWithConstantDefault::make();
        $this->assertSame($instance1->argument1, PHP_INT_MAX);

        $instance2 = $this->injector->get(OneOptionalArgumentWithConstantDefault::class);
        $this->assertEquals($instance1, $instance2);

        $instance2 = $this->injector->get(OneOptionalArgumentWithConstantDefault::class, [PHP_INT_MAX]);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneArrayArgument()
    {
        $instance1 = OneArrayArgument::make(['test1']);
        $this->assertSame($instance1->argument1, ['test1']);

        $instance2 = $this->injector->get(OneArrayArgument::class, [['test1']]);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneOptionalArrayArgumentWithNullDefault()
    {
        $instance1 = OneOptionalArrayArgumentWithNullDefault::make();
        $this->assertSame($instance1->argument1, null);

        $instance2 = $this->injector->get(OneOptionalArrayArgumentWithNullDefault::class);
        $this->assertEquals($instance1, $instance2);

        $instance2 = $this->injector->get(OneOptionalArrayArgumentWithNullDefault::class, [null]);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneOptionalArrayArgumentWithEmptyArrayDefault()
    {
        $instance1 = OneOptionalArrayArgumentWithEmptyArrayDefault::make();
        $this->assertSame($instance1->argument1, []);

        $instance2 = $this->injector->get(OneOptionalArrayArgumentWithEmptyArrayDefault::class);
        $this->assertEquals($instance1, $instance2);

        $instance2 = $this->injector->get(OneOptionalArrayArgumentWithEmptyArrayDefault::class, [[]]);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneOptionalArrayArgumentWithNotEmptyArrayDefault()
    {
        $instance1 = OneOptionalArrayArgumentWithNotEmptyArrayDefault::make();
        $this->assertSame($instance1->argument1, ['test']);

        $instance2 = $this->injector->get(OneOptionalArrayArgumentWithNotEmptyArrayDefault::class);
        $this->assertEquals($instance1, $instance2);

        $instance2 = $this->injector->get(OneOptionalArrayArgumentWithNotEmptyArrayDefault::class, [['test']]);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneOptionalClassArgument()
    {
        $instance1 = OneOptionalClassArgument::make();
        $this->assertSame($instance1->argument1, null);

        $instance2 = $this->injector->get(OneOptionalClassArgument::class);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneClosureArgument()
    {
        // Use the same closure and make hhvm happy
        $closure = function () {
        };

        $instance1 = OneClosureArgument::make($closure);
        $this->assertInstanceOf(Closure::class, $instance1->argument1);

        $instance2 = $this->injector->get(OneClosureArgument::class, [$closure]);
        $this->assertEquals($instance1, $instance2);
    }

    public function testOneOptionalClosureArgument()
    {
        $instance1 = OneOptionalClosureArgument::make();
        $this->assertSame($instance1->argument1, null);

        $instance2 = $this->injector->get(OneOptionalClosureArgument::class);
        $this->assertEquals($instance1, $instance2);
    }

    public function testMultipleArgument()
    {
        $instance1 = MultipleArgument::make('test1', 'test2');

        // named
        $instance2 = $this->injector->get(
            MultipleArgument::class,
            ['argument1' => 'test1', 'argument2' => 'test2']
        );
        $this->assertEquals($instance1, $instance2);

        // named with different order
        $instance2 = $this->injector->get(
            MultipleArgument::class,
            ['argument2' => 'test2', 'argument1' => 'test1']
        );
        $this->assertEquals($instance1, $instance2);

        // indexed
        $instance2 = $this->injector->get(
            MultipleArgument::class,
            ['test1', 'test2']
        );
        $this->assertEquals($instance1, $instance2);

        // indexed with different order (not equals)
        $instance2 = $this->injector->get(
            MultipleArgument::class,
            ['test2', 'test1']
        );
        $this->assertNotEquals($instance1, $instance2);
    }

    public function testMultipleArgumentWithFirstOptionalWithNullDefault()
    {
        $instance1 = MultipleArgumentWithFirstOptionalWithNullDefault::make(null, 'test2');

        // named
        $instance2 = $this->injector->get(
            MultipleArgumentWithFirstOptionalWithNullDefault::class,
            ['argument1' => null, 'argument2' => 'test2']
        );
        $this->assertEquals($instance1, $instance2);

        // named without the optional
        $instance2 = $this->injector->get(
            MultipleArgumentWithFirstOptionalWithNullDefault::class,
            ['argument2' => 'test2']
        );
        $this->assertEquals($instance1, $instance2);

        // indexed
        $instance2 = $this->injector->get(
            MultipleArgumentWithFirstOptionalWithNullDefault::class,
            [null, 'test2']
        );
        $this->assertEquals($instance1, $instance2);
    }
}
