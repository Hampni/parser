<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/autoload.php';
require __DIR__ . '/simple_html_dom.php';

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

\App\Model\TaskQueue::updateTaskQueue();
function makeForks($forks)
{
    echo 'Start collecting data!' . PHP_EOL;

    for ($i = 1; $i <= $forks; ++$i) {

        $parser = new \App\Parser\Parcer();

        $pid = pcntl_fork();

        $log = new Logger('Info');
        $log->pushHandler(new StreamHandler(__DIR__ . '/log.txt', Level::Warning));

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

                        $log->warning('link ' . $nextLink[0]->link . ' finished parsing');

                    }
                    $nextLink = \App\Model\TaskQueue::getNextLink($db);
                }
                exit;
        }
    }
}

makeForks($_ENV['FORKS_NUMBER']);

