# Summernote WYSIWYG Editor for OXID eShop

# Compatibility

* `b-7.1.x` - compatible with OXID eShop b-7.1.x branch, works with **Twig engine** only
* `b-7.0.x` - compatible with OXID eShop b-7.0.x branch and supports **Legacy Smarty engine**
* `b-2.x` branch is compatible with OXID eShop compilations: 6.2.x, 6.3.x, 6.4.x and 6.5.x
* tag `2.0.0` - compatible with OXID eShop compilation 6.0

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
