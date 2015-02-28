<?php
namespace Doctrineum\Integer;

use Doctrineum\Generic\SelfTypedEnum;

/**
 * @method static SelfTypedIntegerEnum getType($name),
 * @see SelfTypedEnum::getType or the origin
 * @see Type::getType
 */
class SelfTypedIntegerEnum extends SelfTypedEnum
{
    use IntegerEnumTrait;
    use IntegerEnumTypeTrait;

    /**
     * Type has private constructor, the only way how to create an Enum, which is also Type, is by Type factory method,
     * @see Type::getType
     *
     * @param mixed $enumValue
     * @return SelfTypedIntegerEnum
     */
    protected static function createByValue($enumValue)
    {
        // is casted first to find out wrong format before instance creation and REGISTRATION
        $integerEnumValue = static::convertToInteger($enumValue);

        $selfTypedEnum = parent::createByValue($enumValue);
        $selfTypedEnum->enumValue = $integerEnumValue;

        return $selfTypedEnum;
    }
}
