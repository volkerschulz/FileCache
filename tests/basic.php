<?php

use volkerschulz\FileCache;

require('../src/FileCache/FileCache.php');

$filecache = new FileCache('testfiles/utf.json');
header('Content-Type: application/json; charset=UTF8');
$filecache->respond();