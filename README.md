# php-bitcoin-address-validator

A simple, easy to use PHP Bitcoin address validator

## Usage

Quick start:

```php
use \LinusU\Bitcoin\AddressValidator;

// This will return false, indicating invalid address.
AddressValidator::isValid('blah');

// This is a valid address and will thus return true.
AddressValidator::isValid('1AGNa15ZQXAZUgFiqJ2i7Z2DPU2J6hW62i');

// This is a Testnet address, it's valid and the function will return true.
AddressValidator::isValid('mo9ncXisMeAoXwqcV5EWuyncbmCcQN4rVs', AddressValidator::TESTNET);
```

## API

### `isValid($addr, $version)`

- `$addr`: A bitcoin address
- `$version`: The version to test against, defaults to `MAINNET`

Returns a boolean indicating if the address is valid or not.

### `typeOf($addr)`

- `$addr`: A bitcoin address

Returns the type of the address.

## Constants

The library exposes the following constants.

- `MAINNET`: Indicates any mainnet address type
- `TESTNET`: Indicates any testnet address type
- `MAINNET_PUBKEY`: Indicates a mainnet pay to pubkey hash address
- `MAINNET_SCRIPT`: Indicates a mainnet pay to script hash address
- `TESTNET_PUBKEY`: Indicates a testnet pay to pubkey hash address
- `TESTNET_SCRIPT`: Indicates a testnet pay to script hash address
