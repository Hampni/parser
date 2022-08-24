<?php
ini_set("default_socket_timeout", -1);
require __DIR__ . '/simple_html_dom.php';

$r = new Redis();
$r->connect('127.0.0.1', 6379);

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
    if (count($childs) >=100) {
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
//                $file = file_get_html($link);
//                foreach ($file->find('main') as $main) {
//                    foreach ($main->find('tbody') as $tbody) {
//                        foreach ($tbody->find('tr') as $tr) {
//                            $question = '';
//                            //find question
//                            foreach ($tr->find('td.Question') as $tdQuestion) {
//                                foreach ($tdQuestion->find('a') as $aQuestion) {
//                                    $question = $aQuestion->innertext;
//                                    $r = new Redis();
//                                    $r->connect('127.0.0.1', 6379);
//                                    $r->rPush('questions', '{"question": "' . $question . '"}');
//                                }
//                            }
//                            foreach ($tr->find('td.AnswerShort') as $tdAnswer) {
//                                foreach ($tdAnswer->find('a') as $aAnswer) {
//                                    $answer = $aAnswer->innertext;
//                                    $r = new Redis();
//                                    $r->connect('127.0.0.1', 6379);
//                                    $r->rPush('answers', '{"answer": "' . $answer . '"}');
//
//                                }
//                            }
//                        }
//                    }
//                }
//                echo 'finished with ' . $link . PHP_EOL;
                exit();
            }
        }
    } elseif (count($childs) == 0) {
        echo 'finish all' . PHP_EOL;
//        $r = new Redis();
//        $r->connect('127.0.0.1', 6379);
//        echo $r->lLen('questions') . "\n";
//        $r = new Redis();
//        $r->connect('127.0.0.1', 6379);
//        echo $r->lLen('answers') . "\n";
        exit();
    }
}
