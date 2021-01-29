# Changelog
All notable changes to this project will be documented in this file.

The format is based now on [Keep a
Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [3.0.3]
### Fix
- Fixed file copy methods between default Uploadcare storage and remote storage (Amazon s3)

## [3.0.2]
### Fix
- Serialization for `originalFileUrl`.

## [3.0.1]
### Fix
- Added support for guzzlehttp/guzzle:^7.

## [3.0.0] 
### BREAKING CHANGES
- You must update PHP to 5.6 or a newer version.
- This **completely new** version **is not** backward compatibile with the
  previous one!

### Features
- Add interfaces for all `File` and dependent classes.
- Add [Guzzle http client](http://github.com/guzzle/guzzle) as standard client.
  You can override client with any `GuzzleHttp\ClientInterface` client
  implementation if you want.
- Full REST and Upload APIs coverage.

## [2.4.1-rc] - 2020-06-02
### Added
- Support for [multipart
  uploads](https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart)

## [2.4.0-rc] - 2020-01-13
### Added
- Support for [signed
  uploads](https://uploadcare.com/docs/api_reference/upload/signed_uploads/)

## [2.3.0] - 2019-09-06
### Added
- Allow to specify filename for local uploads
- Allow to control "autostore" for local uploads

### Fixed
- Wrong return type for `createRemoteCopy` method in `API.php`. As result is
  always casted to `string`, the return type should be `string`

## [2.2.1] - 2018-05-14
### Deprecated
- `$api->getUserAgent()` was deprecated and will be removed in next major
  version. Use `$api->getUserAgentHeader()` instead.

## [2.2.0] - 2018-05-14
### Added
- Allow user to specify User Agent
- User agent reporting for the lib and integrations that use it
- data-integration attribute to the widget

### Changed
- User agent now is reporting in new format by default
- `$api->getUserAgent()` replaced with `$api->getUserAgentHeader()`

### Fixed
- `$api->getGroupList($options = array())` method
- Some mistakes in description of methods
- Hitting max throttling attempts if request was successful

## [2.1.2]
- add `__isset()` to classes that have `__get()` that fixes class behaviours in
  PHP 7.0.6+ see [error description](https://bugs.php.net/bug.php?id=71359)

## [2.1.1]
- fix `File->op()`

## [2.1.0]
- change `File->crop()` and `File->scaleCrop()` behaviou, now they throw
  exceptions if parameters `$width` or `$height` are 0 or not provided
- add `File->getPath()` method
- fix `Api->createRemoteCopy()` default behaviour

## [2.0.0]
- use latest stable build of version 3 (see [widget changelog][widget
  changelog])
- use REST API version 0.5
- update pagination functions for files and groups
- add batch files methods: `Api->storeMultipleFiles()` and
  `Api->deleteMultipleFiles()`
- add new copy methods: `Api->createLocalCopy()` and `Api->createRemoteCopy()`
- add `Helper->deprecate()` method
- change the signature of `Uploader->fromUrl()`, old signature is deprecated but
  will work until 3.0
- deprecate `File->copy()` and `File->copyTo()`
- deprecate `Api->copyFile()`

## [1.5.5]
- bump widget version to 2.9.0 (see [widget changelog][widget changelog])
- add optional "full" argument to Widget->getScriptTag

## [1.5.4]
- fix: File in a Group is not loosing default effects (cropping etc.)

## [1.5.3]
- bump widget version to 2.8.2 (see [widget changelog][widget changelog])

## [1.5.2]
- fix of throttled request exception (thanks to Alexey Scherbakov,
  https://github.com/lexabug)
- fix of pagination logic

## [1.5.1]
- add throttled requests handling
- add customizable User Agent string
- fix upload from url logical bug
- bump widget to 2.5.9 (see [widget changelog][widget changelog])

## [1.5.0]
- upgrade server api usage to v0.4, add new style pagination support
- add FileIterator
- change logic of `Api->getFileList()`: it returns FileIterator object instead
  of array and incoming parameters are now grouped in an array
- remove `Api->getFilePaginationInfo()`

## [1.4.1]
- change logic of `Api->getGroupList()` and `Api->__getPath()`
- fix [#41](https://github.com/uploadcare/uploadcare-php/issues/41)
- bump widget version to 2.5.1 (see [widget changelog][widget changelog])

## [1.4.0]
- use proper authentication instead of simple
- bump widget version to 2.5.0 (see [widget changelog][widget changelog])

## [1.3.4]
- add `Api->cdn_protocol`
- add `Api->getCdnUri()`
- change default CDN protocol to HTTPS

## [1.3.3]
- fix `Group->getFiles()`

## [1.3.2]
- add `Uploader->createGroup()`
- add `Group->updateInfo()`
- add Group API tests

## [1.3.1]
- fix `Group->store()`
- bump widget version to 2.3.5 (see [widget changelog][widget changelog])

## [1.3.0]
- **IMPORTANT:** backward incompatible changes in widget behavior introduced in
  2.0.0, please read [changelog
  entry](https://github.com/uploadcare/uploadcare-widget/blob/master/HISTORY.markdown#200-20022015)
  carefully
- bump widget version to 2.3.4 (see [widget changelog][widget changelog])
- allow CDN URLs in Group constructor

## [1.2.6]
- bump widget version to 1.4.6 (see [widget changelog][widget changelog])
- fix `Api->copyFile()` when copying to custom storage
- add `File->copyTo()` shortcut

## [1.2.5]
- bump widget version to 1.4.2 (see [widget changelog][widget changelog])
- add AUTHORS.txt

## [1.2.4]
- bump widget version to 1.2.0 (see [widget changelog][widget changelog])

## [1.2.3]
- be more explicit on cURL errors

## [1.2.2]
- fix sample-project composer file
- always write widget's charset
- default to sync widget load
- add optional $async argument to `Widget->getScriptTag()`

## [1.2.1]
- bump widget version to 1.0.1 (see [widget changelog][widget changelog])

## [1.2.0]
- bump widget version to 1.0.0 (see [widget changelog][widget changelog]) major
    feature is reponsive behavior of widget dialog

## [1.1.3]
- allow setting custom CDN host

## [1.1.2]
- add preview operation

## [1.1.1]
- accept CDN URL in File's constructor
- bump widget version to 0.18.3 (see [widget changelog][widget changelog])

## [1.1.0]
- drop 5.2 support
- fix composer support

## [1.0.9]

This is last uploadcare-php version that will support php 5.2. Expect no
features added, only bugs fixed.
- bump widget version to 0.18.1 (see [widget changelog][widget changelog])

## [1.0.8]

- fix file copy request
- bump widget version to 0.17.2 (see [widget changelog][widget changelog])

## [1.0.7]

- support `limit` param in `Api->getFileList()` and
  `Api->getFilePaginationInfo()`

## [1.0.6]

- deprecate `File->file_id`, use `File->uuid`
- fix `Api->getFileList()`

## [1.0.5]

- bump widget version to 0.17.1 (see [widget changelog][widget changelog])
- fix HEAD requests
- allow custom User Agent
- prepopulate File with data on `Api->getFileList()`
- add Groups API
- add `File->copy()`
