<?php
namespace Doctrineum\Tests\Integer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Integer\IntegerEnum;
use Doctrineum\Integer\IntegerEnumType;
use Doctrineum\Scalar\ScalarEnumInterface;
use Doctrineum\Scalar\ScalarEnumType;
use Doctrineum\Tests\SelfRegisteringType\AbstractSelfRegisteringTypeTest;

class IntegerEnumTypeTest extends AbstractSelfRegisteringTypeTest
{

    protected function tearDown()
    {
        $integerEnumType = Type::getType($this->getExpectedTypeName());
        /** @var ScalarEnumType $integerEnumType */
        if ($integerEnumType::hasSubTypeEnum($this->getSubTypeEnumClass())) {
            self::assertTrue($integerEnumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
        }

        parent::tearDown();
    }

    protected function setUp()
    {
        if (!Type::hasType($this->getExpectedTypeName())) {
            Type::addType($this->getExpectedTypeName(), $this->getTypeClass());
        }
    }

    /**
     * @test
     * @return IntegerEnumType|Type
     */
    public function I_can_get_instance()
    {
        $enumTypeClass = $this->getTypeClass();
        $instance = $enumTypeClass::getType($this->getExpectedTypeName());
        self::assertInstanceOf($enumTypeClass, $instance);

        return $instance;
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function sql_declaration_is_valid(IntegerEnumType $integerEnumType)
    {
        $sql = $integerEnumType->getSQLDeclaration([], $this->getAbstractPlatform());
        self::assertSame('INTEGER', $sql);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function sql_default_length_is_ten(IntegerEnumType $integerEnumType)
    {
        $defaultLength = $integerEnumType->getDefaultLength($this->getAbstractPlatform());
        self::assertSame(10, $defaultLength);
    }

    /**
     * @return AbstractPlatform|\Mockery\MockInterface
     */
    private function getAbstractPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function enum_as_database_value_is_integer_value_of_that_enum(IntegerEnumType $integerEnumType)
    {
        $enum = \Mockery::mock(ScalarEnumInterface::class);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $enum->shouldReceive('getValue')
            ->once()
            ->andReturn($value = 12345);
        /** @var ScalarEnumInterface $enum */
        self::assertSame($value, $integerEnumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
    }

    /**
     * conversions to PHP value
     */

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function integer_to_php_value_gives_enum_with_that_integer(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue($integer = 12345, $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredClass(), $enum);
        self::assertSame($integer, $enum->getValue());
        self::assertSame("$integer", (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function string_integer_to_php_value_gives_enum_with_that_integer(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue($stringInteger = '12345', $this->getAbstractPlatform());
        self::assertInstanceOf($this->getRegisteredClass(), $enum);
        self::assertSame((int)$stringInteger, $enum->getValue());
        self::assertSame($stringInteger, (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function I_get_null_if_fetched_from_database(IntegerEnumType $integerEnumType)
    {
        self::assertNull($integerEnumType->convertToPHPValue(null, $this->getAbstractPlatform()));
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function It_raises_an_exception_if_get_empty_string_from_database(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue('', $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function float_to_php_value_cause_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue(12345.6789, $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function zero_float_to_php_gives_zero(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue(0.0, $this->getAbstractPlatform());
        self::assertSame(0, $enum->getValue());
        self::assertSame('0', (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function false_to_php_value_gives_zero(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue(false, $this->getAbstractPlatform());
        self::assertSame(0, $enum->getValue());
        self::assertSame('0', (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function true_to_php_gives_one(IntegerEnumType $integerEnumType)
    {
        $enum = $integerEnumType->convertToPHPValue(true, $this->getAbstractPlatform());
        self::assertSame(1, $enum->getValue());
        self::assertSame('1', (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function array_to_php_value_cause_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue([], $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function resource_to_php_value_cause_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue(tmpfile(), $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToConvert
     */
    public function object_to_php_value_cause_exception(IntegerEnumType $integerEnumType)
    {
        $integerEnumType->convertToPHPValue(new \stdClass(), $this->getAbstractPlatform());
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
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
     * @return IntegerEnumType
     * @test
     * @depends I_can_get_instance
     */
    public function can_register_subtype(IntegerEnumType $integerEnumType)
    {
        self::assertTrue($integerEnumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        self::assertTrue($integerEnumType::hasSubTypeEnum($this->getSubTypeEnumClass()));

        return $integerEnumType;
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends can_register_subtype
     */
    public function can_unregister_subtype(IntegerEnumType $integerEnumType)
    {
        /**
         * The subtype is unregistered because of tearDown clean up
         *
         * @see IntegerEnumTypeTestTrait::tearDown
         */
        self::assertFalse($integerEnumType::hasSubTypeEnum($this->getSubTypeEnumClass()), 'Subtype should not be registered yet');
        self::assertTrue($integerEnumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        self::assertTrue($integerEnumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
        self::assertFalse($integerEnumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
    }

    /**
     * @param IntegerEnumType $integerEnumType
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
         *
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enumFromSubType = $integerEnumType->convertToPHPValue($matchingValueToConvert, $abstractPlatform);
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumFromSubType);
        self::assertSame("$matchingValueToConvert", "$enumFromSubType");
    }

    /**
     * @param IntegerEnumType $integerEnumType
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
         *
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enum = $integerEnumType->convertToPHPValue($nonMatchingValueToConvert, $abstractPlatform);
        self::assertNotSame($nonMatchingValueToConvert, $enum);
        self::assertInstanceOf(ScalarEnumInterface::class, $enum);
        self::assertSame("$nonMatchingValueToConvert", (string)$enum);
    }

    /**
     * @param IntegerEnumType $integerEnumType
     * @test
     * @depends I_can_get_instance
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
     * @test
     * @depends I_can_get_instance
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
    public function different_types_with_same_subtype_regexp_distinguish_them()
    {
        /** @var IntegerEnumType $enumTypeClass */
        $enumTypeClass = $this->getTypeClass();
        if ($enumTypeClass::hasSubTypeEnum($this->getSubTypeEnumClass())) {
            $enumTypeClass::removeSubTypeEnum($this->getSubTypeEnumClass());
        }
        $enumTypeClass::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~[4-6]+~');

        $anotherEnumTypeClass = $this->getAnotherEnumTypeClass();
        $anotherEnumTypeClass::registerSelf();
        if ($anotherEnumTypeClass::hasSubTypeEnum($this->getAnotherSubTypeEnumClass())) {
            $anotherEnumTypeClass::removeSubTypeEnum($this->getAnotherSubTypeEnumClass());
        }
        // regexp is same, sub-type is not
        $anotherEnumTypeClass::addSubTypeEnum($this->getAnotherSubTypeEnumClass(), $regexp);

        $value = 345678;
        self::assertRegExp($regexp, "$value");

        $integerEnumType = Type::getType($this->getExpectedTypeName());
        $enumSubType = $integerEnumType->convertToPHPValue($value, $this->getPlatform());
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumSubType);
        self::assertSame("$value", "$enumSubType");

        $anotherEnumType = Type::getType($this->getExpectedTypeName($anotherEnumTypeClass));
        $anotherEnumSubType = $anotherEnumType->convertToPHPValue($value, $this->getPlatform());
        self::assertInstanceOf($this->getSubTypeEnumClass(), $enumSubType);
        self::assertSame("$value", "$anotherEnumSubType");

        // registered sub-types were different, just regexp was the same - let's test if they are kept separately
        self::assertNotSame($enumSubType, $anotherEnumSubType);
    }

    /**
     * @return AbstractPlatform|\Mockery\MockInterface
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
        return TestSubTypeIntegerEnum::class;
    }

    /**
     * @return string|TestAnotherSubTypeIntegerEnum
     */
    protected function getAnotherSubTypeEnumClass()
    {
        return TestAnotherSubTypeIntegerEnum::class;
    }

    /**
     * @return TestAnotherIntegerEnumType|string
     */
    protected function getAnotherEnumTypeClass()
    {
        return TestAnotherIntegerEnumType::class;
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
    const TEST_ANOTHER_INTEGER_ENUM = 'test_another_integer_enum';

    public function getName()
    {
        return self::TEST_ANOTHER_INTEGER_ENUM;
    }
}