<?php

$r = new Redis();
$r->connect('127.0.0.1', 6379);
$r->del('questions');
$r->del('answers');
$r->del('questions_answers');
for ($i = 1; $i <= 4546; $i++) {
    $r->lPop('linksPage');
}



