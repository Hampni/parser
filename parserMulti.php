<?php

require __DIR__ . '/autoload.php';
require __DIR__ . '/simple_html_dom.php';


//$requestLink = 'https://www.kreuzwort-raetsel.net/uebersicht.html';
//$letterLinks = [];
//$pageLinks = [];
//$questionLinks = [];
//$file = file_get_html($requestLink);


$link = new \App\Model\TaskQueue();

for ($i = 1; $i <= 5; ++$i) {

    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            print "Could not fork!\n";
            exit;
        case 0:
            $db = new \App\Db();

            $nextLink = $link->getNextLink($db);
            while (!empty($nextLink)) {
                if ($nextLink[0]->status == 0) {
                    $file = file_get_html($nextLink[0]->link);

                    $link->collectDataFromFile($db, $file);

                    echo $nextLink[0]->id . "\n";
                    $link->setAsFinishedWork($db, ['id' => $nextLink[0]->id]);

                    //file_put_contents(__DIR__ . '/log.txt', 'status changed in  ' . $nextLink[0]->id . PHP_EOL, FILE_APPEND);
                }
                $nextLink = $link->getNextLink($db);
            }
            exit;
    }
}

//working
//$pid = pcntl_fork();
//switch ($pid) {
//    case -1:
//        print "Could not fork!\n";
//        exit;
//    case 0:
//        $nextLink = $link->getNextLink();
//
//        while (!empty($nextLink)) {
//            var_dump($nextLink[0]->id);
//            // set status to 1 - under work
//            $link->setAsUnderWork(['id' => $nextLink[0]->id]);
//            file_put_contents(__DIR__ . '/log.txt', 'link ' . $nextLink[0]->link . ' inserted on ' . date('d-m-y h:i:s') . PHP_EOL, FILE_APPEND);
//            $nextLink = $link->getNextLink();
//        }
//
//    default:
//        $nextLink = $link->getNextLink();
//
//        while (!empty($nextLink)) {
//            var_dump($nextLink[0]->id);
//            // set status to 1 - under work
//            $link->setAsUnderWork(['id' => $nextLink[0]->id]);
//            file_put_contents(__DIR__ . '/log.txt', 'link ' . $nextLink[0]->link . ' inserted on ' . date('d-m-y h:i:s') . PHP_EOL, FILE_APPEND);
//            $nextLink = $link->getNextLink();
//        }
//        pcntl_wait($status);
//}


//foreach ($file->find('main') as $main) {
//    foreach ($main->find('li') as $li) {
//        foreach ($li->find('a') as $a) {
//            $letterLinks[] = 'https://www.kreuzwort-raetsel.net/' . $a->href;
//        }
//    }
//}
//
//foreach ($letterLinks as $letterLink) {
//    $file = file_get_html($letterLink);
//    foreach ($file->find('main') as $main) {
//        foreach ($main->find('li') as $li) {
//            foreach ($li->find('a') as $a) {
//                $pageLink = 'https://www.kreuzwort-raetsel.net/' . $a->href;
//                $link->insert([':link' => $pageLink]);
//                file_put_contents(__DIR__ . '/log.txt', 'link inserted on ' . date('d-m-y h:i:s') . PHP_EOL, FILE_APPEND);
//            }
//        }
//    }
//}
//foreach ($pageLinks as $pageLink) {
//    $file = file_get_html($pageLink);
//    foreach ($file->find('main') as $main) {
//        foreach ($main->find('tbody') as $tbody) {
//            foreach ($main->find('tr') as $tr) {
//                foreach ($tr->find('td.Question') as $tdQuestion) {
//                    foreach ($tdQuestion->find('a') as $aQuestion) {
//                        echo $aQuestion->innertext . '<br>';
//                    }
//                }
//                foreach ($tr->find('td.AnswerShort') as $tdAnswer) {
//                    foreach ($tdAnswer->find('a') as $aAnswer) {
//                        echo $aAnswer->innertext . '<br>';
//                        echo strlen($aAnswer->innertext). '<br>';
//                    }
//                }
//            }
//        }
//    }
//}