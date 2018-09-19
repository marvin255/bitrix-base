<?php

namespace app\composer;

use InvalidArgumentException;
use marvin255\bxcodegen;

/**
 * Установка базового приложения для битрикса.
 */
class Installer
{
    /**
     * Событие после создания проекта. Копируем свежие версии rocketeer, composer и bitrixsetup.php.
     *
     * @param $event
     *
     * @throws \RuntimeException
     */
    public static function postCreateProject($event)
    {
        //загружаем свежую версию rocketeer
        file_put_contents(
            self::getRootPath() . '/rocketeer.phar',
            fopen('http://rocketeer.autopergamene.eu/versions/rocketeer.phar', 'r')
        );
        //загружаем свежую версию bitrixsetup.php
        file_put_contents(
            self::getRootPath() . '/web/bitrixsetup.php',
            fopen('http://www.1c-bitrix.ru/download/scripts/bitrixsetup.php', 'r')
        );
        //загружаем свежую версию composer
        file_put_contents(
            self::getRootPath() . '/composer.phar',
            fopen('https://getcomposer.org/composer.phar', 'r')
        );
    }

    /**
     * Событие для интерактивной настройки проекта.
     *
     * @param $event
     */
    public static function configureProject($event)
    {
        $projectName = $event->getIO()->askAndValidate(
            "Enter project name in latin:\r\n",
            function ($value) {
                if (!preg_match('/^[a-z0-9]+$/i', $value)) {
                    throw new InvalidArgumentException(
                        'Only latin symbols and digits are allowed'
                    );
                }

                return $value;
            }
        );

        self::createMainModule($projectName, self::getRootPath());
    }

    /**
     * Создает главный модуль сайта по названию и пути.
     *
     * @param string $siteName
     * @param string $rootFolder
     */
    protected static function createMainModule($siteName, $rootFolder)
    {
        $options = new bxcodegen\service\options\Collection([
            'name' => "{$siteName}.main",
        ]);
        $pathManager = new bxcodegen\service\path\PathManager($rootFolder, [
            'modules' => '/web/local/modules',
        ]);
        $locator = new bxcodegen\ServiceLocator;
        $locator->set('pathManager', $pathManager);
        $locator->set('renderer', new bxcodegen\service\renderer\Twig);
        $locator->set('copier', new bxcodegen\service\filesystem\Copier);

        (new bxcodegen\generator\Module)->generate($options, $locator);
    }

    /**
     * Возвращает путь до корневой папки проекта.
     *
     * @return string
     */
    public static function getRootPath()
    {
        return dirname(dirname(__DIR__));
    }
}
