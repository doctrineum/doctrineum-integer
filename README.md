[![Build Status](https://travis-ci.org/jaroslavtyc/doctrineum-integer.svg?branch=master)](https://travis-ci.org/jaroslavtyc/doctrineum-integer)
[![Test Coverage](https://codeclimate.com/github/jaroslavtyc/doctrineum-integer/badges/coverage.svg)](https://codeclimate.com/github/jaroslavtyc/doctrineum-integer/coverage)
[![License](https://poser.pugx.org/doctrineum/integer/license)](https://packagist.org/packages/doctrineum/integer)


# About
[Doctrine](http://www.doctrine-project.org/) [enum](http://en.wikipedia.org/wiki/Enumerated_type) allowing integers only.

### Example
```php
$integerEnum = IntegerEnum::getEnum(12345);
(int)(string)$integerEnum === $integerEnum->getValue() === 12345; // true

// correct, string with integer is allowed
$integerEnum = IntegerEnum::getEnum('12345');

// correct - white characters are trimmed, the rest is pure integer
$integerEnum = IntegerEnum::getEnum('  12     ');

// throws an exception - only integer number is allowed
IntegerEnum::getEnum(12.3);

// throws an exception - only integer number is allowed
IntegerEnum::getEnum('12foo');

// throws an exception - again only integer number is allowed
IntegerEnum::getEnum(null)

// throws an exception - again only integer number is allowed
IntegerEnum::getEnum('');
```

# Doctrine integration
For details about new Doctrine type registration, see the parent project [Doctrineum](https://github.com/jaroslavtyc/doctrineum).
