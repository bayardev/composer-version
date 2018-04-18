# Composer-version

**WORK IN PROGRESS : DON'T USE IT NOW !**

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
