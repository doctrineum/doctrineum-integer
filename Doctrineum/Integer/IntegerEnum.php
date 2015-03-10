<?php
namespace Doctrineum\Integer;

use Doctrineum\Scalar\Enum;

class IntegerEnum extends Enum
{
    use IntegerEnumTrait;

    /**
     * @param int $enumValue
     */
    public function __construct($enumValue)
    {
        parent::__construct($this->convertToInteger($enumValue));
    }

}
