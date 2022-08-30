<?php

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

    if ($r->lLen('bad_links') > 0) {
        $link = json_decode($r->lPop('bad_links'))->page;
        echo 'amount of links left = ' . $r->lLen('bad_links') . PHP_EOL;

        if ($link) {

            $pid = pcntl_fork();

            if ($pid == -1)
                die('Could not fork');
            if ($pid) {
                $childs[] = $pid;
            } else {
                //start parcing

                $parser->parse($link);

                exit();
            }
        }
    } elseif (count($childs) == 0) {
        echo 'finish all with bad links' . PHP_EOL;
        exit();
    }
}


