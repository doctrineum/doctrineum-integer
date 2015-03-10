<?php
namespace Doctrineum\Integer;

use Doctrine\DBAL\Types\Type;
use Doctrineum\Tests\Integer\IntegerEnumTestTrait;
use Doctrineum\Tests\Integer\IntegerEnumTypeTestTrait;

class SelfTypedIntegerEnumTest extends \PHPUnit_Framework_TestCase
{

    use IntegerEnumTestTrait;
    use IntegerEnumTypeTestTrait;

    protected function getEnumTypeConstantName()
    {
        return SelfTypedIntegerEnum::SELF_TYPED_INTEGER_ENUM;
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
