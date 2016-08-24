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
	}

	/**
	 * Возвращает путь до корневой папки проекта
	 * @return string
	 */
	public static function getRootPath()
	{
		return dirname(dirname(__DIR__));
	}



	/**
	 * Событие для интерактивной настройки проекта
	 */
	public static function configureProject($event)
	{
		$io = $event->getIO();

		//получаем данные о хосте для тестовой площадки
		$host = null;
		$error = null;
		while (true) {
			if ($host !== null) {
				if (empty($host)) {
					$error = 'Хост не указан';
				} elseif (
					!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,03}(\:\d+)?$/', $host)
					&& !preg_match('/^[0-9a-z\-\.]+\.[a-z]+(\:\d+)?$/i', $host)
				){
					$error = 'Хост указан в неверном формате';
				} elseif (
					!preg_match('/^.+\:\d+$/', $host)
				){
					$error = 'Не указан порт';
				} else {
					break;
				}
			}
			if (!empty($error)) {
				$hostNew = trim($io->ask("{$error} (введите заново или оставьте пустую строку, чтобы сохранить вариант '{$host}'):\r\n"));
				if ($hostNew !== '') $host = $hostNew;
				else break;
			} else {
				$host = trim($io->ask("Введите хост для тестовой площадки:\r\n"));
			}
		}

		//получаем данные об имени пользователя для тестовой площадки
		$username = null;
		$error = null;
		while (true) {
			if ($username !== null) {
				if (empty($username)) {
					$error = 'Имя пользователя не указано';
				} else {
					break;
				}
			}
			if (!empty($error)) {
				$usernameNew = trim($io->ask("{$error} (введите заново или оставьте пустую строку, чтобы сохранить вариант '{$username}'):\r\n"));
				if ($usernameNew !== '') $username = $usernameNew;
				else break;
			} else {
				$username = trim($io->ask("Введите имя пользователя для тестовой площадки:\r\n"));
			}
		}

		//получаем данные о пароле для пользователя для тестовой площадки
		$password = null;
		$error = null;
		while (true) {
			if ($password !== null) {
				if (empty($password)) {
					$error = 'Пароль не указан';
				} else {
					break;
				}
			}
			if (!empty($error)) {
				$passwordNew = trim($io->ask("{$error} (введите заново или оставьте пустую строку, чтобы сохранить вариант '{$password}'):\r\n"));
				if ($passwordNew !== '') $password = $passwordNew;
				else break;
			} else {
				$password = trim($io->ask("Введите пароль для тестовой площадки:\r\n"));
			}
		}

		//получаем ссылку на репозиторий
		$git = null;
		$error = null;
		while (true) {
			if ($git !== null) {
				if (empty($git)) {
					$error = 'Ссылка на репозиторий не указана';
				} elseif (
					!preg_match('/^ssh\:\/\/.+$/', $git)
				){
					$error = 'Не указан ssh протокол';
				} elseif (
					!preg_match('/^ssh\:\/\/[^\/]+\:\d+\/.+$/', $git)
				){
					$error = 'Не указан порт';
				} elseif (
					!preg_match('/^ssh\:\/\/[^@\/]+@[^@\/]+\:\d+\/.+$/', $git)
				){
					$error = 'Не указан пользователь';
				} else {
					break;
				}
			}
			if (!empty($error)) {
				$gitNew = trim($io->ask("{$error} (введите заново или оставьте пустую строку, чтобы сохранить вариант '{$git}'):\r\n"));
				if ($gitNew !== '') $git = $gitNew;
				else break;
			} else {
				$git = trim($io->ask("Введите ссылку на репозиторий:\r\n"));
			}
		}

		//настраиваем конфиг рокетира
		$configFile = self::getRootPath() . '/.rocketeer/config.php';
		$config = file_get_contents($configFile);
		$config = preg_replace(
			'/(\'host\'\s*=>\s*\')[^\']*(\',)/',
			'${1}' . addslashes($host) . '${2}',
			$config
		);
		$config = preg_replace(
			'/(\'username\'\s*=>\s*\')[^\']*(\',)/',
			'${1}' . addslashes($username) . '${2}',
			$config
		);
		$config = preg_replace(
			'/(\'password\'\s*=>\s*\')[^\']*(\',)/',
			'${1}' . addslashes($password) . '${2}',
			$config
		);
		file_put_contents($configFile, $config);


		//настраиваем ссылку на репозиторий
		$scmFile = self::getRootPath() . '/.rocketeer/scm.php';
		$config = file_get_contents($scmFile);
		$config = preg_replace(
			'/(\'repository\'\s*=>\s*\')[^\']*(\',)/',
			'${1}' . addslashes($git) . '${2}',
			$config
		);
		file_put_contents($scmFile, $config);
	}
}
