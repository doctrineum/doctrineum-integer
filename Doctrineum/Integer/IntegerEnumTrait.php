<?php
namespace Doctrineum\Integer;

use Granam\Strict\Integer\StrictInteger;

trait IntegerEnumTrait
{

    /**
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
            return (new StrictInteger($valueToConvert, false /* not strict*/))->getValue();
        } catch (\Granam\Strict\Integer\Exceptions\WrongParameterType $exception) {
            // wrapping the exception by local one
            throw new Exceptions\UnexpectedValueToConvert($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

}
