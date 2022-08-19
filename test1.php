<?php


require __DIR__ . '/autoload.php';
require __DIR__ . '/simple_html_dom.php';



$db = new \App\Db();

//$sql = "INSERT INTO `questions`(`question`) VALUES ('question 10')";
//try {
//    $db->query($sql);
//} catch (Exception $e) {
//    echo 'error';
//    var_dump($e->errorInfo[0]);
//}


$link = 'https://www.kreuzwort-raetsel.net/a-2200';

$file = file_get_html($link);

foreach ($file->find('main') as $main) {
    foreach ($main->find('tbody') as $tbody) {
        foreach ($tbody->find('tr') as $tr) {

            $question = '';
            $answer = '';
            $idQuestion = '';
            $idAnswer = '';
            $answerLength = '';

            foreach ($tr->find('td.Question') as $tdQuestion) {
                foreach ($tdQuestion->find('a') as $aQuestion) {
                $question = $aQuestion->innertext;
                }
            }
            foreach ($tr->find('td.AnswerShort') as $tdAnswer) {
                foreach ($tdAnswer->find('a') as $aAnswer) {
                    $answer = $aAnswer->innertext;
                }
            }


            $sql = "SELECT id FROM questions WHERE question = :question";
            $returnedQuestion = $db->query($sql, [':question' => $question]);


            if ($returnedQuestion == null) {
                try {
                    $sql = "INSERT INTO `questions`(`question`) VALUES (:question)";
                    $db->query($sql, [':question' => $question]);

                    $idQuestion = $db->getLastId();

                } catch (Exception $error) {
                    if ($error->errorInfo[0] == '23000') {
                        $sql = "SELECT id FROM questions WHERE question = :question";
                        $returnedQuestion = $db->query($sql, [':question' => $question]);

                        $idQuestion = $returnedQuestion[0]->id;
                    }
                }
            } else {
                $idQuestion = $returnedQuestion[0]->id;
            }

            echo 'question id: <br>';
            echo $idQuestion;
            echo '<br>';





            $sql = "SELECT id FROM answers WHERE answer = :answer";
            $returnedAnswer = $db->query($sql, [':answer' => $answer]);


            if ($returnedAnswer == null) {
                try {
                    $sql = "INSERT INTO `answers`(`answer`, `letters`) VALUES (:answer, :letters)";
                    $db->query($sql, [':answer' => $answer, ':letters' => strlen($answer)]);

                    $idAnswer = $db->getLastId();

                } catch (Exception $error) {
                    if ($error->errorInfo[0] == '23000') {
                        $sql = "SELECT id FROM answers WHERE answer = :answer";
                        $returnedAnswer = $db->query($sql, [':answer' => $answer]);

                        $idAnswer = $returnedAnswer[0]->id;
                    }
                }
            } else {
                $idAnswer = $returnedAnswer[0]->id;
            }

            echo 'answer id: <br>';
            echo $idAnswer;
            echo '<br>';
            echo '<br>';

            $sql = "INSERT INTO `questions_answers`(`question_id`, `answer_id`) VALUES (:question_id,:answer_id)";
            $db->query($sql, [':question_id' => $idQuestion, ':answer_id' => $idAnswer]);

        }
    }
}