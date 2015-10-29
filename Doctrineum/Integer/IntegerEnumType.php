<?php
namespace Doctrineum\Integer;

use Doctrineum\Scalar\EnumType;
use Granam\Integer\Tools\ToInteger;

/**
 * Class EnumType
 * @package Doctrineum
 *
 * @method static IntegerEnumType getType($name),
 * @see Type::getType
 */
class IntegerEnumType extends EnumType
{
    use IntegerEnumTypeTrait;

    const INTEGER_ENUM = 'integer_enum';

    /**
     * @see \Doctrineum\Scalar\EnumType::convertToPHPValue for usage
     *
     * @param mixed $enumValue
     *
     * @return IntegerEnum
     */
    protected function convertToEnum($enumValue)
    {
        $this->checkValueToConvert($enumValue);

        return parent::convertToEnum($enumValue);
    }

    protected function checkValueToConvert($value)
    {
        try {
            // Uses side effect of the conversion - the checks
            ToInteger::toInteger($value);
        } catch (\Granam\Integer\Tools\Exceptions\WrongParameterType $exception) {
            // wrapping exception by a local one
            throw new Exceptions\UnexpectedValueToConvert($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
