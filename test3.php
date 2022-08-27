<?php
$proxy = "tor:9050";
$url = "https://api.ipify.org/?format=json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_HEADER, true);
$curl_scraped_page = curl_exec($ch);
$error = curl_error($ch);
echo $error;
echo $curl_scraped_page;
curl_close($ch);