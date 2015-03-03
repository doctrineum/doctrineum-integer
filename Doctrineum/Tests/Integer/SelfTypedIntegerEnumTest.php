<?php
namespace Doctrineum\Integer;

use Doctrineum\Tests\Integer\IntegerEnumTestTrait;
use Doctrineum\Tests\Integer\IntegerEnumTypeTestTrait;

class SelfTypedIntegerEnumTest extends \PHPUnit_Framework_TestCase
{

    use IntegerEnumTestTrait;
    use IntegerEnumTypeTestTrait;

    /** @test */
    public function type_name_is_as_expected()
    {
        /** @var \PHPUnit_Framework_TestCase|SelfTypedIntegerEnumTest $this */
        $this->assertSame('self_typed_integer_enum', SelfTypedIntegerEnum::getTypeName());
        $this->assertSame('self_typed_integer_enum', SelfTypedIntegerEnum::SELF_TYPED_INTEGER_ENUM);
        $selfTypedIntegerEnum = SelfTypedIntegerEnum::getType(SelfTypedIntegerEnum::getTypeName());
        $this->assertSame($selfTypedIntegerEnum::getTypeName(), SelfTypedIntegerEnum::getTypeName());
    }

    /** @test */
    public function any_enum_namespace_is_accepted()
    {
        $this->markTestSkipped('Self-typed integer enum does not support enum namespaces yet.');
    }

    /**
     * @test
     * @expectedException \Doctrineum\Scalar\Exceptions\SelfTypedEnumConstantNamespaceChanged
     */
    public function changing_enum_namespace_cause_exception()
    {
        SelfTypedIntegerEnum::getEnum('foo', 'non-default-namespace');
    }
}
