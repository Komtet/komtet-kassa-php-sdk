Запуск тестов:
1. make build
2. make cli_php_5 или make cli_php_7
2. ./vendor/bin/phpunit tests/v1/QueueManagerTest - для запуска конкретного теста
   ./vendor/bin/phpunit tests - для запуска всех тестов

Либо, можно запустить тесты не из контейнера с помощью команды: 
 - make test

Если папка vendor не создалась в проекте, то выполнить повторно команду установки composer 
внутри контейнера
