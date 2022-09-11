<?php

namespace Test\Arguments;

use PHP2\App\Argument\Argument;
use PHP2\App\Exceptions\ArgumentException;
use PHPUnit\Framework\TestCase;

class ArgumentTest extends TestCase
{
    public function argumentsProvider(): iterable
    {
        return [
            ['some_value', 'some_value'],
            ['Vasy', 'Vasy'],
            ['VasY', 'VasY'],
            [123, '123'],
            [12.3, '12.3'],
        ];
    }

    /**
     * @dataProvider argumentsProvider
     */
    public function testItReturnArgumentValueByNameInString($inputValue, $expectedValue): void
    {
        $arguments = new Argument(['some_key' => $inputValue]);

        $value = $arguments->get('some_key');

        $this->assertSame($expectedValue, $value);
    }

    public function testItThrowAnExceptionWhenArgumentIsAbsent(): void
    {
        $arguments = new Argument([]);

        $this->expectException(ArgumentException::class);

        $this->expectExceptionMessage("No such argument - some_key");

        $arguments->get('some_key');
    }


    public function testItReturnsObjectOfArgument(): void
    {
        $input = ['1', 'username=username', '2', 'name=name', 'surname=surname', 'name', 'username'];
        $expect = ['username' => 'username', 'name' => 'name', 'surname' => 'surname'];
        $expectValue = new Argument($expect);

        $value = Argument::fromArgv($input);

        $this->assertEquals($expectValue, $value);
    }

    public function testItMissFreeSpacesWhenCreateArgument(): void
    {
        $input = [' ',  ' '];
        $expect = [];
        $expectValue = new Argument($expect);

        $value = new Argument($input);

        $this->assertEquals($expectValue, $value);
    }

}