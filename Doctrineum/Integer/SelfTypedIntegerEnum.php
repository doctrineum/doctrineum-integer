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
     * Its not directly used this library - the exactly same value is generated and used by
     * @see \Doctrineum\Scalar\SelfTypedEnum::getTypeName
     *
     * This constant exists to follow Doctrine type conventions.
     */
    const SELF_TYPED_INTEGER_ENUM = 'self_typed_integer_enum';

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

    /**
     * Core idea of self-typed enum.
     * As an enum class returns itself.
     *
     * This overloads unwanted method from trait
     * @see \Doctrineum\Integer\IntegerEnumTypeTrait::getEnumClass
     * back to original
     * @see \Doctrineum\Scalar\SelfTypedEnum::getEnumClass
     *
     * @return string
     */
    protected static function getEnumClass()
    {
        /**
         * Skipping unwanted method from trait
         */
        return parent::getEnumClass();
    }
}
