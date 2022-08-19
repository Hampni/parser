<?php

namespace App\Model;

class Question
{

    private $question;

    public function __construct($question)
    {
        $this->question = $question;
    }

    public static function getQuestion($db, $question)
    {
        $sql = "SELECT id FROM questions WHERE question = :question";
        return $db->query($sql, [':question' => $question]);
    }

    public function insert($db)
    {
        $sql = "INSERT INTO `questions`(`question`) VALUES (:question)";
        $db->query($sql, [':question' => $this->question]);
    }

}