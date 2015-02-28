<?php
namespace Doctrineum\Integer;

use Doctrineum\Generic\EnumType;

/**
 * Class EnumType
 * @package Doctrineum
 * @method static IntegerEnumType getType($name),
 * @see Type::getType
 */
class IntegerEnumType extends EnumType
{
    const INTEGER_ENUM = 'integer_enum';

    use IntegerEnumTypeTrait;
}
