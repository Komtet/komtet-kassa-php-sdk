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
git clone git clone https://github.com/Komtet/komtet-kassa-php-sdk
```

```php
<?php

require __DIR__.'/komtet-kassa-php-sdk/autoload.php';
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
$manager->registerQueue('queue-name-1', 'queue-id-1');
$manager->registerQueue('queue-name-2', 'queue-id-2');
// 'queue-name-1' и 'queue-name-2' - произвольные псевдомимы для обращения к очередям.
// 'queue-id-1' и 'queue-id-2' - идентификаторы очередей, созданных в личном кабинете.

```

Отправка чека на печать:

```php
<?php
use Komtet\KassaSdk\Check;
use Komtet\KassaSdk\Payment;
use Komtet\KassaSdk\TaxSystem;
use Komtet\KassaSdk\Vat;
use Komtet\KassaSdk\Exception\ClientException;

// уникальный ID, предоставляемый магазином
$checkID = 'id';
// E-Mail клиента, на который будет отправлен E-Mail с чеком.
$clientEmail = 'user@host';

$check = Check::createSell($checkID, $clientEmail, TaxSystem::Common); // или Check::createSellReturn для оформления возврата
// Говорим, что чек нужно распечатать
$check->setShouldPrint(true);

$vat = new Vat(Vat::RATE_18);

// Позиция в чеке: имя, цена, кол-во, общая стоимость, скидка, налог
$position = new Position('name', 100, 1, 100, 0, $vat);
// Можно также установить единицу измерения:
// $position->setMeasureName('Кг.');
$check->addPosition($position);

// Итоговая сумма расчёта
$payment = Payment::createCard(100); // или createCash при оплате наличными
$check->addPayment($payment);


// Добавляем чек в очередь.
try {
    $manager->putCheck($check, 'queue-name-1');
} catch (ClientException $e) {
    echo $e->getMessage();
}
```

Отправка чека коррекции:

```php
<?php
use Komtet\KassaSdk\Correction;
use Komtet\KassaSdk\CorrectionCheck;

// Данные коррекции
// createSelf для самостоятельной коррекции
// createForced для коррекции по предписанию
$correction = Correction::createSelf(
    '2012-12-21', // Дата документа коррекции в формате yyyy-mm-dd
    '4815162342', // Номер документа коррекции
    'description' // Описание коррекции
);

// createSell для коррекции прихода
// createSellReturn для коррекции расхода
$check = CorrectionCheck::createSell(
    '4815162342', // Номер операции в вашей системе
    '4815162342', // Серийный номер принтера
    TaxSystem::COMMON, // Система налогообложения
    $correction // Данные коррекции
);

$check->setPayment(
    Payment::createCard(4815), // Общая сумма по чеку
    new Vat('118') // Ставка налога
);

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

### 0.4.0 (29.09.2017)

- Добавлен метод `Payment::getSum`.
- `Check::TS_*` константы перенесены в класс `TaxSystem`.
- Добавлен метод `Position::setMeasureName`.
- Добавлен чек коррекции.

### 0.3.0 (11.08.2017)

- Удалён метод `Vat::calculate`.
- Конструктор класса `Vat` теперь принимает только ставку налога.
- Метод `Vat::as_array()` заменён на `Vat::getRate`, который возвращает строку, содержащую ставку налога.

### 0.2.1 (18.07.2017)

- `QueueManager::putCheck()` теперь возвращает ответ от сервера.

### 0.2.0 (12.07.2017)

- Добавлена возможность указать систему налогообложения.
- Удалены все упоминания о Motmom.

### 0.1.0 (30.06.2017)

- Первый релиз.
