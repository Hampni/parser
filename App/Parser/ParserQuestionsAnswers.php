<?php

namespace App\Parser;

use RedisException;
use App\Interfaces\ParserInterface;

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
    public function connectQuestionAndAnswer($question, $answer)
    {

        //connect to redis
        $r = new \Redis();
        $r->connect('127.0.0.1', 6379);

        $question_answer = [
            'question' => $question,
            'answer' => $answer
        ];
        $r->rPush('questions_answers', json_encode($question_answer));
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

        $file = file_get_html($link);

        foreach ($file->find('main') as $main) {
            foreach ($main->find('tbody') as $tbody) {
                foreach ($tbody->find('tr') as $tr) {
                    $question = '';
                    $answer = '';

                    //find question
                    foreach ($tr->find('td.Question') as $tdQuestion) {
                        foreach ($tdQuestion->find('a') as $aQuestion) {
                            $question = $aQuestion->innertext;

                            //adding new question
                            $this->addQuestion($question);
                        }
                    }

                    //find answer
                    foreach ($tr->find('td.AnswerShort') as $tdAnswer) {
                        foreach ($tdAnswer->find('a') as $aAnswer) {
                            $answer = $aAnswer->innertext;

                            //adding new answer
                            $this->addAnswer($answer);
                        }
                    }

                    //making connection between them
                    $this->connectQuestionAndAnswer($question, $answer);
                }
            }
        }
    }

    /**
     * @param $question
     * @return void
     * @throws RedisException
     */
    public function addQuestion($question)
    {
        $r = new \Redis();
        $r->connect('127.0.0.1', 6379);
        $r->hSet('questions', $question, $question);
    }

    /**
     * @param $answer
     * @return void
     * @throws RedisException
     */
    public function addAnswer($answer)
    {
        $r = new \Redis();
        $r->connect('127.0.0.1', 6379);
        $r->hSet('answers', $answer, $answer);
    }

}