<?php
require __DIR__ . '/autoload.php';
require __DIR__ . '/simple_html_dom.php';


$requestLink = 'https://www.kreuzwort-raetsel.net/uebersicht.html';
$letterLinks = [];
$pageLinks = [];
$questionLinks = [];
$file = file_get_html($requestLink);

$link = new \App\Model\TaskQueue();

foreach ($file->find('main') as $main) {
    foreach ($main->find('li') as $li) {
        foreach ($li->find('a') as $a) {
            $letterLinks[] = 'https://www.kreuzwort-raetsel.net/' . $a->href;
        }
    }
}

foreach ($letterLinks as $letterLink) {
    $file = file_get_html($letterLink);
    foreach ($file->find('main') as $main) {
        foreach ($main->find('li') as $li) {
            foreach ($li->find('a') as $a) {
                $pageLink = 'https://www.kreuzwort-raetsel.net/' . $a->href;
                $link->insert([':link' => $pageLink]);
//                $pageLinks[] = $pageLink;
                //file_put_contents(__DIR__ . '/log.txt', 'link inserted on ' . date('d-m-y h:i:s') . PHP_EOL, FILE_APPEND);
            }
        }
    }
}


////$link = 'https://www.kreuzwort-raetsel.net/a-600';
//
//foreach ()
//    $file = file_get_html($link);
//    foreach ($file->find('main') as $main) {
//        foreach ($main->find('tbody') as $tbody) {
//            foreach ($main->find('tr') as $tr) {
//                foreach ($tr->find('td.Question') as $tdQuestion) {
//                    foreach ($tdQuestion->find('a') as $aQuestion) {
//                        $questionLink = 'https://www.kreuzwort-raetsel.net/' . $aQuestion->href;
//                        $questionText = $aQuestion->innertext;
//
//                        if (!in_array($questionLink,$questionLinks)) {
//                            $questionLinks[] = $questionLink;
//                            echo $questionLink . '<br>';
//                            echo $questionText . '<br>';
//                        }
//                    }
//                }
//            }
//        }
//    }


