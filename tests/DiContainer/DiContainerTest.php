<?php

namespace Test\DiContainer;

use PHP2\App\blog\ClassWithDependencies;
use PHP2\App\blog\ClassWithOtherClass;
use PHP2\App\blog\ClassWithoutDependencies;
use PHP2\App\Container\DiContainer;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DiContainerTest extends TestCase
{
    public function testItResolveClassWithoutDependencies()
    {
        $container = new DiContainer();

        $object = $container->get(ClassWithoutDependencies::class);

        $this->assertInstanceOf(ClassWithoutDependencies::class, $object);

    }

    public function testItResolveClassWithParameter()
    {
        $container = new DiContainer();
        $container->bind(ClassWithDependencies::class, new ClassWithDependencies(333));

        $object = $container->get(ClassWithDependencies::class);
        $this->assertInstanceOf(ClassWithDependencies::class, $object);
    }

    public function testItResolveClassWithRightParameter()
    {
        $container = new DiContainer();
        $container->bind(ClassWithDependencies::class, new ClassWithDependencies(333));

        $object = $container->get(ClassWithDependencies::class);
        $this->assertSame(333, $object->getValue());
    }

    public function testItResolveClassWithAnotherClassWithRightParameter()
    {
        $container = new DiContainer();
        $container->bind(ClassWithDependencies::class, new ClassWithDependencies(333));

        $object = $container->get(ClassWithOtherClass::class);
        $this->assertSame("это значение 555Спасибо за внимание.", $object->getValue());
    }

    public function testItResolveClassOnInterface()
    {
        $container = new DiContainer();

        $container->bind(UserRepositoryInterface::class, UserRepository::class);
        $object = $container->get(UserRepositoryInterface::class);

        $this->assertInstanceOf(UserRepositoryInterface::class, $object);
    }

}