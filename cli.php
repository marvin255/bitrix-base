#!/usr/bin/env php
<?php


$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/web');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS',true); 
define('CHK_EVENT', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


define('CLI_MIGRATIONS_PATH', __DIR__ . '/migrations');


use Symfony\Component\Console\Application;

$application = new Application();

$application->run();