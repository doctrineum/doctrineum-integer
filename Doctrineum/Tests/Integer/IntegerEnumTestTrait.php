<?php
namespace Doctrineum\Tests\Integer;

use Doctrineum\Integer\IntegerEnum;
use Doctrineum\Integer\SelfTypedIntegerEnum;
use Doctrineum\Tests\Scalar\WithToStringTestObject;

trait IntegerEnumTestTrait
{
    /**
     * @return \Doctrineum\Integer\IntegerEnum|\Doctrineum\Integer\SelfTypedIntegerEnum
     */
    protected function getEnumClass()
    {
        return preg_replace('~Test$~', '', get_called_class());
    }

    /** @test */
    public function can_create_enum_instance()
    {
        $enumClass = $this->getEnumClass();
        $instance = $enumClass::getEnum(12345);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf($enumClass, $instance);
    }

    /** @test */
    public function returns_the_same_integer_as_created_with()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum($integer = 12345);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame($integer, $enum->getEnumValue());
        $this->assertSame("$integer", (string)$enum);
    }

    /** @test */
    public function returns_integer_created_from_string_created_with()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum($stringInteger = '12345');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(intval($stringInteger), $enum->getEnumValue());
        $this->assertSame($stringInteger, (string)$enum);
    }

    /** @test */
    public function string_with_integer_and_spaces_is_trimmed_and_accepted()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('  12 ');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(12, $enum->getEnumValue());
        $this->assertSame('12', (string)$enum);
    }

    /**
     * @test
     */
    public function float_without_decimal_is_its_integer_value()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(123.0);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(123, $enum->getEnumValue());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Scalar\Exceptions\UnexpectedValueToEnum
     */
    public function float_with_decimal_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum(12.345);
    }

    /**
     * @test
     */
    public function string_float_without_decimal_is_its_integer_value()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('123.0');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(123, $enum->getEnumValue());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Scalar\Exceptions\UnexpectedValueToEnum
     */
    public function string_float_with_decimal_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum('12.345');
    }

    /**
     * @test
     */
    public function string_with_partial_integer_is_that_integer()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('12foo');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(12, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function object_with_integer_and_to_string_can_be_used()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(new WithToStringTestObject($integer = 12345));
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf('Doctrineum\Scalar\EnumInterface', $enum);
        $this->assertSame($integer, $enum->getEnumValue());
        $this->assertSame("$integer", (string)$enum);
    }

    /**
     * @test
     */
    public function to_string_object_with_non_numeric_string_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(new WithToStringTestObject('foo'));
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(0, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function empty_string_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(0, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function null_is_zero()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(null);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(0, $enum->getEnumValue());
    }

    /** @test */
    public function inherited_enum_with_same_value_lives_in_own_inner_namespace()
    {
        $enumClass = $this->getEnumClass();

        $enum = $enumClass::getEnum($value = 12345);
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTestTrait $this */
        $this->assertInstanceOf($enumClass, $enum);
        $this->assertSame($value, $enum->getEnumValue());
        $this->assertSame("$value", (string)$enum);

        $inDifferentNamespace = $this->getInheritedEnum($value);
        $this->assertInstanceOf($enumClass, $inDifferentNamespace);
        $this->assertSame($enum->getEnumValue(), $inDifferentNamespace->getEnumValue());
        $this->assertNotSame($enum, $inDifferentNamespace);
    }

    /**
     * @param $value
     * @return IntegerEnum|SelfTypedIntegerEnum
     */
    abstract protected function getInheritedEnum($value);
}
