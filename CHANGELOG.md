# Change Log for WYSIWYG Editor + Mediathek module

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

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

[3.0.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.4.2...v3.0.0
[2.4.2]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.4.1...v2.4.2
[2.4.1]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.4.0...v2.4.1
[2.4.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.3.0...v2.4.0
[2.3.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.1.1...v2.2.0
[2.1.1]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/OXID-eSales/ddoe-wysiwyg-editor-module/tree/v2.0.0
