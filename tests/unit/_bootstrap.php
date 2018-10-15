<?php

$siteRootFolder = dirname(dirname(__DIR__));
$siteModulesFolder = "{$siteRootFolder}/web/local/modules";

spl_autoload_register(function ($class) use ($siteModulesFolder) {
    $arClass = explode('\\', strtolower(trim($class, '\\ ')));
    if (count($arClass) > 2) {
        $file = $siteModulesFolder;
        $file .= '/' . array_shift($arClass);
        $file .= '.' . array_shift($arClass);
        $file .= '/lib';
        $file .= '/' . implode('/', $arClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
}, true, true);
