# Envman
[![Travis](https://img.shields.io/travis/BrainMaestro/envman.svg?style=flat-square)](https://travis-ci.org/BrainMaestro/envman)
[![Packagist](https://img.shields.io/packagist/v/brainmaestro/envman.svg?style=flat-square)](https://packagist.org/packages/brainmaestro/envman)
> Manage your `.env` configuration easily.

This package makes it easy to keep your `.env` in sync with the rest of your team without too much hassle. It allows you to break up your huge monolithic `.env.example` file into smaller files that have related variables. It also offers an easy and painless way of protecting sensitive configurations with encryption from [php-encryption](https://github.com/defuse/php-encryption).

This works in conjunction with any tool you use to actually load the `.env` values into your code like [phpdotenv](https://github.com/vlucas/phpdotenv)

## Install
```sh
composer require --dev brainmaestro/envman
```

## Getting Started
Refer to the documentation for the functionality of the commands

- [Add](docs/Add.md)
- [Build](docs/Build.md)
- [Encrypt](docs/Encrypt.md)
- [Generate Key](docs/GenerateKey.md)
- [Show](docs/Show.md)

## License
MIT © Ezinwa Okpoechi
