<?php
namespace Doctrineum\Integer;

use Granam\Strict\String\StrictStringTrait;

trait IntegerEnumTrait
{
    /** Adopting convertToString method
     * @see StrictStringTrait::convertToString
     */
    use StrictStringTrait;

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

        $stringValue = trim($this->convertToString($value, false /* not strict */));
        $integerValue = intval($stringValue);
        if ((string)$integerValue === $stringValue) { // the cast has been lossless
            return $integerValue;
        }

        throw new Exceptions\UnexpectedValueToEnum('Expecting integer value only, got ' . var_export($value, true));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        /** @var \Doctrineum\Generic\Enum $this */
        return (string)$this->enumValue;
    }

}
