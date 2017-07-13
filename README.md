# ComposerVersionUpdater

## Installation

You need to clone spryker/composer-version-updater into `vendor/spryker/composer-version-updater/`:

```
git clone git@github.com:spryker/composer-version-updater.git
```

After that you need to install all its dependencies by running:

```
composer install
```

You can test it by running `vendor/bin/codecept run`


## Documentation

Inside this `vendor/spryker/composer-version-updater/` directory:
```
php src/index.php spryker:[command] [args]
```

### Composer Validator
```
php src/index.php spryker:composer-validate -v
```

### PR Module Validator
//TODO read live data
```
php src/index.php spryker:pr-module-validator
```

### PR Constraint Updater
//TODO read live data
```
php src/index.php spryker:constraint-updater
```

## Running phpcs

```
php composer.phar cs-check
php composer.phar cs-fix
```
