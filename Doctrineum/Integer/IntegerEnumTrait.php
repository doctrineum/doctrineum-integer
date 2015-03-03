<?php
namespace Doctrineum\Integer;

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
     * @param mixed $enumValue
     * @return int
     */
    protected static function convertToInteger($enumValue)
    {
        if (is_int($enumValue)) {
            return $enumValue;
        }

        $stringValue = trim(static::convertToString($enumValue));
        $integerValue = intval($stringValue);
        if ((string)$integerValue === $stringValue) { // the cast has been lossless
            return $integerValue;
        }

        throw new Exceptions\UnexpectedValueToEnum('Expecting integer value only, got ' . var_export($enumValue, true));
    }

    /**
     * @param mixed $enumValue
     * @return string
     */
    protected static function convertToString($enumValue)
    {
        if (is_string($enumValue)) {
            return $enumValue;
        }

        if (is_scalar($enumValue) || is_null($enumValue) || (is_object($enumValue) && method_exists($enumValue, '__toString'))) {
            return (string)$enumValue;
        }

        throw new Exceptions\UnexpectedValueToEnum('Expected scalar or to string convertible object, got ' . gettype($enumValue));
    }

}
