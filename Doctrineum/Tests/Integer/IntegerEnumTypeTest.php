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

    protected function tearDown()
    {
        \Mockery::close();

        $enumTypeClass = $this->getEnumTypeClass();
        $integerEnumType = Type::getType($enumTypeClass::getTypeName());
        /** @var ScalarEnumType $integerEnumType */
        if ($integerEnumType::hasSubTypeEnum($this->getSubTypeEnumClass())) {
            self::assertTrue($integerEnumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
        }
    }

    protected function setUp()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        if (!Type::hasType($enumTypeClass::getTypeName())) {
            Type::addType($enumTypeClass::getTypeName(), $enumTypeClass);
        }
    }

    /**
     * @return \Doctrineum\Integer\IntegerEnumType
     */
    protected function getEnumTypeClass()
    {
        return IntegerEnumType::getClass();
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
     * @return IntegerEnumType
     */
    public function type_instance_can_be_obtained()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $instance = $enumTypeClass::getType($enumTypeClass::getTypeName());
        self::assertInstanceOf($enumTypeClass, $instance);

        return $instance;
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function type_name_is_as_expected(IntegerEnumType $integerEnumType)
    {
        $enumTypeClass = $this->getEnumTypeClass();
        // like self_typed_integer_enum
        $typeName = $this->convertToTypeName($enumTypeClass);
        // like SELF_TYPED_INTEGER_ENUM
        $constantName = strtoupper($typeName);
        self::assertTrue(defined("$enumTypeClass::$constantName"));
        self::assertSame($enumTypeClass::getTypeName(), $typeName);
        self::assertSame($typeName, constant("$enumTypeClass::$constantName"));
        self::assertSame($integerEnumType::getTypeName(), $enumTypeClass::getTypeName());
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
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function sql_declaration_is_valid(IntegerEnumType $integerEnumType)
    {
        $sql = $integerEnumType->getSQLDeclaration([], $this->getAbstractPlatform());
        self::assertSame('INTEGER', $sql);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function sql_default_length_is_ten(IntegerEnumType $integerEnumType)
    {
        $defaultLength = $integerEnumType->getDefaultLength($this->getAbstractPlatform());
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
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function enum_as_database_value_is_integer_value_of_that_enum(IntegerEnumType $integerEnumType)
    {
        $enum = \Mockery::mock(Enum::class);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $enum->shouldReceive('getValue')
            ->once()
            ->andReturn($value = 12345);
        /** @var Enum $enum */
        self::assertSame($value, $integerEnumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
    }

    /**
     * conversions to PHP value
     */

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function integer_to_php_value_gives_enum_with_that_integer(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue($integer = 12345, $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame($integer, $enum->getValue());
        self::assertSame("$integer", (string)$enum);
    }

    /**
     * @return \Doctrineum\Integer\IntegerEnum
     */
    protected function getRegisteredEnumClass()
    {
        return IntegerEnum::getClass();
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function string_integer_to_php_value_gives_enum_with_that_integer(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue($stringInteger = '12345', $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        self::assertSame((int)$stringInteger, $enum->getValue());
        self::assertSame($stringInteger, (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function I_get_null_if_fetched_from_database(IntegerEnumType $integerEnumType)
    {
        self::assertNull($integerEnumType->convertToPHPValue(null, $this->getAbstractPlatform()));
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function It_raises_an_exception_if_get_empty_string_from_database(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue('', $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function float_to_php_value_cause_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue(12345.6789, $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function zero_float_to_php_gives_zero(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue(0.0, $this->getAbstractPlatform());
        self::assertSame(0, $enum->getValue());
        self::assertSame('0', (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function false_to_php_value_gives_zero(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue(false, $this->getAbstractPlatform());
        self::assertSame(0, $enum->getValue());
        self::assertSame('0', (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function true_to_php_gives_one(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue(true, $this->getAbstractPlatform());
        self::assertSame(1, $enum->getValue());
        self::assertSame('1', (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function array_to_php_value_cause_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue([], $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function resource_to_php_value_cause_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue(tmpfile(), $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function object_to_php_value_cause_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue(new \stdClass(), $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function callback_to_php_value_cause_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue(function () {
        }, $this->getAbstractPlatform());
    }

    /**
     * subtype tests
     */

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @return IntegerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function can_register_subtype(IntegerEnumType $integerEnumType)
    {
        self::assertTrue($integerEnumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        self::assertTrue($integerEnumType::hasSubTypeEnum($this->getSubTypeEnumClass()));

        return $integerEnumType;
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function can_unregister_subtype(IntegerEnumType $integerEnumType)
    {
        /**
         * The subtype is unregistered because of tearDown clean up
         * @see IntegerEnumTypeTestTrait::tearDown
         */
        self::assertFalse($integerEnumType::hasSubTypeEnum($this->getSubTypeEnumClass()), 'Subtype should not be registered yet');
        self::assertTrue($integerEnumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        self::assertTrue($integerEnumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
        self::assertFalse($integerEnumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function subtype_returns_proper_enum(IntegerEnumType $integerEnumType)
    {
        self::assertTrue($integerEnumType::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~456~'));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $matchingValueToConvert = 123456789;
        self::assertRegExp($regexp, "$matchingValueToConvert");
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enumFromSubType = $integerEnumType->convertToPHPValue($matchingValueToConvert, $abstractPlatform);
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumFromSubType);
        self::assertSame("$matchingValueToConvert", "$enumFromSubType");
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function default_enum_is_given_if_subtype_does_not_match(IntegerEnumType $integerEnumType)
    {
        self::assertTrue($integerEnumType::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~456~'));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $nonMatchingValueToConvert = 99999999;
        self::assertNotRegExp($regexp, "$nonMatchingValueToConvert");
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enum = $integerEnumType->convertToPHPValue($nonMatchingValueToConvert, $abstractPlatform);
        self::assertNotSame($nonMatchingValueToConvert, $enum);
        self::assertInstanceOf(Enum::class, $enum);
        self::assertSame("$nonMatchingValueToConvert", (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumIsAlreadyRegistered
     */
    public function registering_same_subtype_again_throws_exception(IntegerEnumType $integerEnumType)
    {
        self::assertFalse($integerEnumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
        self::assertTrue($integerEnumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        // registering twice - should thrown an exception
        $integerEnumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~');
    }

    /**
     * @param IntegerEnumType $integerEnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidRegexpFormat
     * @expectedExceptionMessage The given regexp is not enclosed by same delimiters and therefore is not valid: 'foo~'
     */
    public function registering_subtype_with_invalid_regexp_throws_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType::addSubTypeEnum($this->getSubTypeEnumClass(), 'foo~' /* missing opening delimiter */);
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

        $integerEnumType = Type::getType($enumTypeClass::getTypeName());
        $enumSubType = $integerEnumType->convertToPHPValue($value, $this->getPlatform());
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
