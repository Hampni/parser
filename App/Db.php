<?php

namespace App;

use App\Config;

class Db
{
    protected \PDO $dbh;

    public function __construct()
    {
        $config = new Config();
        return $this->dbh = new \PDO('mysql:host=' . $config->data['db']['host'] . ';dbname=' . $config->data['db']['dbname'], $config->data['db']['username'], $config->data['db']['password']);
    }

    public function query($sql, $params = []): array
    {
        $sth = $this->dbh->prepare($sql);
        $sth->execute($params);
        $data = $sth->fetchAll(\PDO::FETCH_CLASS);
        return $data;
    }
}
