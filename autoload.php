<?php

  $autoload = function ($classname) {
    include __DIR__ . '/' . str_replace('\\', '/', $classname . '.php');
 };

 spl_autoload_register($autoload);

