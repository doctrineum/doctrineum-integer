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
     * @return bool If enum has not been registered before and was registered now
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function registerSelf()
    {
        if (static::hasType(static::getTypeName())) {
            static::checkRegisteredType();

            return false;
        }

        static::addType(
            static::getTypeName() /** @see \Doctrineum\Integer\IntegerEnumType::INTEGER_ENUM */,
            get_called_class()
        );

        return true;
    }

    protected static function checkRegisteredType()
    {
        $alreadyRegisteredType = static::getType(static::getTypeName());
        if (get_class($alreadyRegisteredType) !== get_called_class()) {
            throw new Exceptions\TypeNameOccupied(
                'Under type of name ' . var_export(static::getTypeName(), true) .
                ' is already registered different class ' . get_class($alreadyRegisteredType)
            );
        }
    }

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
