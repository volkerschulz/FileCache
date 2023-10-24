<?php

use volkerschulz\FileCache;

require('../src/FileCache/FileCache.php');

$options = [
    'use_checksum'  => true,
    'hash_algo'     => 'xxh128',
    'fresh_for'     => 300
];

$filecache = new FileCache('testfiles/utf.json', $options);
header('Content-Type: application/json; charset=UTF8');
$filecache->respond();