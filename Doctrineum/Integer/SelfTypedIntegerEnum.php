<?php
namespace Doctrineum\Integer;

use Doctrineum\Scalar\SelfTypedEnum;

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
     * @see SelfTypedEnum::createByValue and its usage of
     * @see Type::getType
     *
     * @param mixed $enumValue
     * @return SelfTypedIntegerEnum
     */
    protected static function createByValue($enumValue)
    {
        $selfTypedEnum = parent::createByValue(static::convertToInteger($enumValue));

        return $selfTypedEnum;
    }
}
