<?php
namespace Doctrineum\Integer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrineum\Generic\EnumInterface;

/**
 * @method integer convertToDatabaseValue(EnumInterface $enumValue, AbstractPlatform $platform)
 * @see \Doctrineum\Generic\EnumType::convertToDatabaseValue
 */
trait IntegerEnumTypeTrait
{

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'INTEGER';
    }

    /**
     * Just for your information, is not used at code.
     * Maximum length of default SQL integer, @link http://en.wikipedia.org/wiki/Integer_%28computer_science%29
     *
     * @param AbstractPlatform $platform
     * @return int
     */
    public function getDefaultLength(AbstractPlatform $platform)
    {
        return 10;
    }

    /**
     * @see \Doctrineum\Generic\EnumType::convertToPHPValue for usage
     *
     * @param string $enumValue
     * @return IntegerEnum
     */
    protected function convertToEnum($enumValue)
    {
        if (!is_int($enumValue)) {
            throw new Exceptions\UnexpectedValueToEnum(
                'Unexpected value to convert. Expected integer, got ' . gettype($enumValue)
            );
        }

        $enumClass = static::getEnumClass();
        /** @var IntegerEnum $enumClass */
        return $enumClass::getEnum($enumValue);
    }

    /**
     * @return string
     */
    protected static function getEnumClass()
    {
        return IntegerEnum::class;
    }

    /**
     * Gets the strongly recommended name of this type.
     * Its used at @see \Doctrine\DBAL\Platforms\AbstractPlatform::getDoctrineTypeComment
     *
     * @return string
     */
    public static function getTypeName()
    {
        return 'integer-enum';
    }
}
