<?php

$cleanRockteerData = function ($task) {
    $task->command->info('Remove rocketeer info files');
    $task->runForCurrentRelease('rm -Rf .rocketeer');
    $task->runForCurrentRelease('rm -Rf rockteer.phar');
};

$cleanCache = function ($task) {
    $task->command->info('Clean cache');
    $task->runForCurrentRelease('rm -Rf web/bitrix/cache/*');
    $task->runForCurrentRelease('rm -Rf web/bitrix/managed_cache/*');
};

$setComposerAsExecutable = function ($task) {
    $task->command->info('Make composer.phar executable');
    $task->runForCurrentRelease('chmod 0770 composer.phar');
};

$addSshToKnownHosts = function ($task) {
    $repo = $task->rocketeer->getOption('scm.repository');
    if (!$repo) return null;
    $arRepo = parse_url($repo);
    if (empty($arRepo['scheme']) || $arRepo['scheme'] !== 'ssh') return null;
    $hostname = $arRepo['host'] . (!empty($arRepo['port']) ? ":{$arRepo['port']}" : '');
    $removeCommand = "ssh-keygen -R {$hostname}";
    $scanCommand = "ssh-keyscan -H {$hostname} >> ~/.ssh/known_hosts";
    $task->runForCurrentRelease($removeCommand);
    $task->runForCurrentRelease($scanCommand);
};

return [

    // Tasks
    //
    // Here you can define in the `before` and `after` array, Tasks to execute
    // before or after the core Rocketeer Tasks. You can either put a simple command,
    // a closure which receives a $task object, or the name of a class extending
    // the Rocketeer\Abstracts\AbstractTask class
    //
    // In the `custom` array you can list custom Tasks classes to be added
    // to Rocketeer. Those will then be available in the command line
    // with all the other tasks
    //////////////////////////////////////////////////////////////////////

    // Tasks to execute before the core Rocketeer Tasks
    'before' => [
        'setup'   => [],
        'deploy'  => [],
        'cleanup' => [],
        'dependencies' => [
            $setComposerAsExecutable,
        ],
    ],

    // Tasks to execute after the core Rocketeer Tasks
    'after'  => [
        'setup'   => [
            $addSshToKnownHosts,
        ],
        'deploy'  => [
            $cleanRockteerData,
        ],
        'cleanup' => [],
        'update'  => [
            $cleanRockteerData,
            $cleanCache,
        ],
    ],

    // Custom Tasks to register with Rocketeer
    'custom' => [],

];
