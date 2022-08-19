<?php

namespace App;

class View
{
    public $data = [];

    public function add($key, $data)
    {
        $this->data[$key] = $data;
    }

    public function display($template)
    {
        include $template;
    }
}
