# Changelog

All notable changes to `pdf-to-image` will be documented in this file

## 3.0.0 - 2024-06-10

This release updates the package to a new major version, `v3.0.0`.

### Major changes:

- Dropped support for PHP versions < 8.2.
- Many breaking API changes (see below).
- Added support for saving multiple pages to images using `selectPages()`.
- Native Backed Enum implementations to enforce allowed values in several places.
- Dropped support for reading remote PDF files for security reasons.
- Added property, argument and return types.
- Removed Imagick instance creation in constructor, replaced calls to `readImage()` with `pingImage()` *(test suite runs ~33% faster)*.

### Breaking API Changes

There are breaking API changes from v2 to v3. These changes streamline the developer experience, enhance understanding of the functionality of the methods, and reduce cognitive overload by simplifying method names.  See the updated `README.md` for a full list of the new API methods and method names.

### Additional Changes

- `php-cs-fixer` configuration and workflow was dropped in favor of Laravel Pint.
- Readme updated to include all available methods, fix a few grammatical errors, etc.
- Fixed several minor, previously unknown bugs.
- Test coverage increased from ~65% to ~93%.
- Pest upgraded to v2, migrated PHPUnit configuration to v10.
- Unit tests were reorganized/restructured to be more manageable, and Pest configuration files were added.

### What's Changed

* Bump dependabot/fetch-metadata from 1.6.0 to 2.1.0 by @dependabot in https://github.com/spatie/pdf-to-image/pull/228
* Major version: v3 by @patinthehat in https://github.com/spatie/pdf-to-image/pull/230

**Full Changelog**: https://github.com/spatie/pdf-to-image/compare/2.3.0...3.0.0

## 2.3.0 - 2024-03-07

### What's Changed

* Silently ignore pingImage failure and load number of pages lazily by @bobvandevijver in https://github.com/spatie/pdf-to-image/pull/187
* Add Dependabot Automation by @patinthehat in https://github.com/spatie/pdf-to-image/pull/203
* Fix failing tests by @patinthehat in https://github.com/spatie/pdf-to-image/pull/205
* Add PHP 8.2 Support by @patinthehat in https://github.com/spatie/pdf-to-image/pull/202
* Fix php-cs-fixer workflow by @patinthehat in https://github.com/spatie/pdf-to-image/pull/206
* Minor dependabot auto-merge workflow improvements by @patinthehat in https://github.com/spatie/pdf-to-image/pull/220
* Adding webp to the validOutputFormats by @ntaylor-86 in https://github.com/spatie/pdf-to-image/pull/221
* Add PHP 8.3 to the test run workflow by @patinthehat in https://github.com/spatie/pdf-to-image/pull/223

### New Contributors

* @bobvandevijver made their first contribution in https://github.com/spatie/pdf-to-image/pull/187
* @patinthehat made their first contribution in https://github.com/spatie/pdf-to-image/pull/203
* @dependabot made their first contribution in https://github.com/spatie/pdf-to-image/pull/204

**Full Changelog**: https://github.com/spatie/pdf-to-image/compare/2.2.0...2.3.0

## 2.2.0 - 2022-03-08

## What's Changed

- Converting PHPUnit to Pest tests by @ntaylor-86 in https://github.com/spatie/pdf-to-image/pull/189
- Adding a thumbnail method by @ntaylor-86 in https://github.com/spatie/pdf-to-image/pull/188

## New Contributors

- @ntaylor-86 made their first contribution in https://github.com/spatie/pdf-to-image/pull/189

**Full Changelog**: https://github.com/spatie/pdf-to-image/compare/2.1.0...2.2.0

## 2.1.0 - 2020-11-12

- add support for PHP 8

## 2.0.1 - 2020-04-29

- add usage of Imagick `pingImage` to speedup the page count

## 2.0.0 - 2020-01-08

- added typehints
- removal of ability of loading pdfs via URLs

## 1.8.2 - 2019-07-31

- add exception message to `PdfDoesNotExist`

## 1.8.1 - 2018-06-02

- throw exception when trying to fetch a negative page number

## 1.8.0 - 2018-04-03

- add method getOutputFormat and update saveImage for auto set filename

## 1.7.0 - 2018-03-14

- make `imagick` public

## 1.6.1 - 2018-03-14

- fix bug around `setCompressionQuality`

## 1.6.0 - 2017-12-20

- add `setCompressionQuality`

## 1.5.0 - 2017-10-11

- add `setColorspace`

## 1.4.6 - 2017-10-11

- fix remote pdf handling

## 1.4.5 - 2017-07-18

- fix flattening of pdf

## 1.4.4 - 2017-07-07

- fix where `getNumberOfPages` would report the wrong number when looping through the pdf

## 1.4.3 - 2017-07-07

- fix bugs introduced in 1.4.2

## 1.4.2 - 2017-07-01

- fix for setting custom resolution

## 1.4.1 - 2017-06-28

- fix `setLayerMethod` method

## 1.4.0 - 2017-06-15

- add `setLayerMethod` method

## 1.3.3 - 2017-04-25

- remove use of `Imagick::LAYERMETHOD_FLATTEN` as it messes up the rendering of specific pages

## 1.3.2 - 2017-04-25

- set default format

## 1.3.1 - 2017-04-16

- performance improvements

## 1.3.0 - 2017-03-23

- allow pdf to be loaded from a URL

## 1.2.2 - 2016-12-14

- improve return value

## 1.2.1 - 2016-09-08

- fix for pdf's with transparent backgrounds

## 1.2.0 - 2016-04-29

- added `saveAllPagesAsImages`-function.

## 1.1.0 - 2015-04-13

- added `getImageData`-function.

## 1.0.3 - 2015-01-22

### Bugfix

- Exceptions now live in the right namespace.

## 1.0.1 - 2015-07-03

### Bugfix

- setPage is now working as excepted.

## 1.0.0 - 2015-07-02

### Added

- It's so first release, so everything was added.
