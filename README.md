# Summernote WYSIWYG Editor for OXID eShop

[![Development](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/actions/workflows/trigger.yaml/badge.svg?branch=b-7.6.x)](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/actions/workflows/trigger.yaml)
[![Latest Version](https://img.shields.io/packagist/v/ddoe/wysiwyg-editor-module?logo=composer&label=latest&include_prereleases&color=orange)](https://packagist.org/packages/ddoe/wysiwyg-editor-module)
[![PHP Version](https://img.shields.io/packagist/php-v/ddoe/wysiwyg-editor-module)](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=OXID-eSales_ddoe-wysiwyg-editor-module&metric=alert_status)](https://sonarcloud.io/dashboard?id=OXID-eSales_ddoe-wysiwyg-editor-module)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=OXID-eSales_ddoe-wysiwyg-editor-module&metric=coverage)](https://sonarcloud.io/dashboard?id=OXID-eSales_ddoe-wysiwyg-editor-module)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=OXID-eSales_ddoe-wysiwyg-editor-module&metric=sqale_index)](https://sonarcloud.io/dashboard?id=OXID-eSales_ddoe-wysiwyg-editor-module)

# Compatibility

### Versions
* versions `7.0.x` - compatible with OXID eShop compilation 7.5.x and higher
* versions `6.0.x` - compatible with OXID eShop compilation 7.4.x and higher
* versions `5.0.x` - compatible with OXID eShop compilation 7.3.x and higher
* versions `4.2.x` - compatible with OXID eShop compilation 7.2.x and higher
* versions `4.0.x - 4.1.x` - compatible with OXID eShop compilation 7.1.x
* versions `3.x` - compatible with OXID eShop compilation 7.0.x
* versions `2.x` - compatible with OXID eShop compilation 6.0.x - 6.5.x

### Branches
* `b-7.6.x` is compatible with OXID eShop b-7.6.x branch
* `b-7.5.x` is compatible with OXID eShop b-7.5.x branch
* `b-7.4.x` is compatible with OXID eShop b-7.4.x branch
* `b-7.3.x` is compatible with OXID eShop b-7.3.x branch
* `b-7.2.x` is compatible with OXID eShop b-7.2.x branch
* `b-7.1.x` is compatible with OXID eShop b-7.1.x branch, works with **Twig engine** only from here on
* `b-7.0.x` is compatible with OXID eShop b-7.0.x branch and supports **Legacy Smarty engine**
* `b-2.x` is compatible with OXID eShop compilations: 6.2.x - 6.5.x

### Module installation via composer

In order to install the module via composer run one of the following commands in commandline in your shop base directory
(where the shop's composer.json file resides).
* `composer require ddoe/wysiwyg-editor-module:^7.0.0`
  to install the released version compatible with OXID eShop v7.6.x
* `composer require ddoe/wysiwyg-editor-module:dev-b-7.6.x`
  to install the specific unreleased branch

### Module activation in OXID eShop Admin
After installation, please, activate the module in OXID eShop Admin
`EXTENSIONS -> Modules -> "Summernote WYSIWYG Editor for OXID eShop" -> Activate`

# Development installation
The installation instructions below are shown for the current [SDK](https://github.com/OXID-eSales/docker-eshop-sdk)
for shop 7.6. Make sure your system meets the requirements of the SDK.

0. Ensure all docker containers are down to avoid port conflicts

1. Clone the SDK for the new project
```shell
echo MyProject && git clone https://github.com/OXID-eSales/docker-eshop-sdk.git $_ && cd $_
```

2. Clone the repository to the source directory
```shell
git clone --recurse-submodules https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module.git --branch=b-7.6.x ./source
```

3. Run the recipe to setup the development environment
```shell
./source/recipes/setup-development.sh
```

You should be able to access the shop via
- Frontend http://localhost.local
- Admin Panel: http://localhost.local/admin
  - (credentials: noreply@oxid-esales.com / admin)

### Running the tests and quality tools

Check the "scripts" section in the `composer.json` file for the available commands. Those commands can be executed
by connecting to the php container and running the command from there, example:

```shell
make php
composer tests-coverage
```

Commands can be also triggered directly on the container with docker compose, example:

```shell
docker compose exec -T php composer tests-coverage
```

## Rebuilding the assets
To rebuild the assets, latest node docker container can be used. The one is pulled automatically if you are using the
installation method from the previous section. What is left - connect to the container, install the npm dependencies
and run the assets building process

```shell
make node
```

Navigate to the module directory and run:

```shell
npm install
npm run build
```
Alternatively, if you're actively developing and want changes to be applied automatically, you can enable watch mode:

```shell
npm run watch
```

## Migration

Besides running the usual migrations process, there are some addition actions that may differ by project.

### Media paths to IDs

The command `ddoewysiwyg:migrate:urls-to-ids tableName fieldName tableIdKey` migrates hardcoded media 
paths inserted by earlier version of MediaLibrary to Media object ID's. It takes the `tableName`, `fieldName`,
and the `tableIdKey` field as params.   

Example use:
```
vendor/bin/oe-console ddoewysiwyg:migrate:urls-to-ids oxartextends OXLONGDESC
vendor/bin/oe-console ddoewysiwyg:migrate:urls-to-ids oxartextends OXLONGDESC_1
vendor/bin/oe-console ddoewysiwyg:migrate:urls-to-ids oxcategories OXDESC
vendor/bin/oe-console ddoewysiwyg:migrate:urls-to-ids oxcategories OXDESC_1
vendor/bin/oe-console ddoewysiwyg:migrate:urls-to-ids oxcontents OXCONTENT
vendor/bin/oe-console ddoewysiwyg:migrate:urls-to-ids oxcontents OXCONTENT_1
```

Ensure all fields for which the WYSIWYG editor is used are migrated.

Media references in `img src` and `a href` attributes are converted. 
The command reports how many media references it found and converted, and lists the ones it could not resolve:

```
oxcontents::OXCONTENT (key OXID)
Media references found: 40
Converted:              39
Failed:                 1

  [OXID=mediaid] src="/out/pictures/ddmedia/medianame.jpg": no matching entry in the media library
```

References that cannot be resolved are listed and left unchanged - either the media is not registered in the media
library and has to be added there, or the path in the content is wrong. Nothing is imported into the media library
by the migration.

Only the `src` and `href` attributes themselves are converted. Media paths in other attributes, e.g. the `data-src`
of a lazy loading image or a `srcset`, are left untouched.

Pass `--report-file` to write the report as CSV instead of printing it. Relative paths are written to the shop log
directory, absolute paths are used as they are:

```
vendor/bin/oe-console ddoewysiwyg:migrate:urls-to-ids oxcontents OXCONTENT --report-file=media-migration.csv
```

The file holds one row per media reference with the columns `table`, `field`, `key`, `attribute`, `path`,
`outcome`, `media_id` and `detail`.

### Media alt texts

The command `ddoewysiwyg:migrate:alt-texts tableName fieldName tableIdKey` adds `oeMediaAlt` placeholders to
media image `alt` attributes. This enables media alt texts managed in the Media Library to be rendered automatically
in the frontend. It should be run after the `ddoewysiwyg:migrate:urls-to-ids` migration, as it relies on the
`data-id` attribute being present on media image tags.

The command handles the following cases:
- Empty or missing `alt` attribute: replaced with the `oeMediaAlt` placeholder
- Already migrated (`oeMediaAlt` placeholder present): skipped
- Custom alt text written manually: **not modified**, but listed in the command output for manual review

Example use:
```
vendor/bin/oe-console ddoewysiwyg:migrate:alt-texts oxartextends OXLONGDESC
vendor/bin/oe-console ddoewysiwyg:migrate:alt-texts oxartextends OXLONGDESC_1
vendor/bin/oe-console ddoewysiwyg:migrate:alt-texts oxcontents OXCONTENT
vendor/bin/oe-console ddoewysiwyg:migrate:alt-texts oxcontents OXCONTENT_1
```

If any media images have custom alt text, the command will output them:
```
Warning: The following media images have custom alt text that was NOT modified:

  [OXID=abc123] media-id="68cab835a253c" alt="My product photo"

Please review these entries and update alt texts manually if needed.
```

## Bugs and Issues

If you experience any bugs or issues, please report them in the section **WYSIWYG Editor + Media Gallery** of https://bugs.oxid-esales.com.
