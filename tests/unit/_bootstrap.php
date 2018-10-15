<?php

$siteRootFolder = dirname(dirname(__DIR__));
$siteModulesFolder = "{$siteRootFolder}/web/local/modules";

spl_autoload_register(function ($class) use ($siteModulesFolder) {
    $arClass = explode('\\', strtolower(trim($class, '\\ ')));
    if (isset($arClass[0]) && $arClass[0] === 'sbnpf') {
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
