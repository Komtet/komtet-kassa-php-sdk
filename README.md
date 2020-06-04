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
use Komtet\KassaSdk\Buyer;
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

$vat = new Vat(Vat::RATE_20);

// Позиция в чеке: имя, цена, кол-во, общая стоимость, налог
$position = new Position('name', 100, 1, 100, $vat);

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

// Добавление данных покупателя (опционально)
$buyer = new Buyer('Пупкин П.П.', '123412341234');
$check->addBuyer($buyer);

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
    new Vat('120') // Ставка налога
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

Создание заказа на доставку:

```php
<?php
$orderManager = new OrderManager(client);

$order = new Order('123', 'new', 0);
$order->setClient('г.Пенза, ул.Суворова д.10 кв.25',
                  '+87654443322',
                  'client@email.com',
                  'Сергеев Виктор Сергеевич');
$order->setDeliveryTime('2018-02-28 14:00',
                        '2018-02-28 15:20');
$orderPosition = new OrderPosition(['oid' => '1',
                                    'name' => 'position name1',
                                    'price' => 555.0,
                                    'type' => 'product'
                                    ]);
$order->addPosition($orderPosition);

try {
    $orderManager->createOrder($order);
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Обновление заказа на доставку:

```php
<?php
$orderManager = new OrderManager(client);
$order_id = 1;

$order = new Order('123', 'new', 0);
$order->setClient('г.Пенза, ул.Суворова д.10 кв.25',
                  '+87654443322',
                  'client@email.com',
                  'Сергеев Виктор Сергеевич');
$order->setDeliveryTime('2018-02-28 14:00',
                        '2018-02-28 15:20');
$orderPosition = new OrderPosition(['oid' => '1',
                                    'name' => 'position name1',
                                    'price' => 555.0,
                                    'type' => 'product'
                                    ]);
$order->addPosition($orderPosition);

try {
    $orderManager->updateOrder($order_id, $order);
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Информация о заказе:

```php
<?php
$orderManager = new OrderManager(client);
$order_id = 1;

try {
  $info = $orderManager->getOrderInfo($order_id);
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Применить общую скидку на заказ:

```php
<?php
$discount = 250;
$order->applyDiscount($discount);
```

Удалить заказ:

```php
<?php
$orderManager = new OrderManager(client);
$order_id = 1;

try {
  $orderManager->deleteOrder($order_id);
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Получить список заказов:

```php
<?php
$orderManager = new OrderManager(client);
$order_id = 1;

try {
    $orderList = $orderManager->getOrders();
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Получить список курьеров:

```php
<?php
$courierManager = new CourierManager(client);

try {
    $courierList = $courierManager->getCouriers();
} catch (SdkException $e) {
    echo $e->getMessage();
}
```
