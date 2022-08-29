<?php

namespace App\Parser;

use App\Db;
use App\Models\Question;
use App\Models\Answer;
use RedisException;
use App\Interfaces\ParserInterface;
use simple_html_dom;

class ParserQuestionsAnswers implements ParserInterface
{

    public function parse($link)
    {
        $this->collectDataFromFile($link);
    }

    /**
     * @param $question
     * @param $answer
     * @return void
     * @throws RedisException
     */
    public function connectQuestionAndAnswer($db, $questionId, $answerId)
    {

        $sql = "SELECT * FROM answers_questions WHERE question_id=:question_id AND answer_id=:answer_id";
        $connection = $db->query($sql, [':answer_id' => $answerId, ':question_id' => $questionId]);

        if ($connection == null) {
            try {
                $sql = "INSERT INTO `answers_questions`(`question_id`, `answer_id`) VALUES (:question_id,:answer_id)";
                $db->query($sql, [':answer_id' => $answerId, ':question_id' => $questionId]);
            } catch (\Exception) {
                echo 'attempt to insert duplicate';
            }
        }
    }

    /**
     * starts data withdrawing
     *
     * @param $link
     * @return void
     * @throws RedisException
     */
    public function collectDataFromFile($link)
    {

        $db = new Db();

        //proxy
        $proxy = "localhost:9050";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);

        // Create a DOM object
        $file = new simple_html_dom();

        // Load HTML from a string
        $file->load($curl_scraped_page);

        foreach ($file->find('main') as $main) {
            foreach ($main->find('tbody') as $tbody) {
                foreach ($tbody->find('tr') as $tr) {
                    $questionId = '';
                    $answerId = '';

                    //find question
                    foreach ($tr->find('td.Question') as $tdQuestion) {
                        foreach ($tdQuestion->find('a') as $aQuestion) {
                            $question = $aQuestion->innertext;

                            //adding new question
                            $questionId = $this->addQuestion($db, $question);
                        }
                    }

                    //find answer
                    foreach ($tr->find('td.AnswerShort') as $tdAnswer) {
                        foreach ($tdAnswer->find('a') as $aAnswer) {
                            $answer = $aAnswer->innertext;

                            //adding new answer
                            $answerId = $this->addAnswer($db, $answer);
                        }
                    }

                    //making connection between them
                    $this->connectQuestionAndAnswer($db, $questionId, $answerId);
                }
            }
        }
    }

    /**
     * @param $db
     * @param $question
     * @return mixed
     */
    public function addQuestion($db, $question)
    {


        $returnedQuestion = Question::getQuestion($db, $question);

        //checking if question with such id exists
        //if such id does not exist insert new question
        if ($returnedQuestion == null) {
            try {
                $newQuestion = new Question($question);
                $newQuestion->insert($db);
                $idQuestion = $db->getLastId();

                //if another process already inserted such question take its id
            } catch (\Exception $error) {
                if ($error->errorInfo[0] == '23000') {
                    $returnedQuestion = Question::getQuestion($db, $question);
                    $idQuestion = $returnedQuestion[0]->id;
                }
            }
        } else {
            $idQuestion = $returnedQuestion[0]->id;
        }
        return $idQuestion;

    }

    /**
     * @param $db
     * @param $answer
     * @return mixed
     */
    public function addAnswer($db, $answer)
    {

        $returnedAnswer = Answer::getAnswer($db, $answer);
        //checking if answer with such id exists
        //if such id does not exist insert new question
        if ($returnedAnswer == null) {
            try {
                $newAnswer = new Answer($answer);
                $newAnswer->insert($db);
                $idAnswer = $db->getLastId();
            } catch (\Exception $error) {
                if ($error->errorInfo[0] == '23000') {
                    $returnedAnswer = Answer::getAnswer($db, $answer);
                    $idAnswer = $returnedAnswer[0]->id;
                }
            }
        } else {
            $idAnswer = $returnedAnswer[0]->id;
        }
        return $idAnswer;

    }

}