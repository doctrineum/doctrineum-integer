<?php
namespace Doctrineum\Integer;

use Doctrineum\Scalar\Enum;

/**
 * @method static IntegerEnum getEnum($value)
 */
class IntegerEnum extends Enum implements IntegerEnumInterface
{

    use IntegerEnumTrait;

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->getEnumValue();
    }

}
