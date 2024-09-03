# komtet-kassa-php-sdk

Библиотека для интеграции вашего сайта с облачным сервисом распределенной печати чеков [КОМТЕТ Касса](http://kassa.komtet.ru)

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

Для отправки примеров из examples из php-cli:

```
make build
make cli_php_7 или make cli_php_8
php -f examples/v1/send_check_example.php
```

# Использование v1

Первым делом необходимо создать менеджер очередей:
```php
<?php

use Komtet\KassaSdk\v1\Client;
use Komtet\KassaSdk\v1\QueueManager;

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

# Чек

## Отправка чека на печать - [Пример](https://github.com/Komtet/komtet-kassa-php-sdk/tree/master/examples/v1/send_check_example.php)

## Отправка чека коррекции на печать - [Пример](https://github.com/Komtet/komtet-kassa-php-sdk/tree/master/examples/v1/send_correction_check_example.php)
---

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

# Заказ
## Создание заказа на доставку - [Пример](https://github.com/Komtet/komtet-kassa-php-sdk/tree/master/examples/v1/send_order_example.php)

Обновление заказа на доставку:

```php
<?php

$orderManager = new OrderManager($client);
$order_id = 1;

$order = new Order('123', TaxSystem::COMMON, 'new', 0);
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

$orderManager = new OrderManager($client);
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

$orderManager = new OrderManager($client);
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

$orderManager = new OrderManager($client);

try {
    $orderList = $orderManager->getOrders();
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Получить список сотрудников:

```php
<?php

use Komtet\KassaSdk\EmployeeManager;
use Komtet\KassaSdk\EmployeeType;

$employeeManager = new EmployeeManager(client);

try {
    $employeeList = $employeeManager->getEmployees(EmployeeType::COURIER);
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Получить информацию по сотруднику:

```php
<?php

$employeeManager = new EmployeeManager(client);
$employeeID = 1;

try {
    $employee = $employeeManager->getEmployee($employeeID);
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Создание сотрудника:

```php
<?php

$employeeManager = new EmployeeManager(client);
$employee = new Employee(EmployeeType::CASHIER, 'Full Name', 
                         'login_employee', 'password', 'POS_KEY');
$employee->setPaymentAddress('payment address');
$employee->setAccessSettings(true, false, none);

try {
    $employeeManager->createEmployee($employee);
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Обновление сотрудника:

```php
<?php

$employeeManager = new EmployeeManager(client);
$employee = new Employee(EmployeeType::CASHIER, 'Full Name', 
                         'login_employee', 'new_password', 'POS_KEY');
$employee->setPaymentAddress('new payment address');
$employee->setAccessSettings(true, true, true);

$employeeID = 1;

try {
    $employeeManager->updateEmployee($employeeID, $employee);
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

Удаление сотрудника:

```php
<?php

$employeeManager = new EmployeeManager(client);
$employeeID = 1;

try {
    $employeeManager->deleteEmployee($employeeID);
} catch (SdkException $e) {
    echo $e->getMessage();
}
```


# Использование v2
Первым делом необходимо создать менеджер очередей:
```php
<?php

use Komtet\KassaSdk\v2\Client;
use Komtet\KassaSdk\v2\QueueManager;

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

---

# Чек

## Отправка чека на печать - [Пример](https://github.com/Komtet/komtet-kassa-php-sdk/tree/master/examples/v2/send_check_example.php)

## Отправка чека коррекции на печать - [Пример](https://github.com/Komtet/komtet-kassa-php-sdk/tree/master/examples/v2/send_correction_check_example.php)
---

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

---

# Заказ
## Создание заказа на доставку - [Пример](https://github.com/Komtet/komtet-kassa-php-sdk/tree/master/examples/v2/send_order_example.php)

## Обновление заказа на доставку:

```php
<?php

$orderManager = new OrderManager($client);
$order_id = 1;

$order = new Order('12345', 'new', true);

$orderCompany = new OrderCompany(TaxSystem::COMMON, 'Улица Московская д.4');
$order->setCompany($orderCompany);

$orderBuyer = new OrderBuyer('+87654443322', 
                             'г.Пенза, ул.Суворова д.10 кв.25')
$order->setOrderBuyer($orderBuyer);

$order->setDeliveryTime('20.02.2022 14:00',
                        '20.02.2022 15:20');

$orderPosition = new OrderPosition(['name' => 'position name1',
                                    'price' => 555.0,
                                    'quantity' => 1,
                                    'total' => 555.0,
                                    'vat' => '20',
                                    ]);
$order->addPosition($orderPosition);

try {
    $orderManager->updateOrder($order_id, $order);
} catch (ApiValidationException $e) {
    echo $e->getMessage();
    echo $e->getVLDCode();
    echo $e->getDescription();
} catch (SdkException $e) {
    echo $e->getMessage();
}
```

## Следующие операции в API v2 идентичны по вызову с API v1(примеры представлены выше в описании API v1):
- Информация о заказе
- Применить общую скидку на заказ
- Удалить заказ
- Получить список заказов
- Получить список сотрудников
- Получить информацию по сотруднику
- Создание сотрудника
- Обновление сотрудника
- Удаление сотрудника

