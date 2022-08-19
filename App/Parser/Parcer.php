<?php

namespace App\Parser;

use App\Db;
use App\Model\Answer;
use App\Model\Question;

class Parcer
{

    /**
     * get this question id
     *
     * @param $db
     * @param $question
     * @return mixed
     */
    public function getQuestionId($db, $question)
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
     * get this answer id
     *
     * @param $db
     * @param $answer
     * @return mixed
     */
    public function getAnswerId($db, $answer)
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

    /**
     * @param $db
     * @param $questionId
     * @param $answerId
     * @return void
     */
    public function connectQuestionAndAnswer($db, $questionId, $answerId)
    {
        $sql = "SELECT * FROM questions_answers WHERE question_id=:question_id AND answer_id=:answer_id";
        $connection = $db->query($sql,[':question_id' => $questionId, ':answer_id' => $answerId]);

        if ($connection == null) {
            $sql = "INSERT INTO `questions_answers`(`question_id`, `answer_id`) VALUES (:question_id,:answer_id)";
            $db->query($sql, [':question_id' => $questionId, ':answer_id' => $answerId]);
        }

    }

    /**
     * starts data withdrawing
     *
     * @param $db
     * @param $file
     * @return void
     */
    public function collectDataFromFile($db, $file)
    {

        foreach ($file->find('main') as $main) {
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
                    $questionId = $this->getQuestionId($db, $question);

                    //getting answer id
                    $answerId = $this->getAnswerId($db, $answer);

                    //making connection between them
                    $this->connectQuestionAndAnswer($db, $questionId, $answerId);
                }
            }
        }
    }

}