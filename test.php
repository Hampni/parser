<?php

$r = new Redis();
$r->connect('127.0.0.1', 6379);
$r->del('questions');
$r->del('answers');
$r->del('questions_answers');
for ($i = 1; $i <= 4836; $i++) {
    $r->lPop('linksPage');
}

echo $r->lLen('linksPage') . "\n";
echo $r->lLen('questions') . "\n";
echo $r->lLen('answers') . "\n";
echo $r->lLen('questions_answers') . "\n";

