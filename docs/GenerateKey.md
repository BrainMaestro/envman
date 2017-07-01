# Generate Key

This command generates a new random key for encryption and decryption.

## Example
```sh
envman generate:key production-env-key
```

## Usage
```sh
generate:key <key-name>
```

## Arguments
Argument | Description | Default
-------- | ----------- | -------
`key-name` | Name of the key to be generated |

## Notes
- The keys are generated with [php-encryption](https://github.com/defuse/php-encryption).
- **DO NOT** commit any generated key into your repository. That defeats the whole purpose because any one can decrypt the encrypted variables.
