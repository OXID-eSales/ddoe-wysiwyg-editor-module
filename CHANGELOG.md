# Change Log for WYSIWYG Editor

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [7.1.0] - unreleased

### Changed
- Update module to work with OXID eShop 7.6
- Updated `squizlabs/php_codesniffer` from `3.*` to `4.*`

## [7.0.1] - 2026-05-13

### Added
- DOMPurify integration for HTML content normalization in the editor
- Twig block `ddoe_wysiwyg_dompurify_config` to allow other modules to customize DOMPurify options
- Changes from v6.0.3

## [7.0.0] - 2026-04-09

### Added
- Twig blocks `ddoe_wysiwyg_plugins` and `ddoe_wysiwyg_summernote_options` to allow other modules to extend Summernote plugins and options
- `ddoewysiwyg:migrate:alt-texts` - migration command to replace empty or missing alt attributes on media images with `oeMediaAlt` calls

### Changed
- Updated to work with OXID eShop 7.5.x
- Minimum PHP version is now 8.3, tested up to PHP 8.5
- Replaced Font Awesome with Bootstrap Icons

## [6.0.3] - 2026-05-13

### Fixed
- Incorrect Bootstrap style imports affecting shop styles

## [6.0.2] - 2026-04-09

### Fixed
- Summernote toolbar dropdowns not opening due to Bootstrap 5 event delegation conflict
- Content deleted when emojis are used in text widgets [#0007619](https://bugs.oxid-esales.com/view.php?id=7619)

## [6.0.1] - 2025-11-10

### Fixed
- Summernote initialization stealing focus from previously selected input fields (regression from v6.0.0) [#0007104](https://bugs.oxid-esales.com/view.php?id=7104)

## [6.0.0] - 2025-10-15

### Added
- `ddoewysiwyg:migrate:urls-to-ids` - migration command to exchange hardcoded media paths to new `oeMediaUrl` calls
- Add acceptance test to verify image alt attribute is transformed to a Twig placeholder

### Changed
- Instead of inserting paths to the media items, `oeMediaUrl` function with the media id is inserted
- Update to Bootstrap 5
- Switch from LESS to SASS

### Fixed
- Summernote initialization stealing focus from previously selected input fields [#0007104](https://bugs.oxid-esales.com/view.php?id=7104)
- Use `ViewConfig::formJsFileUrl` for adding the timestamps to JS files, this prevents caching issues after module update

## [5.0.1] - 2025-10-15

### Changed
- Updated the PHPStan to 2.1

### Fixed
- Development recipe to not require the media library module twice
- Run migrations on CI before module activation
- Add missing `getMediaUrl` replacement for Media library images

## [5.0.0] - 2025-04-10

### Added
- Support of PHP 8.4

### Changed
- Migrate from Grunt to Vite for assets generating
- Rewrite JS scripts to ES6
- jQuery updated to v3.7.1 version

### Removed
- External libraries from vendor directory. They are installed using Node.js now
- Completely removed Grunt and its dependencies from the project
- Possibility to 'Use default protocol' in Link Dialog

## [4.2.0] - 2024-10-14

### Changed
- Upgrade phpunit to 11.x

### Removed
- Support of PHP 8.1

## [4.1.0] - 2024-10-10

### Fixed
- Pre-filter CMS content before it is passed to Summernote editor

### Added
- `HtmlFilter` and `HtmlTagRemover` class

## [4.0.0] - 2024-03-12

### Changed
- Media library extracted to [separate module](https://github.com/OXID-eSales/media-library-module) and can be used separately
- Media library module added as dependency
- New module logo
- Updated the structure to Codeception 5
- Modify github workflows to use new universal workflow

### Removed
- Legacy Smarty engine variant is not supported anymore, and will not work with this release.
- PHP 8.0 support

### Fixed
- Handling of url and CMS-Ident improved in the Link add/edit plugin.

## [3.0.2] - 2023-11-22

### Fixed
- License title and paddings updated
- Compatibility matrix information updated in readme

## [3.0.1] - 2023-05-11

### Fixed
- Use Symfony filesystem instead of deprecated Webmozart
- Fix phpstan running composer alias
- Respect 'THEME_ID' environment variable for codeception test run
- Coverage report preparation step in the workflow

## [3.0.0] - 2023-05-09

### Added
- Simple codeception test to check module is functioning
- Code quality tools - phpcs

### Changed
- Migrations are used during module activation to install and update module related database parts
- Php code moved to 'src' folder
- Template access keys changed in metadata.php; New keys used in controllers and template includes
- TemplateRenderer used to load templates
- License updated - OXID Module and Component License instead of GPL

### Fixed
- Coding style issues
- Media library limitations for multishop support

### Removed
- Unnecessary parameters and its usage removed:
  - `blModuleWasEnabled`
  - `iInstallledVersion`
  - `blMediaLibraryMultiShopCapability`
- PHP 7.3 and 7.4 support

## [2.4.3] - unreleased

### Changed
- License updated - now using OXID Module and Component License

## [2.4.2] - 2023-02-03

### Fixed
- Update code edit textarea font color [PR-21](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/pull/21)
- Incorrect variables for editor height used in template [PR-20](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/pull/20)

## [2.4.1] - 2021-11-26

### Fixed
- Fix broken editor view in some html cases [PR-18](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/pull/18)

## [2.4.0] - 2020-10-27

### Added
- Enable superscript button in editor [PR-15](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/pull/15)

## [2.3.0] - 2020-07-13
- File upload improvements
- Update Summernote to version 0.8.18

### Fixed
- Replace incorrectly encoded html lace bracket in smarty tags [PR-13](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/pull/13) [#0007045](https://bugs.oxid-esales.com/view.php?id=7045) [#0006779](https://bugs.oxid-esales.com/view.php?id=6779)

## [2.2.0] - 2019-01-21

### Added
- Possibility to have more than one textarea on a admin-page [PR-10](https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/pull/10). [Bug #6884](https://bugs.oxid-esales.com/view.php?id=6884).

## [2.1.1] - 2018-03-26

### Fixed
- Fix html entities in smarty tags. [Bug #6514](https://bugs.oxid-esales.com/view.php?id=6514)

## [2.1.0] - 2018-01-17

### Added
- Option to disable the wysiyg editor and make the content display readonly

## [2.0.0] - 2017-11-14

### Added
- Introduced namespaces

### Changed
- Usage of metadata 2.0

### Fixed
- Smarty tags are parsed correct now
- Correct protocol usage for image urls

[7.0.1]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v7.0.0...v7.0.1
[7.0.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v6.0.3...v7.0.0
[6.0.3]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v6.0.2...v6.0.3
[6.0.2]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v6.0.1...v6.0.2
[6.0.1]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v6.0.0...v6.0.1
[6.0.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v5.0.0...v6.0.0
[5.0.1]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v5.0.0...v5.0.1
[5.0.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v4.2.0...v5.0.0
[4.2.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v4.1.0...v4.2.0
[4.1.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v4.0.0...v4.1.0
[4.0.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v3.0.2...v4.0.0
[3.0.2]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.4.2...v3.0.0
[2.4.3]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.4.2...b-2.x
[2.4.2]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.4.1...v2.4.2
[2.4.1]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.4.0...v2.4.1
[2.4.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.3.0...v2.4.0
[2.3.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.1.1...v2.2.0
[2.1.1]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/tree/v2.0.0
