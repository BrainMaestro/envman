# Build

This command combines the environment variables in all your separate `.env.* (.env.app, .env.auth, .env.db, ...)` files into a single `.env` file.

## Example
```sh
envman build production --key=production-env-key
```

## Usage
```sh
build [options] [--] [<directories>]...
```

## Arguments
Argument | Description | Default
-------- | ----------- | -------
`directories` | Directories of env files | `["."]`

## Options
Option | Description | Default
------ | ----------- | -------
`--key` | Key file for decryption |

## Notes
- Environment variables that are commented out will not be built into the `.env` file.
- If a key is not provided, encrypted variables will be skipped.
