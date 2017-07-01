# Encrypt

This command encrypts every environment variable in specified env files or directories.

## Example
```sh
envman encrypt .env.db production --key=production-env-key
```

## Usage
```sh
encrypt [options] [--] [<targets>]...
```

## Arguments
Argument | Description | Default
-------- | ----------- | -------
`targets` | Files or directories with .env.* files to be encrypted |

## Options
Option | Description | Default
------ | ----------- | -------
`--key` | Key file for encryption |

## Notes
- Environment variables that are commented out will not be encrypted.
