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

$addGitToKnownHosts = function ($task) {
    $repo = $task->rocketeer->getOption('scm.repository');
    if (!$repo) return null;
    $arRepo = parse_url($repo);
    if (empty($arRepo['scheme']) || $arRepo['scheme'] !== 'ssh') return null;
    $hostname = $arRepo['host'];
    $task->runForCurrentRelease([
        "ssh-keygen -R {$hostname}",
        "ssh-keyscan -t rsa" . (!empty($arRepo['port']) ? " -p{$arRepo['port']}" : '') . " -H {$hostname} >> ~/.ssh/known_hosts"
    ]);
};

$createOrShowSshKey = function ($task) {
    $isKeyExists = trim($task->runRaw('[ -f ~/.ssh/id_rsa.pub ] && echo 1')) === '1';
    if (!$isKeyExists) {
        $task->runRaw('ssh-keygen -t rsa -N "" -f ~/.ssh/id_rsa');
    }
    $key = $task->runRaw('cat ~/.ssh/id_rsa.pub');
    $task->command->info('Hosting ssh key is: ' . $key);
};

$autoCreateShared = function ($task) {
    $basePath = $task->paths->getHomeFolder() . '/shared';
    $shareds = $task->rocketeer->getOption('remote.shared');
    //folders
    foreach ($shareds as $object) {
        $arObject = pathinfo($object);
        if (!empty($arObject['extension'])) $object = $arObject['dirname'];
        $arPath = explode('/', trim($object, "/ \t\n\r\0\x0B"));
        $checkedPath = '';
        foreach ($arPath as $chain) {
            $fullPath = "{$basePath}{$checkedPath}/{$chain}";
            $isFolderExists = trim($task->runRaw('[ -d \'' . $fullPath . '\' ] && echo 1')) === '1';
            if (!$isFolderExists) {
                $task->run("mkdir '{$fullPath}'");
            }
            $checkedPath .= "/{$chain}";
        }
    }
    //files
    foreach ($shareds as $object) {
        $arObject = pathinfo($object);
        if (empty($arObject['extension'])) continue;
        $object = trim($object, "/ \t\n\r\0\x0B");
        $fullPath = "{$basePath}/{$object}";
        if (trim($task->runRaw('[ -f \'' . $fullPath . '\' ] && echo 1')) === '1') continue;
        $local = __DIR__ . '/../examples/' . $arObject['basename'];
        $task->run("touch '{$fullPath}'");
        if (file_exists($local)) {
            $content = file_get_contents($local);
            $task->runRaw('echo "' . addcslashes($content, '"') . '" > "'. $fullPath . '"');
        }
    }
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
            $autoCreateShared,
            $addGitToKnownHosts,
            $createOrShowSshKey,
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
