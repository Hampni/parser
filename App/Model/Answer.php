<?php

namespace App\Model;

class Answer
{
    private $answer;

    private $letters;

    public function __construct($answer)
    {
        $this->answer = $answer;
    }

    public static function getAnswer($db, $answer)
    {
        $sql = "SELECT id FROM answers WHERE answer = :answer";
        return $db->query($sql, [':answer' => $answer]);
    }

    public function insert($db)
    {
        $sql = "INSERT INTO `answers`(`answer`, `letters`) VALUES (:answer, :letters)";
        $db->query($sql, [':answer' => $this->answer, ':letters' => strlen($this->answer)]);
    }


}