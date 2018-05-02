Тесты Codeception
=================

Ознакомьтесь с документацией на [codeception.com](https://codeception.com/docs/01-Introduction).

Предполагается, что сайт по мере разработки будет проходить авто-тесты. Рекомендуется, как минимум, реализовывать минимальный набор Acceptance-тестов для каждой страницы сайта.

Конфигурация для запуска Codeception - `codeception.yml`, пример Unit-теста – `tests/unit/mainModulesCest.php`, пример Acceptance-теста – `acceptance/MainPageCest.php`.

Конфигурация приложения
-----------------------

Использование `WebDriver` для запуска приемочных тестов обеспечивает запуск сайта целиком в браузере Google Chrome с размером экрана 1360x1280px. Меняйте (при необходимости) разрешение в файле настроек `tests/acceptance.suite.yml`. 
Конфигурация, поставляемая в этом проекте расчитана на запуск всех сервисов внутри docker-контейнеров. Если вы запускаете свой проект без контейнеров, (установите и сконфигурируйте)[http://codeception.com/docs/modules/WebDriver#Selenium] Selenium и измените строку `host: selenium` в файле настроек.

Даже если ваше приложение запускается вне docker-среды, вы можете использовать headless-selenium контейнер вместо нативного запуска selenium и ChromeDriver. Для этого вам нужно установить демон docker и запустить контейнер selenium:

```bash
docker run --net=host selenium/standalone-chrome 
```

Измените параметр конфигурации `host` на `127.0.0.1` и запускайте тесты.

Запуск тестов
--------------

Все примеры приведены для случая запуска из рабочей директории проекта.

Запуск всех существующих тестов 

```bash
vendor/bin/codecept run
```

Запуск acceptance-тестов

```bash
vendor/bin/codecept run acceptance
```

Запуск отдельного комплекта тестов (отдельного класса)

```bash
vendor/bin/codecept run acceptance MainPageCest
```

Запуск отдельного теста

```bash
vendor/bin/codecept run acceptance MainPageCest:tryToSeeBodyElement
```

