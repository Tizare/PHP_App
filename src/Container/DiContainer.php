<?php

namespace PHP2\App\Container;

use PHP2\App\Exceptions\NotFoundException;
use ReflectionClass;

class DiContainer
{
    private array $resolvers = [];

    public function bind(string $abstract, $resolver)
    {
        $this->resolvers[$abstract] = $resolver;
    }

    /**
     * @throws NotFoundException
     */
    public function get(string $abstract)
    {
        if(array_key_exists($abstract, $this->resolvers)) {
            $concrete = $this->resolvers[$abstract];

            if (is_object($concrete)) {
                return $concrete;
            }

            return $this->get($concrete);
        }

        if(!class_exists($abstract)){
            throw new NotFoundException("Class $abstract not found!");
        }

        $reflection = new ReflectionClass($abstract);
        $construct = $reflection->getConstructor();

        if(!$construct) {
            return new $abstract;
        }

        $parameters = [];

        foreach ($construct->getParameters() as $parameter) {
            $parentAbstract = $parameter->getType()->getName();
            $parameters[] = $this->get($parentAbstract);
        }

        return new $abstract(...$parameters);
    }
}