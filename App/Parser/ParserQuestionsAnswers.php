<?php

namespace App\Parser;

use App\Db;
use App\Model\Answer;
use App\Model\Question;
use RedisException;

class ParserQuestionsAnswers implements ParserInterface
{

    public function parse($link)
    {
        $this->collectDataFromFile($link);
    }

    /**
     * get this question id
     *
     * @param $question
     * @return mixed
     * @throws RedisException
     */
    public function getQuestionId($question)
    {
        //connect to redis
        $r = new \Redis();
        $r->connect('127.0.0.1', 6379);


        //taking question from db

        //checking if question exists
        //if does not exist insert new question

        //return question
        return null;
    }

    /**
     * get this answer id
     *
     * @param $answer
     * @return mixed
     * @throws RedisException
     */
    public function getAnswerId($answer)
    {

        //connect to redis
        $r = new \Redis();
        $r->connect('127.0.0.1', 6379);

        //taking answer from db

        //checking if answer exists
        //if does not exist insert new answer

        //return answer
        return null;
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

        //insert new relation question_answer

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

        foreach ($link->find('main') as $main) {
            foreach ($main->find('tbody') as $tbody) {
                foreach ($tbody->find('tr') as $tr) {
                    $question = '';
                    $answer = '';

                    //find question
                    foreach ($tr->find('td.Question') as $tdQuestion) {
                        foreach ($tdQuestion->find('a') as $aQuestion) {
                            $question = $aQuestion->innertext;
                        }
                    }

                    //find answer
                    foreach ($tr->find('td.AnswerShort') as $tdAnswer) {
                        foreach ($tdAnswer->find('a') as $aAnswer) {
                            $answer = $aAnswer->innertext;
                        }
                    }

                    //getting question id
                    $question = $this->getQuestionId($question);

                    //getting answer id
                    $answer = $this->getAnswerId($answer);

                    //making connection between them
                    $this->connectQuestionAndAnswer($question, $answer);
                }
            }
        }
    }

}