<?php
namespace Doctrineum\Integer;

use Doctrine\DBAL\Types\Type;
use Doctrineum\Tests\Integer\IntegerEnumTestTrait;
use Doctrineum\Tests\Integer\IntegerEnumTypeTestTrait;

class SelfTypedIntegerEnumTest extends \PHPUnit_Framework_TestCase
{

    use IntegerEnumTestTrait;
    use IntegerEnumTypeTestTrait;

    /** @test */
    public function type_name_is_as_expected()
    {
        /** @var \PHPUnit_Framework_TestCase|SelfTypedIntegerEnumTest $this */
        $this->assertSame('self_typed_integer_enum', SelfTypedIntegerEnum::getTypeName());
        $this->assertSame('self_typed_integer_enum', SelfTypedIntegerEnum::SELF_TYPED_INTEGER_ENUM);
        $selfTypedIntegerEnum = SelfTypedIntegerEnum::getType(SelfTypedIntegerEnum::getTypeName());
        $this->assertSame($selfTypedIntegerEnum::getTypeName(), SelfTypedIntegerEnum::getTypeName());
    }

    protected function getInheritedEnum($value)
    {
        if (!Type::hasType(TestInheritedSelfTypedIntegerEnum::getTypeName())) {
            TestInheritedSelfTypedIntegerEnum::registerSelf();
        }
        $enum = TestInheritedSelfTypedIntegerEnum::getEnum($value);

        return $enum;
    }
}

/** inner */
class TestInheritedSelfTypedIntegerEnum extends SelfTypedIntegerEnum
{

}
