<?php
namespace Doctrineum\Integer;

use Doctrineum\Tests\Integer\IntegerEnumTypeTestTrait;

class SelfTypedIntegerEnumTest extends IntegerEnumTest
{

    use IntegerEnumTypeTestTrait;

    /** @test */
    public function type_name_is_as_expected()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        /** @var \PHPUnit_Framework_TestCase|SelfTypedIntegerEnumTest $this */
        $this->assertSame('self_typed_integer_enum', $enumTypeClass::getTypeName());
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $this->assertSame($enumType::getTypeName(), $enumTypeClass::getTypeName());
    }
}
