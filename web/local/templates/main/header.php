<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
        die();
    }

    use Bitrix\Main\Page\Asset;
    use Bitrix\Main\Application;

    global $APPLICATION;

    define('BASE_TEMPLATE_PATH', realpath(dirname(__FILE__)));
    define('BASE_TEMPLATE_URL', '/local/templates/main');

    //на случай, если унаследуем от этого шаблона новые, подключаем все скрипты и стили через битрикс
    //Asset::getInstance()->addCss(BASE_TEMPLATE_URL.'/stylesheets/main.css', true);
    //Asset::getInstance()->addJs(BASE_TEMPLATE_URL.'/javascripts/main.js', true);
?><!DOCTYPE html>
<html>
    <head>
        <?php $APPLICATION->ShowHead(); ?>
        <title><?php $APPLICATION->ShowTitle(); ?></title>
    </head>

    <body>
        <?php $APPLICATION->ShowPanel(); ?>
