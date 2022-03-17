<?php
require __DIR__.'/autoload.php';

use Komtet\KassaSdk\v1\Client;
use Komtet\KassaSdk\v1\QueueManager;
use Komtet\KassaSdk\v1\Check;
use Komtet\KassaSdk\v1\Cashier;
use Komtet\KassaSdk\v1\Payment;
use Komtet\KassaSdk\v1\Position;
use Komtet\KassaSdk\v1\TaxSystem;
use Komtet\KassaSdk\v1\Vat;
use Komtet\KassaSdk\Exception\SdkException;
use Komtet\KassaSdk\Exception\ApiValidationException;

$key = 'YOUR_SHOP_ID';
$secret = 'YOUR_SHOP_SECRET';
// PSR-совместимый логгер (опциональный параметр)
$logger = null;
$client = new Client($key, $secret, $logger);
$manager = new QueueManager($client);
$manager->registerQueue('queue', 'YOUR_QUEUE_ID'); //int

// уникальный ID, предоставляемый магазином
$checkID = 'testdf1234';
// E-Mail клиента, на который будет отправлен E-Mail с чеком.
$clientEmail = 'user@host.com';

$check = Check::createSell($checkID, $clientEmail, TaxSystem::COMMON);

$vat = new Vat(Vat::RATE_20);

// Позиция в чеке: имя, цена, кол-во, общая стоимость, налог
$position = new Position('name', 100, 1, 100, $vat);

$check->addPosition($position);

// Итоговая сумма расчёта
$payment = new Payment(Payment::TYPE_CARD, 100);
$check->addPayment($payment);

// Добавление кассира (опционально)
$cashier = new Cashier('Иваров И.П.', '1234567890123');
$check->addCashier($cashier);

// Добавляем чек в очередь.
try {
    $manager->putCheck($check, 'queue');
} catch (ApiValidationException $e) {
    echo $e->getMessage();
    echo $e->getVLDCode();
    echo $e->getDescription();
} catch (SdkException $e) {
    echo $e->getMessage();
}
