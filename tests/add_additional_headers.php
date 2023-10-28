<?php

use volkerschulz\FileCache;

require('../src/FileCache/FileCache.php');

$filecache = new FileCache('testfiles/loremipsum.txt');
$filecache->addHeader('Content-Type', 'text/plain; charset=UTF8');
$filecache->addHeader('X-FileCache-Test', 'foobar');
$filecache->respond();