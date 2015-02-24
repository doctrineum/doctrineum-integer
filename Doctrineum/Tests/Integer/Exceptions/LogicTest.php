<?php
namespace Doctrineum\Integer\Exceptions;

class LogicTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function is_interface()
    {
        $this->assertTrue(interface_exists(Logic::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\Logic
     */
    public function extends_doctrineum_logic_interface()
    {
        throw new TestLogicInterface();
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\Exception
     */
    public function extends_local_mark_interface()
    {
        throw new TestLogicInterface();
    }
}

/** inner */
class TestLogicInterface extends \Exception implements Logic
{

}
