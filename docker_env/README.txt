Запуск тестов:
1. docker-compose up -d
2. docker-compose exec php_sdk /bin/bash
3. cd /home/php_sdk
4. ./vendor/bin/phpunit tests/QueueManagerTest
Если папка vendor не создалась в проекте, то выполнить повторно команду установки composer 
внутри контейнера
