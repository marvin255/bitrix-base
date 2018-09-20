<?php

namespace app\composer;

use marvin255\bxcodegen;
use Composer\IO\IOInterface;
use InvalidArgumentException;

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
        $options = [];

        //название приложения для создания главного модуля
        $options['application_name'] = $event->getIO()->askAndValidate(
            "Enter project name (only latin symbols and digits allowed):\r\n",
            function ($value) {
                if (!preg_match('/^[a-z0-9]+$/i', $value)) {
                    throw new InvalidArgumentException(
                        'Only latin symbols and digits are allowed'
                    );
                }

                return $value;
            }
        );

        //настройки rocketeer
        $options['rocketeer'] = $event->getIO()->askConfirmation(
            "Ignite rocketeer (yes or no):\r\n"
        );
        if ($options['rocketeer']) {
            $options['repository'] = $event->getIO()->ask(
                "Enter repository url for rocketeer:\r\n"
            );
            $options['username'] = $event->getIO()->ask(
                "Enter username for rocketeer:\r\n"
            );
            $options['password'] = $event->getIO()->ask(
                "Enter password for rocketeer:\r\n"
            );
            $options['root_directory'] = $event->getIO()->ask(
                "Enter root directory for rocketeer:\r\n"
            );
        }

        self::createMainModule($options, $event->getIO());
        self::createRocketeerConfig($options, $event->getIO());
    }

    /**
     * Создает главный модуль сайта.
     *
     * @param array                   $options
     * @param Composer\IO\IOInterface $io
     */
    protected static function createMainModule(array $options, IOInterface $io)
    {
        $options = new bxcodegen\service\options\Collection([
            'name' => "{$options['application_name']}.main",
        ]);

        (bxcodegen\Factory::createDefault(self::getRootPath()))->run('module', $options);
    }

    /**
     * Создает конфиг для rocketeer.
     *
     * @param array                   $options
     * @param Composer\IO\IOInterface $io
     */
    protected static function createRocketeerConfig(array $options, IOInterface $io)
    {
        if ($options['rocketeer']) {
            $options = new bxcodegen\service\options\Collection([
                'application_name' => $options['application_name'],
                'repository' => $options['repository'],
                'username' => $options['username'],
                'password' => $options['password'],
                'root_directory' => $options['root_directory'],
                'gitignore_inject' => true,
                'phar_inject' => true,
            ]);
            $codegen = bxcodegen\Factory::createDefault(self::getRootPath());
            $codegen->run('module', $options);
            $io->write([
                'Current rockteer config requires marvin255/bxrocketeer.',
                'Please run following command before using:',
                'composer require marvin255/bxrocketeer:dev-master',
            ]);
        }
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
