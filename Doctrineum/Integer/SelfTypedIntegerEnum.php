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
     * Using own namespace to avoid conflicts with other enums
     *
     * @param string $enumValue
     * @param string $namespace
     * @return SelfTypedIntegerEnum
     */
    public static function getEnum($enumValue, $namespace = __CLASS__)
    {
        return parent::getEnum($enumValue, $namespace);
    }

    /**
     * Type has private constructor, the only way how to create an Enum, which is also Type, is by Type factory method,
     * @see Type::getType
     *
     * @param mixed $enumValue
     * @return SelfTypedIntegerEnum
     */
    protected static function createByValue($enumValue)
    {
        $selfTypedEnum = static::getType(static::getTypeName());
        $selfTypedEnum->enumValue = $selfTypedEnum->convertToInteger($enumValue);

        return $selfTypedEnum;
    }

    /**
     * Core idea of self-typed enum.
     * As an enum class returns itself.
     *
     * @return string
     */
    protected static function getEnumClass()
    {
        return static::class;
    }

    public static function getTypeName()
    {
        return 'self_typed_integer_enum';
    }
}
