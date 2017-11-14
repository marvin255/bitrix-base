<?php

//Подключаем автозагрузку классов композера.
require_once dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';

//подключаем общие функции для всего проекта
require_once __DIR__ . '/include/functions.php';
//подключаем события
require_once __DIR__ . '/include/events.php';
//подключаем агентов
require_once __DIR__ . '/include/agents.php';
