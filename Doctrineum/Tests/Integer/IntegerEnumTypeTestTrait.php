<?php
namespace Doctrineum\Tests\Integer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Integer\IntegerEnum;
use Doctrineum\Integer\IntegerEnumType;

trait IntegerEnumTypeTestTrait
{

    // SET UP

    protected function setUp()
    {
        if (Type::hasType(IntegerEnumType::getTypeName())) {
            Type::overrideType(IntegerEnumType::getTypeName(), IntegerEnumType::class);
        } else {
            Type::addType(IntegerEnumType::getTypeName(), IntegerEnumType::class);
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
        return IntegerEnumType::getType(IntegerEnumType::getTypeName());
    }

    // TESTS THEMSELVES

    /** @test */
    public function instance_can_be_obtained()
    {
        $instance = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf(IntegerEnumType::class, $instance);
    }

    /** @test */
    public function sql_declaration_is_valid()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $sql = $enumType->getSQLDeclaration([], $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame('INTEGER', $sql);
    }

    /**
     * @test
     */
    public function enum_as_database_value_is_integer_value_of_that_enum()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enum = \Mockery::mock(IntegerEnum::class);
        $enum->shouldReceive('getValue')
            ->once()
            ->andReturn($value = 12345);
        /** @var IntegerEnum $enum */
        /** @var \PHPUnit_Framework_TestCase|IntegerEnumTypeTestTrait $this */
        $this->assertSame($value, $enumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
    }

    /**
     * @test
     */
    public function integer_to_php_value_gives_enum_with_that_integer()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enum = $enumType->convertToPHPValue($integer = 12345, $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf(IntegerEnum::class, $enum);
        $this->assertSame($integer, $enum->getValue());
        $this->assertSame("$integer", (string)$enum);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function string_integer_to_php_value_causes_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue('12345', $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function null_to_php_value_causes_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue(null, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function empty_string_to_php_value_causes_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue('', $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function float_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue(12345.6789, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function zero_float_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue(0.0, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function false_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue(false, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function true_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue(true, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function array_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue([], $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function resource_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue(tmpfile(), $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function object_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue(new \stdClass(), $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function callback_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::getTypeName());
        $enumType->convertToPHPValue(function () {
        }, $this->getAbstractPlatform());
    }

    /**
     * @return AbstractPlatform
     */
    private function getAbstractPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }
}
