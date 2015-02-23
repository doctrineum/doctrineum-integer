<?php
namespace Doctrineum\Integer;

class IntegerEnumTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function can_create_instance()
    {
        $instance = IntegerEnum::get(12345);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf(IntegerEnum::class, $instance);
    }

    /** @test */
    public function returns_the_same_integer_as_created_with()
    {
        $enum = IntegerEnum::get($integer = 12345);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame($integer, $enum->getValue());
        $this->assertSame("$integer", (string)$enum);
    }

    /** @test */
    public function returns_integer_created_from_string_created_with()
    {
        $enum = IntegerEnum::get($stringInteger = '12345');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(intval($stringInteger), $enum->getValue());
        $this->assertSame($stringInteger, (string)$enum);
    }

    /**
     * @test
     */
    public function string_with_integer_and_spaces_is_trimmed_and_accepted()
    {
        $enum = IntegerEnum::get('  12 ');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame(12, $enum->getValue());
        $this->assertSame('12', (string)$enum);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\UnexpectedValueToEnum
     */
    public function float_cause_exception()
    {
        IntegerEnum::get(12.345);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\UnexpectedValueToEnum
     */
    public function string_float_cause_exception()
    {
        IntegerEnum::get('12.345');
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\UnexpectedValueToEnum
     */
    public function string_with_partial_integer_cause_exception()
    {
        IntegerEnum::get('12foo');
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\UnexpectedValueToEnum
     */
    public function object_with_integer_and_convertible_to_string_throws_exception_anyway()
    {
        IntegerEnum::get(new TestWithToString($integer = 12345));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\UnexpectedValueToEnum
     */
    public function object_with_non_numeric_string_cause_exception_even_if_to_string_convertible()
    {
        IntegerEnum::get(new TestWithToString('foo'));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function empty_string_cause_exception()
    {
        IntegerEnum::get('');
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function non_integer_cause_exception()
    {
        IntegerEnum::get(null);
    }
}

/** inner */
class TestWithToString
{

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}
