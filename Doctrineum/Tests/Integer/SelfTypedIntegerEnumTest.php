<?php
namespace Doctrineum\Integer;

use Doctrine\DBAL\Types\Type;
use Doctrineum\Tests\Integer\IntegerEnumTestTrait;
use Doctrineum\Tests\Integer\IntegerEnumTypeTestTrait;

class SelfTypedIntegerEnumTest extends \PHPUnit_Framework_TestCase
{

    use IntegerEnumTestTrait;
    use IntegerEnumTypeTestTrait;

    /**
     * Overloaded parent test to test self-registration
     *
     * @test
     */
    public function can_be_registered()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumTypeClass::registerSelf();
        $this->assertTrue(Type::hasType($enumTypeClass::getTypeName()));
    }

    /**
     * @test
     * @depends can_be_registered
     */
    public function repeated_self_registration_returns_false()
    {
        $this->assertFalse(SelfTypedIntegerEnum::registerSelf());
    }

    protected function getInheritedEnum($value)
    {
        if (!Type::hasType(TestInheritedSelfTypedIntegerEnum::getTypeName())) {
            TestInheritedSelfTypedIntegerEnum::registerSelf();
        }
        $enum = TestInheritedSelfTypedIntegerEnum::getEnum($value);

        return $enum;
    }

    /**
     * @return string
     */
    protected function getSubTypeEnumClass()
    {
        return 'Doctrineum\Integer\TestSubTypeSelfTypedIntegerEnum';
    }

    /**
     * @return string
     */
    protected function getAnotherSubTypeEnumClass()
    {
        return 'Doctrineum\Integer\TestAnotherSubTypeSelfTypedIntegerEnum';
    }

    /**
     * @return string
     */
    protected function getAnotherEnumTypeClass()
    {
        return 'Doctrineum\Integer\TestAnotherSelfTypedIntegerEnum';
    }

}

/** inner */
class TestInheritedSelfTypedIntegerEnum extends SelfTypedIntegerEnum
{

}

class TestSubTypeSelfTypedIntegerEnum extends SelfTypedIntegerEnum
{

}

class TestAnotherSubTypeSelfTypedIntegerEnum extends SelfTypedIntegerEnum
{

}

class TestAnotherSelfTypedIntegerEnum extends SelfTypedIntegerEnum
{

}
