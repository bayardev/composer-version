# Composer-version

[![Latest Stable Version](https://poser.pugx.org/bayardev/composer-version/v/stable)](https://packagist.org/packages/bayardev/composer-version)
[![Latest Unstable Version](https://poser.pugx.org/bayardev/composer-version/v/unstable)](https://packagist.org/packages/bayardev/composer-version#dev-master)
[![PHP required version](https://img.shields.io/badge/php-%5E5.5.9%7C%3E=7.0.8-8892BF.svg?style=flat-square)](https://github.com/bayardev/composer-version/blob/master/composer.json)
[![License](https://poser.pugx.org/bayardev/composer-version/license)](https://github.com/bayardev/composer-version/blob/master/LICENCE)
[![Total Downloads](https://poser.pugx.org/bayardev/composer-version/downloads)](https://packagist.org/packages/bayardev/composer-version)

A composer plugin that helps with releasing semantically versioned composer packages or projects,
automatically adding git tags.

Inspired by [npm version](https://docs.npmjs.com/cli/version).

## Install

You can add it as a dependency in your projects or globally on your machine

```sh
composer require bayardev/composer-version
# OR
composer global require bayardev/composer-version
```

## Usage

```
composer version <new-version> | major | minor | patch

  -h, --help
        Show help and exit
  -s, --gpg-sign
        sign tag with gpg key
  -p <prefix>, --prefix=<prefix>
        set tag prefix (default: 'v')
        `-p false` for no prefix
```
