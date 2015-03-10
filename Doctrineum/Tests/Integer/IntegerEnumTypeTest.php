<?php
namespace Doctrineum\Integer;

use Doctrineum\Tests\Integer\IntegerEnumTypeTestTrait;

class IntegerEnumTypeTest extends \PHPUnit_Framework_TestCase
{

    use IntegerEnumTypeTestTrait;

    protected function getEnumTypeConstantName()
    {
        return IntegerEnumType::INTEGER_ENUM;
    }
}
