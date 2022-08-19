<?php
require __DIR__ . '/autoload.php';
require __DIR__ . '/simple_html_dom.php';

\App\Model\TaskQueue::updateTaskQueue();

$parser = new \App\Parser\Parcer();

for ($i = 1; $i <= 100; ++$i) {

    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            print "Could not fork!\n";
            exit;
        case 0:
            $db = new \App\Db();

            $nextLink = \App\Model\TaskQueue::getNextLink($db);

            while (!empty($nextLink)) {
                if ($nextLink[0]->status == 0) {
                    $file = file_get_html($nextLink[0]->link);

                    echo 'i started working with' . $nextLink[0]->id . "\n";
                    $parser->collectDataFromFile($db, $file);

                    echo 'i finished working with' . $nextLink[0]->id . "\n";
                    \App\Model\TaskQueue::setAsFinishedWork($db, ['id' => $nextLink[0]->id]);

                    file_put_contents(__DIR__ . '/log.txt', 'link ' . $nextLink[0]->link . ' finished parsing on ' . date('d-m-y h:i:s') . PHP_EOL, FILE_APPEND);
                }
                $nextLink = \App\Model\TaskQueue::getNextLink($db);
            }
            exit;
    }
}