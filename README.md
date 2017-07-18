# komtet-kassa-php-sdk

Библиотека для интеграции вашего сайта с облачным сервисом распределенной печати чеков [КОМТЕТ Касса](http://kassa.komtet.ru)

[![Travis](https://img.shields.io/travis/Komtet/komtet-kassa-php-sdk.svg?style=flat-square)](https://travis-ci.org/Komtet/komtet-kassa-php-sdk)

## Требования

* PHP >= 5.4
* CURL

## Установка

С помощью Composer:

```
composer require komtet/kassa-sdk
```

Вручную:

```
git clone https://github.com/Komtet/kassa-php-sdk
```

```php
<?php

require __DIR__.'/kassa-php-sdk/autoload.php';
```

## Использование

Первым делом необходимо создать менеджер очередей:

```php
<?php

use Komtet\KassaSdk\Client;
use Komtet\KassaSdk\QueueManager;

$key = 'идентификатор магазина';
$secret = 'секретный ключ';
$client = new Client($key, $secret);
$manager = new QueueManager($client);
```

После чего зарегистрировать очереди:

```php
// Где 'queue-name-1' и 'queue-name-2' произвольные псевдомимы для обращения к очереди, а
// 'queue-id-1' и 'queue-id-2' id очередей созданных в личном кабинете
$manager->registerQueue('queue-name-1', 'queue-id-1');
$manager->registerQueue('queue-name-2', 'queue-id-2');

```

Отправка чека на печать:

```php
<?php

use Komtet\KassaSdk\Exception\SdkException;

// уникальный ID, предоставляемый магазином
$checkID = 'id';
// E-Mail клиента, на который будет отправлен E-Mail с чеком.
$clientEmail = 'user@host';

$check = Check::createSellReturn($checkID, $clientEmail);
// Говорим, что чек нужно распечатать
$check->setShouldPrint(true);

$vat = new Vat(0, Vat::TYPE_NO);

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

## Changelog

# 0.2.0 (12.07.2017)

- Добавлена возможность указать систему налогообложения.
- Удалены все упоминания о Motmom.

# 0.1.0 (30.06.2017)

- Первый релиз.
