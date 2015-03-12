<?php
namespace Doctrineum\Tests\Integer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Integer\IntegerEnum;
use Doctrineum\Integer\IntegerEnumType;
use Doctrineum\Scalar\EnumInterface;
use Doctrineum\Scalar\EnumType;

trait IntegerEnumTypeTestTrait
{
    /**
     * @return \Doctrineum\Integer\IntegerEnumType|\Doctrineum\Integer\SelfTypedIntegerEnum
     */
    protected function getEnumTypeClass()
    {
        // like IntegerEnumType
        return preg_replace('~Test$~', '', static::class);
    }

    /**
     * @return \Doctrineum\Integer\IntegerEnum|\Doctrineum\Integer\SelfTypedIntegerEnum
     */
    protected function getRegisteredEnumClass()
    {
        // like IntegerEnum
        return preg_replace('~(Type)?Test$~', '', static::class);
    }

    protected function tearDown()
    {
        \Mockery::close();

        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = Type::getType($enumTypeClass::getTypeName(), $enumTypeClass);
        /** @var EnumType $enumType */
        if ($enumType::hasSubTypeEnum($this->getTestSubTypeEnumClass())) {
            /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
            $this->assertTrue($enumType::removeSubTypeEnum($this->getTestSubTypeEnumClass()));
        }
    }

