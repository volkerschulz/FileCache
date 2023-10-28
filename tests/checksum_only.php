<?php

use volkerschulz\FileCache;

require('../src/FileCache/FileCache.php');

$options = [
    'use_filetime'  => false,
    'use_filesize'  => false,
    'use_checksum'  => true,
    'hash_algo'     => 'xxh128'
];

$filecache = new FileCache('testfiles/loremipsum.txt', $options);
$filecache->addHeader('Content-Type', 'text/plain; charset=UTF8');
$filecache->respond();