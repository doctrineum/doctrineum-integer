<?php
namespace Doctrineum\Integer;

use Doctrineum\Tests\Integer\IntegerEnumTestTrait;

class IntegerEnumTest extends \PHPUnit_Framework_TestCase
{
    use IntegerEnumTestTrait;

    protected function getInheritedEnum($value)
    {
        return new TestInheritedIntegerEnum($value);
    }
}

/** inner */
class TestInheritedIntegerEnum extends IntegerEnum
{

}
