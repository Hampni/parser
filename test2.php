<?php
$r = new Redis();
$r->connect('127.0.0.1', 6379);
//$r->del('linksPage');
var_dump($r->lLen('linksPage'));
var_dump($r->lLen('links'));
//var_dump($r->lRange('linksPage', 0, -1));