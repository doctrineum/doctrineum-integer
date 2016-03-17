<?php
namespace Doctrineum\Tests\Integer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Integer\IntegerEnum;
use Doctrineum\Integer\IntegerEnumType;
use Doctrineum\Scalar\Enum;
use Doctrineum\Scalar\ScalarEnumType;

class IntegerEnumTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Doctrineum\Integer\IntegerEnumType
     */
    protected function getEnumTypeClass()
    {
        return IntegerEnumType::getClass();
    }

    /**
     * @return \Doctrineum\Integer\IntegerEnum
     */
    protected function getRegisteredEnumClass()
    {
        return IntegerEnum::getClass();
    }

    protected function setUp()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        if (!Type::hasType($enumTypeClass::getTypeName())) {
            Type::addType($enumTypeClass::getTypeName(), $enumTypeClass);
        }
    }

    protected function tearDown()
    {
        \Mockery::close();

        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = Type::getType($enumTypeClass::getTypeName());
        /** @var ScalarEnumType $enumType */
        if ($enumType::hasSubTypeEnum($this->getSubTypeEnumClass())) {
            self::assertTrue($enumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
        }
    }

    /**
     * @test
     */
    public function can_be_registered()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        if (!Type::hasType($enumTypeClass::getTypeName())) {
            Type::addType($enumTypeClass::getTypeName(), $enumTypeClass);
        }
        self::assertTrue(Type::hasType($enumTypeClass::getTypeName()));
    }

    /**
     * @test
     */
    public function type_instance_can_be_obtained()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $instance = $enumTypeClass::getType($enumTypeClass::getTypeName());
        self::assertInstanceOf($enumTypeClass, $instance);

        return $instance;
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function type_name_is_as_expected(ScalarEnumType $enumType)
    {
        $enumTypeClass = $this->getEnumTypeClass();
        // like self_typed_integer_enum
        $typeName = $this->convertToTypeName($enumTypeClass);
        // like SELF_TYPED_INTEGER_ENUM
        $constantName = strtoupper($typeName);
        self::assertTrue(defined("$enumTypeClass::$constantName"));
        self::assertSame($enumTypeClass::getTypeName(), $typeName);
        self::assertSame($typeName, constant("$enumTypeClass::$constantName"));
        self::assertSame($enumType::getTypeName(), $enumTypeClass::getTypeName());
    }

    /**
     * @param string $className
     *
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
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function sql_declaration_is_valid(ScalarEnumType $enumType)
    {
        $sql = $enumType->getSQLDeclaration([], $this->getAbstractPlatform());
        self::assertSame('INTEGER', $sql);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function sql_default_length_is_ten(ScalarEnumType $enumType)
    {
        $defaultLength = $enumType->getDefaultLength($this->getAbstractPlatform());
        self::assertSame(10, $defaultLength);
    }

    /**
     * @return AbstractPlatform
     */
    private function getAbstractPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function enum_as_database_value_is_integer_value_of_that_enum(ScalarEnumType $enumType)
    {
        $enum = \Mockery::mock(Enum::class);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $enum->shouldReceive('getValue')
            ->once()
            ->andReturn($value = 12345);
        /** @var Enum $enum */
        self::assertSame($value, $enumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
    }

    /**
     * conversions to PHP value
     */

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function integer_to_php_value_gives_enum_with_that_integer(ScalarEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($integer = 12345, $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame($integer, $enum->getValue());
        self::assertSame("$integer", (string)$enum);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function string_integer_to_php_value_gives_enum_with_that_integer(ScalarEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($stringInteger = '12345', $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame((int)$stringInteger, $enum->getValue());
        self::assertSame($stringInteger, (string)$enum);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function null_to_php_value_gives_zero(ScalarEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue(null, $this->getAbstractPlatform());
        self::assertSame(0, $enum->getValue());
        self::assertSame('0', (string)$enum);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function empty_string_to_php_gives_zero(ScalarEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue('', $this->getAbstractPlatform());
        self::assertSame(0, $enum->getValue());
        self::assertSame('0', (string)$enum);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function float_to_php_value_cause_exception(ScalarEnumType $enumType)
    {
        $enumType->convertToPHPValue(12345.6789, $this->getAbstractPlatform());
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function zero_float_to_php_gives_zero(ScalarEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue(0.0, $this->getAbstractPlatform());
        self::assertSame(0, $enum->getValue());
        self::assertSame('0', (string)$enum);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function false_to_php_value_gives_zero(ScalarEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue(false, $this->getAbstractPlatform());
        self::assertSame(0, $enum->getValue());
        self::assertSame('0', (string)$enum);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function true_to_php_gives_one(ScalarEnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue(true, $this->getAbstractPlatform());
        self::assertSame(1, $enum->getValue());
        self::assertSame('1', (string)$enum);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function array_to_php_value_cause_exception(ScalarEnumType $enumType)
    {
        $enumType->convertToPHPValue([], $this->getAbstractPlatform());
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function resource_to_php_value_cause_exception(ScalarEnumType $enumType)
    {
        $enumType->convertToPHPValue(tmpfile(), $this->getAbstractPlatform());
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function object_to_php_value_cause_exception(ScalarEnumType $enumType)
    {
        $enumType->convertToPHPValue(new \stdClass(), $this->getAbstractPlatform());
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function callback_to_php_value_cause_exception(ScalarEnumType $enumType)
    {
        $enumType->convertToPHPValue(function () {
        }, $this->getAbstractPlatform());
    }

    /**
     * subtype tests
     */

    /**
     * @param ScalarEnumType $enumType
     *
     * @return IntegerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function can_register_subtype(ScalarEnumType $enumType)
    {
        self::assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        self::assertTrue($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));

        return $enumType;
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function can_unregister_subtype(ScalarEnumType $enumType)
    {
        /**
         * The subtype is unregistered because of tearDown clean up
         * @see IntegerEnumTypeTestTrait::tearDown
         */
        self::assertFalse($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()), 'Subtype should not be registered yet');
        self::assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        self::assertTrue($enumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
        self::assertFalse($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function subtype_returns_proper_enum(ScalarEnumType $enumType)
    {
        self::assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~456~'));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $matchingValueToConvert = 123456789;
        self::assertRegExp($regexp, "$matchingValueToConvert");
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enumFromSubType = $enumType->convertToPHPValue($matchingValueToConvert, $abstractPlatform);
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumFromSubType);
        self::assertSame("$matchingValueToConvert", "$enumFromSubType");
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function default_enum_is_given_if_subtype_does_not_match(ScalarEnumType $enumType)
    {
        self::assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~456~'));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $nonMatchingValueToConvert = 99999999;
        self::assertNotRegExp($regexp, "$nonMatchingValueToConvert");
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enum = $enumType->convertToPHPValue($nonMatchingValueToConvert, $abstractPlatform);
        self::assertNotSame($nonMatchingValueToConvert, $enum);
        self::assertInstanceOf(Enum::class, $enum);
        self::assertSame("$nonMatchingValueToConvert", (string)$enum);
    }

    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumIsAlreadyRegistered
     */
    public function registering_same_subtype_again_throws_exception(ScalarEnumType $enumType)
    {
        self::assertFalse($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
        self::assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        // registering twice - should thrown an exception
        $enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~');
    }


    /**
     * @param ScalarEnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidRegexpFormat
     * @expectedExceptionMessage The given regexp is not enclosed by same delimiters and therefore is not valid: 'foo~'
     */
    public function registering_subtype_with_invalid_regexp_throws_exception(ScalarEnumType $enumType)
    {
        $enumType::addSubTypeEnum($this->getSubTypeEnumClass(), 'foo~' /* missing opening delimiter */);
    }

    /**
     * @test
     */
    public function can_register_another_enum_type()
    {
        $anotherEnumType = $this->getAnotherEnumTypeClass();
        if (!$anotherEnumType::isRegistered()) {
            self::assertTrue($anotherEnumType::registerSelf());
        } else {
            self::assertFalse($anotherEnumType::registerSelf());
        }

        self::assertTrue($anotherEnumType::isRegistered());
    }

    /**
     * @test
     *
     * @depends can_register_another_enum_type
     */
    public function different_types_with_same_subtype_regexp_distinguish_them()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        if ($enumTypeClass::hasSubTypeEnum($this->getSubTypeEnumClass())) {
            $enumTypeClass::removeSubTypeEnum($this->getSubTypeEnumClass());
        }
        $enumTypeClass::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~[4-6]+~');

        $anotherEnumTypeClass = $this->getAnotherEnumTypeClass();
        if ($anotherEnumTypeClass::hasSubTypeEnum($this->getAnotherSubTypeEnumClass())) {
            $anotherEnumTypeClass::removeSubTypeEnum($this->getAnotherSubTypeEnumClass());
        }
        // regexp is same, sub-type is not
        $anotherEnumTypeClass::addSubTypeEnum($this->getAnotherSubTypeEnumClass(), $regexp);

        $value = 345678;
        self::assertRegExp($regexp, "$value");

        $enumType = Type::getType($enumTypeClass::getTypeName());
        $enumSubType = $enumType->convertToPHPValue($value, $this->getPlatform());
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumSubType);
        self::assertSame("$value", "$enumSubType");

        $anotherEnumType = Type::getType($anotherEnumTypeClass::getTypeName());
        $anotherEnumSubType = $anotherEnumType->convertToPHPValue($value, $this->getPlatform());
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumSubType);
        self::assertSame("$value", "$anotherEnumSubType");

        // registered sub-types were different, just regexp was the same - let's test if they are kept separately
        self::assertNotSame($enumSubType, $anotherEnumSubType);
    }

    /**
     * @return AbstractPlatform
     */
    protected function getPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }

    /**
     * @return string|TestSubTypeIntegerEnum
     */
    protected function getSubTypeEnumClass()
    {
        return TestSubTypeIntegerEnum::getClass();
    }

    /**
     * @return string|TestAnotherSubTypeIntegerEnum
     */
    protected function getAnotherSubTypeEnumClass()
    {
        return TestAnotherSubTypeIntegerEnum::getClass();
    }

    /**
     * @return TestAnotherIntegerEnumType|string
     */
    protected function getAnotherEnumTypeClass()
    {
        return TestAnotherIntegerEnumType::getClass();
    }

}

/** inner */
class TestSubTypeIntegerEnum extends IntegerEnum
{

}

class TestAnotherSubTypeIntegerEnum extends IntegerEnum
{

}

class TestAnotherIntegerEnumType extends IntegerEnumType
{

}
