<?php
namespace Doctrineum\Integer;

use Doctrineum\Scalar\EnumType;
use Granam\Scalar\Tools\ValueDescriber;

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
     * @param string $enumValue
     *
     * @return IntegerEnum
     */
    protected function convertToEnum($enumValue)
    {
        if (!is_int($enumValue)) {
            /** @var mixed $enumValue */
            throw new Exceptions\UnexpectedValueToConvert(
                'Unexpected value to convert. Expected integer, got ' . ValueDescriber::describe($enumValue)
            );
        }

        return parent::convertToEnum($enumValue);
    }
}
