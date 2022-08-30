<?php

$r = new Redis();
$r->connect('127.0.0.1', 6379);

$r->del('bad_links');

//for ($i = 1; $i <= 4838; $i++) {
//    $r->lPop('linksPage');
//}

echo 'links: ' . $r->lLen('linksPage') . "\n";

echo 'badlinks: ' . $r->lLen('bad_links') . "\n";

