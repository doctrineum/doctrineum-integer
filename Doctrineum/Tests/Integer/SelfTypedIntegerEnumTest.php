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
        if (!Type::hasType(TestInheritedSelfTypedIntegerSubTypeEnum::getTypeName())) {
            TestInheritedSelfTypedIntegerSubTypeEnum::registerSelf();
        }
        $enum = TestInheritedSelfTypedIntegerSubTypeEnum::getEnum($value);

        return $enum;
    }

    /**
     * @return string
     */
    protected function getTestSubTypeEnumClass()
    {
        return TestInheritedSelfTypedIntegerSubTypeEnum::class;
    }

    /**
     * @return string
     */
    protected function getTestAnotherSubTypeEnumClass()
    {
        return TestAnotherSelfTypedIntegerSubTypeEnum::class;
    }

    /**
     * @return string
     */
    protected function getTestAnotherEnumTypeClass()
    {
        return TestAnotherSelfTypedIntegerEnum::class;
    }

}

/** inner */
class TestInheritedSelfTypedIntegerSubTypeEnum extends SelfTypedIntegerEnum
{

}

class TestAnotherSelfTypedIntegerSubTypeEnum extends SelfTypedIntegerEnum
{

}

class TestAnotherSelfTypedIntegerEnum extends SelfTypedIntegerEnum
{

}
