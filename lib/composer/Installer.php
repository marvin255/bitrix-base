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
		$host = self::askValid(
			'Введите хост для тестовой площадки',
			[
				['preg' => true, 'message' => 'Хост не указан'],
				[
					'preg' => '/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})|([0-9a-z\-\.]+\.[a-z]+)(\:\d+)?$/',
					'message' => 'Хост указан в неверном формате'
				],
				['preg' => '/^.+\:\d+$/', 'message' => 'Не указан порт'],
			],
			$io
		);

		//получаем данные об имени пользователя для тестовой площадки
		$username = self::askValid(
			'Введите имя пользователя для тестовой площадки',
			[
				['preg' => true, 'message' => 'Имя пользователя не указано'],
			],
			$io
		);

		//получаем данные о пароле для пользователя для тестовой площадки
		$password = self::askValid(
			'Введите пароль для тестовой площадки',
			[
				['preg' => true, 'message' => 'Пароль не указан'],
			],
			$io
		);

		//получаем ссылку на репозиторий
		$git = self::askValid(
			'Введите ссылку на репозиторий',
			[
				['preg' => true, 'message' => 'Ссылка на репозиторий не указана'],
				['preg' => '/^ssh\:\/\/.+$/', 'message' => 'Не указан ssh протокол'],
				['preg' => '/^ssh\:\/\/[^\/]+\:\d+\/.+$/', 'message' => 'Не указан порт'],
				['preg' => '/^ssh\:\/\/[^@\/]+@[^@\/]+\:\d+\/.+$/', 'message' => 'Не указан пользователь'],
			],
			$io
		);

		//настраиваем конфиг рокетира
		$configFile = self::getRootPath() . '/.rocketeer/config.php';
		$config = file_get_contents($configFile);
		//хост
		$config = preg_replace(
			'/(\'host\'\s*=>\s*\')[^\']*(\',)/',
			'${1}' . addslashes($host) . '${2}',
			$config
		);
		//имя пользователя
		$config = preg_replace(
			'/(\'username\'\s*=>\s*\')[^\']*(\',)/',
			'${1}' . addslashes($username) . '${2}',
			$config
		);
		//пароль
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

	/**
	 * Запрашиваем параметр с проверками
	 * @param string $message
	 * @param array $checks
	 * @param mixed $io
	 * @return string
	 */
	protected static function askValid($message, array $checks, $io)
	{
		$return = null;
		while (true) {
			$error = null;
			if ($return !== null) {
				foreach ($checks as $check) {
					if (
						$check['preg'] === true && empty($return)
						|| ($check['preg'] !== true && !preg_match($check['preg'], $return))
					){
						$error = $check['message'];
						break;
					}
				}
				if ($error === null) break;
			}
			if (!empty($error)) {
				$returnNew = trim($io->ask(
					self::textToConsole("{$error} (введите заново или оставьте пустую строку, чтобы сохранить вариант '{$return}'):\r\n")
				));
				if ($returnNew !== '') $return = $returnNew;
				else break;
			} else {
				$return = trim($io->ask(self::textToConsole("{$message}:\r\n")));
			}
		}
		return $return;
	}

	/**
	 * Проблема русских букв
	 * @param string $message
	 * @return string
	 */
	protected static function textToConsole($message)
	{
		if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
			return iconv('UTF-8', 'CP1251', $message);
		} else {
			return $message;
		}
	}
}
