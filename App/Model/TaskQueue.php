<?php

namespace App\Model;

use App\Db;

class TaskQueue
{
    public function insert($params = [])
    {
        $db = new Db();
        $sql = "INSERT INTO `task_schedule`(`link`) VALUES (:link)";
        $db->query($sql, $params);
    }

    public function getNextLink($db)
    {
        $sql = "SELECT * FROM task_schedule WHERE status = 0 LIMIT 1; UPDATE task_queue SET status = 1 WHERE status = 0 LIMIT 1;";
        return $db->query($sql);
    }

    public function setAsFinishedWork($db,$params = [])
    {
        $sql = "UPDATE task_schedule SET status = 2 WHERE id = :id";
        return $db->query($sql, $params);
    }

    public function collectDataFromFile($db,$file)
    {
        foreach ($file->find('main') as $main) {
            foreach ($main->find('tbody') as $tbody) {
                foreach ($tbody->find('tr') as $tr) {
                    foreach ($tr->find('td.Question') as $tdQuestion) {
                        foreach ($tdQuestion->find('a') as $aQuestion) {
                            $sql = "INSERT INTO `questions`(`question`) VALUES (:question)";
                            $db->query($sql, [':question' => $aQuestion->innertext]);
                        }
                    }
                }
            }
        }

    }

}
