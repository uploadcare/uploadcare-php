# Changelog

## 1.2.3
- be more explicit on cURL errors

## 1.2.2
- fix sample-project composer file
- always write widget's charset
- default to sync widget load
- add optional $async argument to Widget->getScriptTag()

## 1.2.1
- bump widget version to 1.0.1 (see [widget changelog][widget changelog])

## 1.2.0
- bump widget version to 1.0.0 (see [widget changelog][widget changelog])
    major feature is reponsive behavior of widget dialog

## 1.1.3
- allow setting custom CDN host

## 1.1.2
- add preview operation

## 1.1.1
- accept CDN URL in File's constructor
- bump widget version to 0.18.3 (see [widget changelog][widget changelog])

## 1.1.0
- drop 5.2 support
- fix composer support

## 1.0.9

This is last uploadcare-php version that will support php 5.2. Expect no features added, only bugs fixed.
- bump widget version to 0.18.1 (see [widget changelog][widget changelog])

## 1.0.8

- fix file copy request
- bump widget version to 0.17.2 (see [widget changelog][widget changelog])

## 1.0.7

- support 'limit' param in Api->getFileList() and Api->getFilePaginationInfo()

## 1.0.6

- deprecate File->file_id, use File->uuid
- fix Api->getFileList()

## 1.0.5

- bump widget version to 0.17.1 (see [widget changelog][widget changelog])
- fix HEAD requests
- allow custom User Agent
- prepopulate File with data on Api->getFileList()
- add Groups API
- add File->copy()

[widget changelog]: https://github.com/uploadcare/uploadcare-widget/blob/master/HISTORY.markdown
