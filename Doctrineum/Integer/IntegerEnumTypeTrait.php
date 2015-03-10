<?php
namespace Doctrineum\Integer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrineum\Scalar\EnumInterface;

/**
 * @method integer convertToDatabaseValue(EnumInterface $enumValue, AbstractPlatform $platform)
 * @see \Doctrineum\Scalar\EnumType::convertToDatabaseValue
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
    public function getSQLDeclaration(
        /** @noinspection PhpUnusedParameterInspection */
        array $fieldDeclaration,
        AbstractPlatform $platform
    )
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
    public function getDefaultLength(
        /** @noinspection PhpUnusedParameterInspection */
        AbstractPlatform $platform
    )
    {
        return 10;
    }
}
