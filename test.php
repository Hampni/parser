<?php

$r = new Redis();
$r->connect('127.0.0.1', 6379);
$r->del('questions');
$r->del('answers');
for ($i = 1; $i <= 4746; $i++) {
    $r->lPop('linksPage');
}
//var_dump($r->lPop('linksPage'));
//var_dump(json_decode($r->lPop('linksPage'))->page);



//$r->del('links2');
//$r->rPush('links2', '{"link": "https://www.kreuzwort-raetsel.net/"}');
//$r->rPush('links2', '{"link": "https://www.kreuzwort-raetsel.net/lj"}');
//$r->rPush('links2', '{"link": "https://www.kreuzwort-raetsel.net/ljsdc"}');

echo $r->lLen('linksPage') . "\n";
echo $r->lLen('questions') . "\n";
echo $r->lLen('answers') . "\n";
