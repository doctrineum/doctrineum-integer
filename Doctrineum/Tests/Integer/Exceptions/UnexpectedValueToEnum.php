<?php
namespace Doctrineum\Tests\Integer\Exceptions;

use Doctrineum\Integer\Exceptions\UnexpectedValueToConvert;

class UnexpectedValueToEnumTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException \Doctrineum\Scalar\Exceptions\UnexpectedValueToEnum
     */
    public function is_doctrineum_similar_exception()
    {
        throw new UnexpectedValueToConvert();
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\Logic
     */
    public function is_local_logic_exception()
    {
        throw new UnexpectedValueToConvert();
    }

}
