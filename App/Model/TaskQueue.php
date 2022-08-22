<?php

namespace App\Model;

use App\Db;

class TaskQueue
{

    /**
     * sets links which were under work when process stopped to unhandled state
     *
     * @return void
     */
    public static function updateTaskQueue()
    {
        $db = new Db();
        $sql = "UPDATE `task_schedule` SET `status` = 0 WHERE status = 1";
        $db->query($sql);
    }

    public static function insert($params = [])
    {
        $db = new Db();
        $sql = "INSERT INTO `task_schedule`(`link`) VALUES (:link)";
        $db->query($sql, $params);
    }

    public static function getNextLink($db)
    {
        $sql = "SELECT * FROM task_schedule WHERE status = 0 LIMIT 1";
        $row = $db->query($sql);

        if ($row != null) {
            $sql = "UPDATE task_schedule SET status = 1 WHERE id = :id LIMIT 1";
            $db->query($sql,[':id' => $row[0]->id]);
        }

        return $row;
    }

    public static function setAsFinishedWork($db, $params = [])
    {
        $sql = "UPDATE task_schedule SET status = 2 WHERE id = :id";
        return $db->query($sql, $params);
    }

}
