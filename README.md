# FileCache
PHP file cache handler

## Installation
This project using composer.
```
$ composer require volkerschulz/file-cache
```

## Usage
```php
<?php

use volkerschulz\FileCache;

$filecache = new FileCache('testfiles/utf.json');
header('Content-Type: application/json; charset=UTF8');
$filecache->respond();
```
