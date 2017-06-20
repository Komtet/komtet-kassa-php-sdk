# komtet-kassa-php-sdk

Библиотека для интеграции вашего сайта с облачным сервисом распределенной печати чеков [OnlineКасса](http://kassa.komtet.ru)

[![Travis](https://img.shields.io/travis/Motmom/komtet-kassa-php-sdk.svg?style=flat-square)](https://travis-ci.org/Motmom/komtet-kassa-php-sdk)

## Требования

* PHP >= 5.4
* CURL

## Установка

С помощью Composer:

```
composer require motmom/komtet-kassa-sdk
```

Вручную:

```
git clone https://github.com/Motmom/komtet-kassa-php-sdk
```

```php
<?php

require __DIR__.'/komtet-kassa-php-sdk/autoload.php';
```

## Использование

Первым делом необходимо создать менеджер очередей:

```php
<?php

use Motmom\KomtetKassaSdk\Client;
use Motmom\KomtetKassaSdk\QueueManager;

$key = 'идентификатор магазина';
$secret = 'секретный ключ';
$client = new Client($key, $secret);
$manager = new QueueManager($client);
```

После чего зарегистрировать очереди:

```php
$manager->registerQueue('queue-name-1', 'queue-id-1');
$manager->registerQueue('queue-name-2', 'queue-id-2');
```

Отправка чека на печать:

```php
<?php

use Motmom\KomtetKassaSdk\Exception\SdkException;

// уникальный ID, предоставляемый магазином
$checkID = 'id';
// E-Mail клиента, на который будет отправлен E-Mail с чеком.
$clientEmail = 'user@host';

$check = Check::createSellReturn($checkID, $clientEmail);
// Говорим, что чек нужно распечатать
$check->setShouldPrint(true);

$vat = Vat::createUnit(0, Vat::TYPE_NO);

// Позиция в чеке: имя, цена, кол-во, общая стоимость, скидка, налог
$position = new Position('name', 100, 1, 100, 0, $vat);
$check->addPosition($position);

// Итоговая сумма расчёта
$payment = Payment::createCash(100);
$check->addPayment($payment);


// Добавляем чек в очередь.
try {
    $manager->putCheck($check, 'queue-name-1');
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Чтобы не указывать каждый раз имя очереди, установите очередь по умолчанию:

```php
<?php

$manager->setDefaultQueue('queue-name-1');
$manager->putCheck($check);
```


Получить состояние очереди:

```php
<?php
$manager->isQueueActive('queue-name-1');
```
