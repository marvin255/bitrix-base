<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
        die();
    }

    use Bitrix\Main\Page\Asset;

    global $APPLICATION;

    define('BASE_TEMPLATE_PATH', str_replace('\\', '/', dirname(__FILE__)));
    define('BASE_TEMPLATE_URL', '/local/templates/main');

    //дополнительная обработка данных, которые могут понадобиться в шаблоне
    $arResult = array(
        'isMain' => CSite::InDir('/index.php'),
    );
    if (file_exists(BASE_TEMPLATE_PATH.'/template.php')) {
        $arResult = include BASE_TEMPLATE_PATH.'/template.php';
    }

    //на случай, если унаследуем от этого шаблона новые, подключаем все скрипты и стили через битрикс
    //$APPLICATION->AddHeadScript(BASE_TEMPLATE_URL . '/js/script.js', true);
    //Asset::getInstance()->addJs(BASE_TEMPLATE_URL . '/css/style.css', true);

?><!DOCTYPE html>
<html>
    <head>
        <?php $APPLICATION->ShowHead(); ?>
        <title><?php $APPLICATION->ShowTitle(); ?></title>
    </head>

    <body>
        <?php $APPLICATION->ShowPanel(); ?>
