<?php

use volkerschulz\FileCache;

require('../src/FileCache/FileCache.php');

$options = [
    'use_checksum'  => false,
    'use_filetime'  => false,
    'hash_algo'     => 'xxh128',
    'use_filesize'  => false
];

$filecache = new FileCache('testfiles/utf.json', $options);
header('Content-Type: application/json; charset=UTF8');
$filecache->respond();