<?php
namespace Doctrineum\Integer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
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
