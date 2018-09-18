<?php

//Подключаем автозагрузку классов композера.
$composerAutoloader = dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
if (file_exists($composerAutoloader)) {
    require_once $composerAutoloader;
}
