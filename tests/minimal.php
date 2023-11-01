<?php

use volkerschulz\FileCache;

require('../src/FileCache/FileCache.php');

// Create instance 
$filecache = new FileCache('testfiles/cache.png');

// Respond to request
$filecache->respond();