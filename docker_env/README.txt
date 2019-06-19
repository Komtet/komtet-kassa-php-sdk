Запуск тестов:
1. docker-compose up -d
2. docker-compose exec php_sdk /bin/bash
3. cd /home/php_sdk
4. ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/QueueManagerTest
