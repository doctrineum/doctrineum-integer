<?php
namespace Doctrineum\Tests\Integer;

use Doctrineum\Generic\EnumInterface;
use Doctrineum\Tests\Generic\WithToStringTestObject;

trait IntegerEnumTestTrait
{
    /**
     * @return \Doctrineum\Integer\IntegerEnum|\Doctrineum\Integer\IntegerSelfTypedEnum
     */
    protected function getEnumClass()
    {
        return preg_replace('~Test$~', '', static::class);
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
     * @expectedException \Doctrineum\Generic\Exceptions\UnexpectedValueToEnum
     */
    public function float_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum(12.345);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\UnexpectedValueToEnum
     */
    public function string_float_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum('12.345');
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\UnexpectedValueToEnum
     */
    public function string_with_partial_integer_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum('12foo');
    }

    /**
     * @test
     */
    public function object_with_integer_and_to_string_can_be_used()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum(new WithToStringTestObject($integer = 12345));
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf(EnumInterface::class, $enum);
        $this->assertSame($integer, $enum->getEnumValue());
        $this->assertSame("$integer", (string)$enum);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\UnexpectedValueToEnum
     */
    public function object_with_non_numeric_string_cause_exception_even_if_to_string_convertible()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum(new WithToStringTestObject('foo'));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function empty_string_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum('');
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function non_integer_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum(null);
    }
}

