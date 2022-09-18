<?php

namespace PHP2\App\blog;

class ClassWithoutDependencies
{
    private string $myValue = "Спасибо за внимание.";

    public function getMyValue(): string
    {
        return $this->myValue;
    }

}