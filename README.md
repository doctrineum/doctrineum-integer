[![Build Status](https://travis-ci.org/jaroslavtyc/doctrineum-integer.svg?branch=master)](https://travis-ci.org/jaroslavtyc/doctrineum-integer)


# About
[Doctrine](http://www.doctrine-project.org/) [enum](http://en.wikipedia.org/wiki/Enumerated_type) allowing integers only.

### Example
```php
$integerEnum = integerEnum::get(12345);
(int)(string)$integerEnum === $integerEnum->getValue() === 12345; // true

// correct, string with integer is allowed
$integerEnum = integerEnum::get('12345');

// correct - white charters are trimmed, the rest is pure integer
$integerEnum = integerEnum::get('  12     ');

// throws an exception - only integer number is allowed
$integerEnum = integerEnum::get(12.3);

// throws an exception - only integer number is allowed
$integerEnum = integerEnum::get('12foo');

// throws an exception - again only integer number is allowed
$integerEnum = integerEnum::get(null)

// throws an exception - again only integer number is allowed
$integerEnum = integerEnum::get('');
```

# Doctrine integration
For details about new Doctrine type registration, see the parent project [Doctrineum](https://github.com/jaroslavtyc/doctrineum).
