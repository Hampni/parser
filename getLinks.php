<?php
require __DIR__ . '/autoload.php';
require __DIR__ . '/simple_html_dom.php';
echo 'Collecting links, please wait...' . PHP_EOL;

$r = new Redis();
$r->connect('127.0.0.1', 6379);

$r->del('linksPage');

//link to main page of the site kreuzwort-raetsel.net
$requestLink = 'https://www.kreuzwort-raetsel.net/uebersicht.html';

$letterLinks = [];
$file = file_get_html($requestLink);

//getting letter links
foreach ($file->find('main') as $main) {
    foreach ($main->find('li') as $li) {
        foreach ($li->find('a') as $a) {
            $r->rPush('links', '{"link": "https://www.kreuzwort-raetsel.net/'.$a->href.'"}');
   //         $letterLinks[] = 'https://www.kreuzwort-raetsel.net/' . $a->href;
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
    if (count($childs) >= 5) {
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
                $r->connect('127.0.0.1', 6379);

                $file = file_get_html($link);

                foreach ($file->find('main') as $main) {
                    foreach ($main->find('li') as $li) {
                        foreach ($li->find('a') as $a) {
                            //inserting links to task queue
                            $r->rPush('linksPage', '{"page": "https://www.kreuzwort-raetsel.net/'.$a->href.'"}');
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


//foreach ($letterLinks as $letterLink) {
//    $file = file_get_html($letterLink);
//    foreach ($file->find('main') as $main) {
//        foreach ($main->find('li') as $li) {
//            foreach ($li->find('a') as $a) {
//                $pageLink = 'https://www.kreuzwort-raetsel.net/' . $a->href;
//
//                //inserting links to task queue
//                $r->rPush('links', '{"link": "https://www.kreuzwort-raetsel.net/' . $a->href . '"}');
//
////                \App\Model\TaskQueue::insert([':link' => $pageLink]);
//            }
//        }
//    }
//}
//
//
//var_dump($r->lRange('links', 0, -1));


