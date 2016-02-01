<?php

namespace app\composer;

/**
 * Установка базового приложения для битрикса
 */
class Installer
{
	/**
	 * Событие после создания проекта. Копируем свежие версии rocketeer, composer и bitrixsetup.php
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
		//делаем composer.phar исполняемым
		chmod(self::getRootPath() . '/composer.phar', 0770);
	}

	/**
	 * Возвращает путь до корневой папки проекта
	 * @return string
	 */
	public static function getRootPath()
	{
		return dirname(dirname(__DIR__));
	}
}