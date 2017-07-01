# Add

This command adds a new enviroment variable to a specified env file.

## Example
```sh
envman add APP_NAME test-app app
```

## Usage
```sh
add [options] [--] <key> <value> [<file>]
```

## Arguments
Argument | Description | Default
-------- | ----------- | -------
`key` | Key of new environment variable |
`value` | Value of new environment variable |
`file` | File to store new environment variable | `example`

## Options
Option | Description | Default
------ | ----------- | -------
`--dir` | Directory of env file | `"."` (current directory)
`--allow-duplicates` | Allow duplication of env variables | `false`

## Notes
- The `key` argument is converted to uppercase before usage (`'app_name' => 'APP_NAME'`).
- The `file` argument is prefixed with `.env`.
- The `--dir` option will be created if it does not exist.
