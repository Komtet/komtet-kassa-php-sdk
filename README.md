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
git clone https://github.com/Komtet/komtet-kassa-php-sdk
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
// PSR-совместимый логгер (опциональный параметр)
$logger = null;
$client = new Client($key, $secret, $logger);
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
use Komtet\KassaSdk\Agent;
use Komtet\KassaSdk\Check;
use Komtet\KassaSdk\Cashier;
use Komtet\KassaSdk\Payment;
use Komtet\KassaSdk\Position;
use Komtet\KassaSdk\TaxSystem;
use Komtet\KassaSdk\Vat;
use Komtet\KassaSdk\Exception\SdkException;

// уникальный ID, предоставляемый магазином
$checkID = 'id';
// E-Mail клиента, на который будет отправлен E-Mail с чеком.
$clientEmail = 'user@host';

$check = Check::createSell($checkID, $clientEmail, TaxSystem::COMMON); // или Check::createSellReturn для оформления возврата
// Говорим, что чек нужно распечатать
$check->setShouldPrint(true);

$vat = new Vat(Vat::RATE_18);

// Позиция в чеке: имя, цена, кол-во, общая стоимость, скидка, налог
$position = new Position('name', 100, 1, 100, 0, $vat);

// Опционально можно установить:
// Идентификатор позиции
// $position->setId('123');

// Единицу измерения
// $position->setMeasureName('Кг.');

// Cпособ рассчета
// $position->setCalculationMethod(CalculationMethod::FULL_PAYMENT);

// Признак рассчета
// $position->setCalculationSubject(CalculationSubject::PRODUCT);

// Агента по предмету расчета
// $agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "12345678901");
// $position->setAgent($agent);

$check->addPosition($position);

// Итоговая сумма расчёта
$payment = new Payment(Payment::TYPE_CARD, 100);
$check->addPayment($payment);

// Добавление кассира (опционально)
$cashier = new Cashier('Иваров И.П.', '1234567890123');
$check->addCashier($cashier);

// Добавляем чек в очередь.
try {
    $manager->putCheck($check, 'queue-name-1');
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Отправка чека коррекции:

```php
<?php
use Komtet\KassaSdk\Correction;
use Komtet\KassaSdk\CorrectionCheck;
use Komtet\KassaSdk\AuthorisedPerson;

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
    new Payment(Payment::TYPE_CARD, 4815), // Общая сумма по чеку
    new Vat('118') // Ставка налога
);

$authorised_person = new AuthorisedPerson(
  'Иваров И.И.', // ФИО
  '123456789012' // ИНН
);
$check->setAuthorisedPerson($authorised_person);

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

Получить информацию о поставленной на фискализацию задаче:

```php
<?php
$taskManager = new TaskManager($client);
try {
    $taskManager->getTaskInfo('task-id');
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

## Changelog

### 1.1.0 (27.11.2018)

- Добавлен метод применения скидки к чеку Check::applyDiscount;

### 1.0.0 (01.09.2018)

- Убрыны методы Payment::createCard и Payment::createCash из-за расширения списка возможных видов оплаты. Теперь объект платежа необходимо создавать явно `new Payment(Payment::TYPE_CARD, 100)`;

### 0.9.1 (01.09.2018)

- Вернул методы createCard и createCash для подержания совместимости версии 0.X.X

### 0.9.0 (15.08.2018)

- Добавлены константы направлений платежа `INTENT_BUY` и `INTENT_BUY_RETURN` в класс `Check`

### 0.8.0 (09.04.2018)

- Добавлен класс `AuthorisedPerson`

### 0.7.0 (02.04.2018)

- Добавлены классы `Agent`, `CalculationMethod`, `CalculationSubject` и `Cashier`.
- Добавлены методы `Check::addCashier`, `Position::setCalculationMethod`, `Position::setCalculationSubject`, `Position::setAgent`.
- Добавлены константы `Payment::TYPE_PREPAYMENT`, `Payment::TYPE_CREDIT` и `Payment::TYPE_COUNTER_PROVISIONING`.
- Удалены методы `Payment::createCard` и `Payment::createCash`

### 0.6.0 (28.11.2017)

- Добавлен метод `Client::setPartner`.

### 0.5.2 (01.11.2017)

- Маскирование значений заголовков Authorization и X-HMAC-Signature в логах.

### 0.5.1 (31.10.2017)

- Логирование с помощью PSR-совместимого логера.

### 0.5.0 (27.10.2017)

- Добавлен класс `TaskManager`.
- Добавлен метод `Position::setId`.

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
