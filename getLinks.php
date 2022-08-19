<?php
require __DIR__ . '/autoload.php';
require __DIR__ . '/simple_html_dom.php';

//link to main page of the site kreuzwort-raetsel.net
$requestLink = 'https://www.kreuzwort-raetsel.net/uebersicht.html';

$letterLinks = [];
$file = file_get_html($requestLink);

//getting letter links
foreach ($file->find('main') as $main) {
    foreach ($main->find('li') as $li) {
        foreach ($li->find('a') as $a) {
            $letterLinks[] = 'https://www.kreuzwort-raetsel.net/' . $a->href;
        }
    }
}

//getting pages links
foreach ($letterLinks as $letterLink) {
    $file = file_get_html($letterLink);
    foreach ($file->find('main') as $main) {
        foreach ($main->find('li') as $li) {
            foreach ($li->find('a') as $a) {
                $pageLink = 'https://www.kreuzwort-raetsel.net/' . $a->href;

                //inserting links to task queue
                \App\Model\TaskQueue::insert([':link' => $pageLink]);
            }
        }
    }
}



