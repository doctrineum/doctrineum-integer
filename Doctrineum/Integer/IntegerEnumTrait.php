<?php
namespace Doctrineum\Integer;

use Granam\Integer\Tools\ToInteger;

trait IntegerEnumTrait
{

    /**
     * Overloading parent @see \Doctrineum\Scalar\EnumTrait::convertToEnumFinalValue
     * @param mixed $enumValue
     * @return int
     */
    protected static function convertToEnumFinalValue($enumValue)
    {
        return static::convertToInteger($enumValue);
    }

    /**
     * @param mixed $valueToConvert
     * @return float
     */
    protected static function convertToInteger($valueToConvert)
    {
        try {
            return ToInteger::toInteger($valueToConvert);
        } catch (\Granam\Integer\Tools\Exceptions\WrongParameterType $exception) {
            // wrapping the exception by local one
            throw new Exceptions\UnexpectedValueToConvert($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

}
