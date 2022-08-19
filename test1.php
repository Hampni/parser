<?php
require __DIR__ . '/autoload.php';

for ($i = 1; $i <= 3; ++$i) {

    $pid = pcntl_fork();

    $config = new \App\Config();
    $connection = new \PDO('mysql:host=' . $config->data['db']['host'] . ';dbname=' . $config->data['db']['dbname'], $config->data['db']['username'], $config->data['db']['password']);

    if ($pid == -1) {
        // Fork failed
        exit(1);
    } else if ($pid) {
        // We are the parent
        // Can no longer use $db because it will be closed by the child
        // Instead, make a new MySQL connection for ourselves to work with
        $connection = new \PDO('mysql:host=' . $config->data['db']['host'] . ';dbname=' . $config->data['db']['dbname'], $config->data['db']['username'], $config->data['db']['password']);
    } else {

        $sql = "SELECT * FROM task_queue WHERE status = 0 LIMIT 1; UPDATE task_queue SET status = 1 WHERE status = 0 LIMIT 1;";

        $sth = $connection->prepare($sql);
        $sth->execute();
        $nextLink = $sth->fetchAll(\PDO::FETCH_CLASS);

        while (!empty($nextLink)) {
            if ($nextLink[0]->status == 0) {
                echo $nextLink[0]->id . "\n";
                file_put_contents(__DIR__ . '/log.txt', 'status changed in  ' . $nextLink[0]->id . PHP_EOL, FILE_APPEND);
            }

            $sql = "SELECT * FROM task_queue WHERE status = 0 LIMIT 1; UPDATE task_queue SET status = 1 WHERE status = 0 LIMIT 1;";
            $sth = $connection->prepare($sql);
            $sth->execute();
            $nextLink = $sth->fetchAll(\PDO::FETCH_CLASS);
        }
        exit(0);
    }
}