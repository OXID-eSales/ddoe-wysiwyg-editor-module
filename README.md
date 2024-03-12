# Summernote WYSIWYG Editor for OXID eShop

[![Development](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/actions/workflows/push_module_71x.yml/badge.svg?branch=b-7.1.x)](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/actions/workflows/push_module_71x.yml)
[![Latest Version](https://img.shields.io/packagist/v/ddoe/wysiwyg-editor-module?logo=composer&label=latest&include_prereleases&color=orange)](https://packagist.org/packages/ddoe/wysiwyg-editor-module)
[![PHP Version](https://img.shields.io/packagist/php-v/ddoe/wysiwyg-editor-module)](https://github.com/ddoe/wysiwyg-editor-module)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=OXID-eSales_ddoe-wysiwyg-editor-module&metric=alert_status)](https://sonarcloud.io/dashboard?id=OXID-eSales_ddoe-wysiwyg-editor-module)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=OXID-eSales_ddoe-wysiwyg-editor-module&metric=coverage)](https://sonarcloud.io/dashboard?id=OXID-eSales_ddoe-wysiwyg-editor-module)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=OXID-eSales_ddoe-wysiwyg-editor-module&metric=sqale_index)](https://sonarcloud.io/dashboard?id=OXID-eSales_ddoe-wysiwyg-editor-module)

# Compatibility

### Branches
* `b-7.1.x` is compatible with OXID eShop b-7.1.x branch, works with **Twig engine** only
* `b-7.0.x` is compatible with OXID eShop b-7.0.x branch and supports **Legacy Smarty engine**
* `b-2.x` is compatible with OXID eShop compilations: 6.2.x - 6.5.x

### Versions
* versions `4.x` - compatible with OXID eShop compilation 7.1.x
* versions `3.x` - compatible with OXID eShop compilation 7.0.x
* versions `2.x` - compatible with OXID eShop compilation 6.0.x - 6.5.x

### Module installation via composer

In order to install the module via composer run one of the following commands in commandline in your shop base directory 
(where the shop's composer.json file resides).
* `composer require ddoe/wysiwyg-editor-module:^3.0.0`
  to install the latest released version compatible with OXID eShop v6.0
* `composer require ddoe/wysiwyg-editor-module:dev-b-7.1.x`  
  to install the specific unreleased branch

### Module activation in OXID eShop Admin 
After installation, please, activate the module in OXID eShop Admin  
`EXTENSIONS -> Modules -> "Summernote WYSIWYG Editor for OXID eShop" -> Activate`

# Development installation

The installation paths in commands below are fitting current [SDK](https://github.com/OXID-eSales/docker-eshop-sdk).
In case of different environment usage, please adjust by your own needs.

```shell
# Clone the repository
cd <shopRootPath>
git clone https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module.git --branch=b-7.1.x source/dev-packages/wysiwyg

docker compose exec -T \
  php composer config repositories.ddoe/wysiwyg-editor-module \
  --json '{"type":"path", "url":"./dev-packages/wysiwyg", "options": {"symlink": true}}'
docker compose exec -T php composer require ddoe/wysiwyg-editor-module:* --no-update

# Activate modules
bin/oe-console oe:module:activate ddoemedialibrary
bin/oe-console oe:module:activate ddoewysiwyg
```

## Bugs and Issues

If you experience any bugs or issues, please report them in the section **WYSIWYG Editor + Media Gallery** of https://bugs.oxid-esales.com.
