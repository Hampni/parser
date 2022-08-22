<?php
require __DIR__ . '/autoload.php';
require __DIR__ . '/simple_html_dom.php';

//$db = new \App\Db();
//$nextLink = \App\Model\TaskQueue::getNextLink($db);
//var_dump($nextLink);

\App\Model\TaskQueue::updateTaskQueue();

function makeForks($forks)
{
    echo 'Start collecting data!' . PHP_EOL;

    for ($i = 1; $i <= $forks; ++$i) {

        $parser = new \App\Parser\Parcer();

        $pid = pcntl_fork();

        sleep(0.2);

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

                        $parser->collectDataFromFile($db, $file);

                        echo 'finished with ' . $nextLink[0]->id . "\n";
                        \App\Model\TaskQueue::setAsFinishedWork($db, ['id' => $nextLink[0]->id]);

                        file_put_contents(__DIR__ . '/log.txt', 'link ' . $nextLink[0]->link . ' finished parsing on ' . date('d-m-y h:i:s') . PHP_EOL, FILE_APPEND);
                    }
                    $nextLink = \App\Model\TaskQueue::getNextLink($db);
                }
                exit;
        }
    }
}

makeForks(100);

