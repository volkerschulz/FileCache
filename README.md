# FileCache
PHP file cache handler

## Installation
The recommended way to install FileCache is through
[Composer](https://getcomposer.org/).
```bash
composer require volkerschulz/file-cache
```

## Usage
Basic example with custom header:
```php
use volkerschulz\FileCache;

// Create instance for file 'testfiles/utf.json' 
// with default options
$filecache = new FileCache('testfiles/utf.json');

// Set additional custom headers (optional)
$filecache->addHeader('Content-Type', 'application/json; charset=UTF8');

// Respond to request
$filecache->respond();
```
\
With custom options:
```php
use volkerschulz\FileCache;

$options = [
    'use_checksum'  => true,
    'hash_algo'     => 'xxh128',
    'fresh_for'     => 300
];

// Create instance for file 'testfiles/utf.json' 
// with custom options
$filecache = new FileCache('testfiles/utf.json', $options);

// Set additional custom headers (optional)
$filecache->addHeader('Content-Type', 'application/json; charset=UTF8');

// Respond to request
$filecache->respond();
```

## Options

**use_filetime** *bool* \
*Default: true* - Whether to use the file's last modification time when creating the ETag.\
\
**use_filesize** *bool* \
*Default: true* - Whether to use the file's size when creating the ETag.\
\
**use_checksum** *bool* \
*Default: false* - Whether to use the file's hash when creating the ETag. Depending on the filesize this might consume a lot of CPU and I/O time.

> At least one of **use_filetime**, **use_filesize**, **use_checksum** needs to be *true* to create or compare an ETag.

**hash_algo** *String* \
*Default: 'crc32'* - Which hash algorithm to use when **use_checksum** is *true*. Must be supported by the current PHP version.\
\
**use_etag** *bool* \
*Default: true* - Whether to use an ETag at all. It is strongly recommended to leave that option set to *true*.\
\
**fresh_for** *int* \
*Default: 0* - Number of seconds (from now) the resource is guaranteed not to be stale and should not be revalidated.

## Security

If you discover a security vulnerability within this package, please send an email to security@volkerschulz.de. All security vulnerabilities will be promptly addressed. Please do not disclose security-related issues publicly until a fix has been announced. 

## License

This package is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.
