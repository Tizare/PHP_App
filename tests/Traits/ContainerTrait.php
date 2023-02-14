<?php

namespace Test\Traits;

use PHP2\App\Container\DiContainer;

trait ContainerTrait
{
    private DiContainer $container;

    private function getContainer(): DiContainer
    {
        $this->container = $this->container ?? require __DIR__ . '\..\..\public\autoload_runtime.php';
        return $this->container;
    }
}