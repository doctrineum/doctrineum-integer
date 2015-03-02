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

    /**
     * Using own namespace to avoid conflicts with other enums
     *
     * @param string $enumValue
     * @param string $namespace
     * @return IntegerEnum
     */
    public static function getEnum($enumValue, $namespace = __CLASS__)
    {
        return parent::getEnum($enumValue, $namespace);
    }

}
