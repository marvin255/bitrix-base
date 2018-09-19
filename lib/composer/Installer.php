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
            self::textToConsole("Укажите название проекта латиницей:\r\n"),
            function ($value) {
                if (!preg_match('/^[a-z0-9]+$/i', $value)) {
                    throw new InvalidArgumentException(
                        self::textToConsole('Укажите название проекта латиницей')
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

    /**
     * Проблема русских букв.
     *
     * @param string $message
     *
     * @return string
     */
    protected static function textToConsole($message)
    {
        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            return self::translit($message);
        }

        return $message;
    }

    /**
     * Транслитератор
     *
     * @param string $str
     *
     * @return string
     */
    protected static function translit($str)
    {
        $rus = ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'];
        $lat = ['A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya'];

        return str_replace($rus, $lat, $str);
    }
}
