<?php
namespace Doctrineum\Integer;

use Doctrineum\Generic\Enum;
use Granam\Strict\String\StrictStringTrait;

/**
 * Inspired by @link http://github.com/marc-mabe/php-enum
 */
class IntegerEnum extends Enum
{
    /** Adopting convertToString method
     * @see StrictStringTrait::convertToString
     */
    use StrictStringTrait;

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
     * @param $value
     * @return int
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
     * @param string $enumValue
     * @param string $namespace
     * @return Enum
     */
    public static function get($enumValue, $namespace = __CLASS__)
    {
        /** @see \Doctrineum\Generic\Enum::get */
        /** @noinspection PhpUndefinedClassInspection */
        return parent::get($enumValue, $namespace);
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
