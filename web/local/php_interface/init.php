<?php

//Подключаем автозагрузку классов композера.
$composerAutoloader = dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
if (file_exists($composerAutoloader)) {
    require_once $composerAutoloader;
}

//подключаем общие функции для всего проекта
require_once __DIR__ . '/include/functions.php';
//подключаем события
require_once __DIR__ . '/include/events.php';
//подключаем агентов
require_once __DIR__ . '/include/agents.php';
