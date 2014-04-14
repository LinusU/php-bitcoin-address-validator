# php-bitcoin-address-validator

A simple, easy to use PHP Bitcoin address validator

## Usage

There is only one method exposed from this library, `isValid($addr, $version)`, which returns a boolean.

```php
use \LinusU\Bitcoin\AddressValidator;

// This will return false, indicating invalid address.
AddressValidator::isValid('blah');

// This is a valid address and will thus return true.
AddressValidator::isValid('1AGNa15ZQXAZUgFiqJ2i7Z2DPU2J6hW62i');

// This is a Testnet address, it's valid and the function will return true.
AddressValidator::isValid('mo9ncXisMeAoXwqcV5EWuyncbmCcQN4rVs', AddressValidator::TESTNET);
```
