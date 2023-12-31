<?php

use volkerschulz\FileCache;

require('../src/FileCache/FileCache.php');

$options = [
    'use_checksum'  => true,
    'hash_algo'     => 'xxh128',
    'fresh_for'     => 300
];

$filecache = new FileCache('testfiles/loremipsum.txt', $options);
$filecache->addHeader('Content-Type', 'text/plain; charset=UTF8');
$filecache->respond();