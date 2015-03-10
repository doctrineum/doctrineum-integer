<?php
namespace Doctrineum\Tests\Integer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Integer\IntegerEnumType;
use Doctrineum\Scalar\EnumInterface;

trait IntegerEnumTypeTestTrait
{
    /**
     * @return \Doctrineum\Integer\IntegerEnumType|\Doctrineum\Integer\SelfTypedIntegerEnum
     */
    protected function getEnumTypeClass()
    {
        return preg_replace('~Test$~', '', static::class);
    }

    /**
     * @return \Doctrineum\Integer\IntegerEnum|\Doctrineum\Integer\SelfTypedIntegerEnum
     */
    protected function getRegisteredEnumClass()
    {
        return preg_replace('~(Type)?Test$~', '', static::class);
    }

    protected function setUp()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        if (Type::hasType($enumTypeClass::getTypeName())) {
            Type::overrideType($enumTypeClass::getTypeName(), $enumTypeClass);
        } else {
            Type::addType($enumTypeClass::getTypeName(), $enumTypeClass);
        }
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @return \Doctrine\DBAL\Types\Type
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function createObjectInstance()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        return $enumTypeClass::getType($enumTypeClass::getTypeName());
    }

    /** @test */
    public function type_name_is_as_expected()
    {
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame('integer_enum', IntegerEnumType::getTypeName());
        $this->assertSame('integer_enum', IntegerEnumType::INTEGER_ENUM);
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $this->assertSame($enumType::getTypeName(), IntegerEnumType::getTypeName());
    }

    /** @test */
    public function can_create_enum_type_instance()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $instance = $enumTypeClass::getType($enumTypeClass::getTypeName());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf($enumTypeClass, $instance);
    }

    /** @test */
    public function sql_declaration_is_valid()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
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
     * @test
     */
    public function enum_as_database_value_is_integer_value_of_that_enum()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enum = \Mockery::mock(EnumInterface::class);
        $enum->shouldReceive('getEnumValue')
            ->once()
            ->andReturn($value = 12345);
        /** @var EnumInterface $enum */
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertSame($value, $enumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
    }

    /**
     * @test
     */
    public function integer_to_php_value_gives_enum_with_that_integer()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enum = $enumType->convertToPHPValue($integer = 12345, $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        $this->assertSame($integer, $enum->getEnumValue());
        $this->assertSame("$integer", (string)$enum);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function string_integer_to_php_value_causes_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enum = $enumType->convertToPHPValue($stringInteger = '12345', $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        $this->assertSame($stringInteger, $enum->getEnumValue());
        $this->assertSame($stringInteger, (string)$enum);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function null_to_php_value_causes_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(null, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function empty_string_to_php_value_causes_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue('', $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function float_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(12345.6789, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function zero_float_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(0.0, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function false_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(false, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function true_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(true, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function array_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue([], $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function resource_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(tmpfile(), $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function object_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(new \stdClass(), $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function callback_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(function () {
        }, $this->getAbstractPlatform());
    }

}
