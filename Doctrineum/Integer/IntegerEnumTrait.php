<?php
namespace Doctrineum\Integer;

trait IntegerEnumTrait
{

    /**
     * @param mixed $value
     * @return int
     * @throws Exceptions\UnexpectedValueToEnum
     */
    protected function convertToInteger($value)
    {
        if (is_int($value)) {
            return $value;
        }

        $stringValue = trim($this->convertToString($value));
        $integerValue = intval($stringValue);
        if ((string)$integerValue === $stringValue) { // the cast has been lossless
            return $integerValue;
        }

        throw new Exceptions\UnexpectedValueToEnum('Expecting integer value only, got ' . var_export($value, true));
    }

    /**
     * @param mixed $value
     * @throws Exceptions\UnexpectedValueToEnum
     * @return string
     */
    protected function convertToString($value)
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value) || is_null($value) || (is_object($value) && method_exists($value, '__toString'))) {
            return (string)$value;
        }

        throw new Exceptions\UnexpectedValueToEnum('Expected scalar or to string convertible object, got ' . gettype($value));
    }

}
