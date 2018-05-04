<?php

//Определяем DOCUMENT_ROOT - без него битрикс не работает
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/web');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

//Отключаем сбор статистики и проверку прав
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_NO_ACCELERATOR_RESET', true);
define('CHK_EVENT', true);

//Подключаем пролог битрикса
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

//Запуск агентов
CAgent::CheckAgents();
define('BX_CRONTAB_SUPPORT', true);
define('BX_CRONTAB', true);
CEvent::CheckEvents();

//Отправка почты
if (CModule::IncludeModule('sender')) {
    \Bitrix\Sender\MailingManager::checkPeriod(false);
    \Bitrix\Sender\MailingManager::checkSend();
}

//Подключаем скрипт, который создает бэкапы
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/tools/backup.php';
