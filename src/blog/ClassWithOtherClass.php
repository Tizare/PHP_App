<?php

namespace PHP2\App\blog;

class ClassWithOtherClass
{
    private ClassWithDependencies $classWithDependencies;
    private string $bye;
    private string $classValue = "это значение ";

    public function __construct()
    {
        $this->classWithDependencies = new ClassWithDependencies(555);
        $this->bye = (new ClassWithoutDependencies())->getMyValue();
    }


    public function getValue(): string
    {
        return $this->classValue . $this->classWithDependencies->getValue() . $this->bye;
    }







}