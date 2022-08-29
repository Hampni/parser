<?php
require __DIR__ . '/autoload.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/simple_html_dom.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$r = new Redis();
$r->connect('127.0.0.1', 6379);

echo 'Collecting links, please wait...' . PHP_EOL;

$r->del('linksPage');

//proxy
$proxy = "localhost:9050";

//link to main page of the site kreuzwort-raetsel.net
$url = 'https://www.kreuzwort-raetsel.net/uebersicht.html';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_HEADER, false);
$curl_scraped_page = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

$letterLinks = [];

$file = new simple_html_dom();

// Load HTML from a string
$file->load($curl_scraped_page);

//getting letter links
foreach ($file->find('main') as $main) {
    foreach ($main->find('li') as $li) {
        foreach ($li->find('a') as $a) {
            $r->rPush('links', '{"link": "https://www.kreuzwort-raetsel.net/' . $a->href . '"}');
        }
    }
}

//getting pages links
$childs = [];
while (true) {
    // check forks
    foreach ($childs as $key => $pid) {
        $res = pcntl_waitpid($pid, $status, WNOHANG);

        // If the process has already exited
        if ($res == -1 || $res > 0) {
            unset($childs[$key]);
        }
    }

    // check limit
    if (count($childs) >= $_ENV['FORKS_NUMBER']) {
        continue;
    }

    // get links
    if ($r->lLen('links') > 0) {

        $link = json_decode($r->lPop('links'))->link;

        echo 'amount of links left = ' . $r->lLen('links') . PHP_EOL;

        if ($link) {

            $pid = pcntl_fork();

            if ($pid == -1)
                die('Could not fork');
            if ($pid) {
                $childs[] = $pid;
            } else {
                $r = new Redis();
                $r->connect($_ENV['APP_HOST'], 6379);

                //proxy
                $proxy = "localhost:9050";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $link);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $link_scraped_page = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);

                $file = new simple_html_dom();

                // Load HTML from a string
                $file->load($link_scraped_page);

                foreach ($file->find('main') as $main) {
                    foreach ($main->find('li') as $li) {
                        foreach ($li->find('a') as $a) {
                            //inserting links to task queue
                            $r->rPush('linksPage', '{"page": "https://www.kreuzwort-raetsel.net/' . $a->href . '"}');
                        }
                    }
                }
                echo 'finished with ' . $link . PHP_EOL;
                exit();
            }
        }
    } elseif (count($childs) == 0) {
        echo 'Collecting finished! =)' . PHP_EOL;
        exit();
    }
}