    /**
     * @test
     */
    public function can_be_registered()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        Type::addType($enumTypeClass::getTypeName(), $enumTypeClass);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertTrue(Type::hasType($enumTypeClass::getTypeName()));
    }

    /**
     * @test
     * @depends can_be_registered
     */
    public function type_instance_can_be_obtained()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $instance = $enumTypeClass::getType($enumTypeClass::getTypeName());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf($enumTypeClass, $instance);

        return $instance;
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function type_name_is_as_expected(EnumType $enumType)
    {
        $enumTypeClass = $this->getEnumTypeClass();
        // like self_typed_integer_enum
        $typeName = $this->convertToTypeName($enumTypeClass);
        // like SELF_TYPED_INTEGER_ENUM
        $constantName = strtoupper($typeName);
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertTrue(defined("$enumTypeClass::$constantName"));
        $this->assertSame($enumTypeClass::getTypeName(), $typeName);
        $this->assertSame($typeName, constant("$enumTypeClass::$constantName"));
        $this->assertSame($enumType::getTypeName(), $enumTypeClass::getTypeName());
    }

    /**
     * @param string $className
     * @return string
     */
    private function convertToTypeName($className)
    {
        $withoutType = preg_replace('~Type$~', '', $className);
        $parts = explode('\\', $withoutType);
        $baseClassName = $parts[count($parts) - 1];
        preg_match_all('~(?<words>[A-Z][^A-Z]+)~', $baseClassName, $matches);
        $concatenated = implode('_', $matches['words']);

        return strtolower($concatenated);
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function sql_declaration_is_valid(EnumType $enumType)
    {
        $sql = $enumType->getSQLDeclaration([], $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame('INTEGER', $sql);
    }

    /**
     * @return AbstractPlatform
     */
    private function getAbstractPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function enum_as_database_value_is_integer_value_of_that_enum(EnumType $enumType)
    {
        $enum = \Mockery::mock(EnumInterface::class);
        $enum->shouldReceive('getEnumValue')
            ->once()
            ->andReturn($value = 12345);
        /** @var EnumInterface $enum */
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertSame($value, $enumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
    }

    /**
     * conversions to PHP value
     */

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function integer_to_php_value_gives_enum_with_that_integer(EnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($integer = 12345, $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        $this->assertSame($integer, $enum->getEnumValue());
        $this->assertSame("$integer", (string)$enum);
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function string_integer_to_php_value_causes_exception(EnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($stringInteger = '12345', $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        $this->assertSame($stringInteger, $enum->getEnumValue());
        $this->assertSame($stringInteger, (string)$enum);
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function null_to_php_value_causes_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(null, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function empty_string_to_php_value_causes_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue('', $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function float_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(12345.6789, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function zero_float_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(0.0, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function false_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(false, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function true_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(true, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function array_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue([], $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function resource_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(tmpfile(), $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function object_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(new \stdClass(), $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function callback_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(function () {
        }, $this->getAbstractPlatform());
    }

    /**
     * subtype tests
     */

    /**
     * @param EnumType $enumType
     * @return IntegerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function can_register_subtype(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertTrue($enumType::addSubTypeEnum($this->getTestSubTypeEnumClass(), '~foo~'));
        $this->assertTrue($enumType::hasSubTypeEnum($this->getTestSubTypeEnumClass()));

        return $enumType;
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function can_unregister_subtype(EnumType $enumType)
    {
        /**
         * @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this
         *
         * The subtype is unregistered because of tearDown clean up
         * @see IntegerEnumTypeTestTrait::tearDown
         */
        $this->assertFalse($enumType::hasSubTypeEnum($this->getTestSubTypeEnumClass()), 'Subtype should not be registered yet');
        $this->assertTrue($enumType::addSubTypeEnum($this->getTestSubTypeEnumClass(), '~foo~'));
        $this->assertTrue($enumType::removeSubTypeEnum($this->getTestSubTypeEnumClass()));
        $this->assertFalse($enumType::hasSubTypeEnum($this->getTestSubTypeEnumClass()));
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function subtype_returns_proper_enum(EnumType $enumType)
    {
        /**
         * @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this
         */
        $this->assertTrue($enumType::addSubTypeEnum($this->getTestSubTypeEnumClass(), $regexp = '~456~'));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $matchingValueToConvert = 123456789;
        $this->assertRegExp($regexp, "$matchingValueToConvert");
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enumFromSubType = $enumType->convertToPHPValue($matchingValueToConvert, $abstractPlatform);
        $this->assertInstanceOf($this->getTestSubTypeEnumClass(), $enumFromSubType);
        $this->assertSame("$matchingValueToConvert", "$enumFromSubType");
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function default_enum_is_given_if_subtype_does_not_match(EnumType $enumType)
    {
        /**
         * @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this
         */
        $this->assertTrue($enumType::addSubTypeEnum($this->getTestSubTypeEnumClass(), $regexp = '~456~'));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $nonMatchingValueToConvert = 99999999;
        $this->assertNotRegExp($regexp, "$nonMatchingValueToConvert");
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enum = $enumType->convertToPHPValue($nonMatchingValueToConvert, $abstractPlatform);
        $this->assertNotSame($nonMatchingValueToConvert, $enum);
        $this->assertInstanceOf(EnumInterface::class, $enum);
        $this->assertSame("$nonMatchingValueToConvert", (string)$enum);
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumIsAlreadyRegistered
     */
    public function registering_same_subtype_again_throws_exception(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertFalse($enumType::hasSubTypeEnum($this->getTestSubTypeEnumClass()));
        $this->assertTrue($enumType::addSubTypeEnum($this->getTestSubTypeEnumClass(), '~foo~'));
        // registering twice - should thrown an exception
        $enumType::addSubTypeEnum($this->getTestSubTypeEnumClass(), '~foo~');
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidClassForSubTypeEnum
     */
    public function registering_non_existing_subtype_class_throws_exception(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $enumType::addSubTypeEnum('NonExistingClassName', '~foo~');
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidClassForSubTypeEnum
     */
    public function registering_subtype_class_without_proper_method_throws_exception(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $enumType::addSubTypeEnum(\stdClass::class, '~foo~');
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidRegexpFormat
     * @expectedExceptionMessage The given regexp is not enclosed by same delimiters and therefore is not valid: 'foo~'
     */
    public function registering_subtype_with_invalid_regexp_throws_exception(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $enumType::addSubTypeEnum($this->getTestSubTypeEnumClass(), 'foo~' /* missing opening delimiter */);
    }

    /**
     * @return string
     */
    protected function getTestSubTypeEnumClass()
    {
        return TestSubTypeEnum::class;
    }

    /**
     * @test
     */
    public function can_register_another_enum_type()
    {
        /** @var IntegerEnumType $anotherEnumType */
        $anotherEnumType = $this->getTestAnotherEnumTypeClass();
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        if (!$anotherEnumType::isRegistered()) {
            $this->assertTrue($anotherEnumType::registerSelf());
        } else {
            $this->assertFalse($anotherEnumType::registerSelf());
        }

        $this->assertTrue($anotherEnumType::isRegistered());
    }

    /**
     * @test
     *
     * @depends can_register_another_enum_type
     */
    public function different_types_with_same_subtype_regexp_distinguish_them()
    {
        $enumType = $this->getEnumTypeClass();
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        if ($enumType::hasSubTypeEnum($this->getTestSubTypeEnumClass())) {
            $enumType::removeSubTypeEnum($this->getTestSubTypeEnumClass());
        }
        $enumType::addSubTypeEnum($this->getTestSubTypeEnumClass(), $regexp = '~[4-6]+~');

        /** @var TestAnotherEnumType $anotherEnumType */
        $anotherEnumType = $this->getTestAnotherEnumTypeClass();
        if ($anotherEnumType::hasSubTypeEnum($this->getTestAnotherSubTypeEnumClass())) {
            $anotherEnumType::removeSubTypeEnum($this->getTestAnotherSubTypeEnumClass());
        }
        // regexp is same, sub-type is not
        $anotherEnumType::addSubTypeEnum($this->getTestAnotherSubTypeEnumClass(), $regexp);

        $value = 345678;
        $this->assertRegExp($regexp, "$value");

        $enumType = $enumType::getIt();
        $enumSubType = $enumType->convertToPHPValue($value, $this->getPlatform());
        $this->assertInstanceOf($this->getTestSubTypeEnumClass(), $enumSubType);
        $this->assertSame("$value", "$enumSubType");

        $anotherEnumType = $anotherEnumType::getIt();
        $anotherEnumSubType = $anotherEnumType->convertToPHPValue($value, $this->getPlatform());
        $this->assertInstanceOf($this->getTestSubTypeEnumClass(), $enumSubType);
        $this->assertSame("$value", "$anotherEnumSubType");

        // registered sub-types were different, just regexp was the same - let's test if they are kept separately
        $this->assertNotSame($enumSubType, $anotherEnumSubType);
    }

    /**
     * @return AbstractPlatform
     */
    protected function getPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }

    /**
     * @return string
     */
    protected function getTestAnotherSubTypeEnumClass()
    {
        return TestAnotherSubTypeEnum::class;
    }

    /**
     * @return string
     */
    protected function getTestAnotherEnumTypeClass()
    {
        return TestAnotherEnumType::class;
    }

}

/** inner */
class TestSubTypeEnum extends IntegerEnum
{

}

class TestAnotherSubTypeEnum extends IntegerEnum
{

}

class TestAnotherEnumType extends IntegerEnumType
{

}
