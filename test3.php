<?php
require __DIR__ . '/simple_html_dom.php';

//echo file_get_contents(__DIR__ . '/simple_html_dom.php');


$proxy = "localhost:9050";
$url = "https://www.kreuzwort-raetsel.net/uebersicht.html";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_HEADER, false);
$curl_scraped_page = curl_exec($ch);
$error = curl_error($ch);

curl_close($ch);

// Create a DOM object
$html = new simple_html_dom();

// Load HTML from a string
$html->load($curl_scraped_page);

foreach ($html->find('main') as $main) {
    foreach ($main->find('li') as $li) {
        foreach ($li->find('a') as $a) {
            //inserting links to task queue
            echo 'https://www.kreuzwort-raetsel.net/'.$a->href."\n";
        }
    }
}