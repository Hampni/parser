<?php

$r = new Redis();
$r->connect('127.0.0.1', 6379);
$r->del('questions');
$r->del('answers');
$r->del('questions_answers');
for ($i = 1; $i <= 4348; $i++) {
    $r->lPop('linksPage');
}


echo 'links: ' . $r->lLen('linksPage') . "\n";

echo 'badlinks: ' . $r->lLen('bad_links'). "\n";

var_dump($r->lRange('bad_links', 0 , -1));