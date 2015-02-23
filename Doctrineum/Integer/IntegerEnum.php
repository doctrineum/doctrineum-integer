<?php
namespace Doctrineum\Integer;

use Doctrineum\Generic\Enum;

/**
 * Inspired by @link http://github.com/marc-mabe/php-enum
 */
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
}
