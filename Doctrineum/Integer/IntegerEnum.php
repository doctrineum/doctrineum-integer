<?php
namespace Doctrineum\Integer;

use Doctrineum\Generic\Enum;

class IntegerEnum extends Enum
{
    use IntegerEnumTrait;

    /**
     * @param int $enumValue
     */
    public function __construct($enumValue)
    {
        try {
            parent::__construct($this->convertToInteger($enumValue));
        } catch (\Granam\Strict\String\Exceptions\Exception $exception) {
            throw new Exceptions\UnexpectedValueToEnum(
                'Expecting integer value only, got ' . gettype($enumValue), $exception->getCode(), $exception
            );
        }
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
