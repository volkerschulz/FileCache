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
header('Content-Type: application/json; charset=UTF8');
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
header('Content-Type: application/json; charset=UTF8');
// Respond to request
$filecache->respond();
```


## Security

If you discover a security vulnerability within this package, please send an email to security@volkerschulz.de. All security vulnerabilities will be promptly addressed. Please do not disclose security-related issues publicly until a fix has been announced. 

## License

This package is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.
