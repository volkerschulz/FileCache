<?php

use volkerschulz\FileCache;

require('../src/FileCache/FileCache.php');

$filecache = new FileCache('testfiles/cache.png');
$filecache->respond();