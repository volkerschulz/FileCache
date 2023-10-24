<?php

use volkerschulz\FileCache;

require('../vendor/autoload.php');

$filecache = new FileCache('testfiles/utf.json');
header('Content-Type: application/json; charset=UTF8');
$filecache->respond();