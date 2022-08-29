<?php
ini_set("default_socket_timeout", -1);
require __DIR__ . '/autoload.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/simple_html_dom.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$r = new Redis();
$r->connect($_ENV['APP_HOST'], 6379);

$parser = new \App\Parser\ParserQuestionsAnswers();
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

    if ($r->lLen('linksPage') > 0) {
        $link = json_decode($r->lPop('linksPage'))->page;
        echo 'amount of links left = ' . $r->lLen('linksPage') . PHP_EOL;

        if ($link) {

            $pid = pcntl_fork();

            if ($pid == -1)
                die('Could not fork');
            if ($pid) {
                $childs[] = $pid;
            } else {
                //start parcing

                $parser->parse($link);

                echo 'finished with ' . $link . PHP_EOL;
                exit();
            }
        }
    } elseif (count($childs) == 0) {
        echo 'finish all' . PHP_EOL;
        exit();
    }
}


