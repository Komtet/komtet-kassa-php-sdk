<?php
require __DIR__.'/../../autoload.php';

use Komtet\KassaSdk\v1\Agent;
use Komtet\KassaSdk\v1\Client;
use Komtet\KassaSdk\v1\Order;
use Komtet\KassaSdk\v1\OrderManager;
use Komtet\KassaSdk\v1\OrderPosition;
use Komtet\KassaSdk\v1\TaxSystem;
use Komtet\KassaSdk\v1\Vat;
use Komtet\KassaSdk\Exception\SdkException;
use Komtet\KassaSdk\Exception\ApiValidationException;


$key = 'YOUR_SHOP_ID'; // идентификатор магазина
$secret = 'YOUR_SHOP_SECRET'; // секретный ключ
// PSR-совместимый логгер (опциональный параметр)
$logger = null;
$client = new Client($key, $secret, $logger);
$orderManager = new OrderManager($client);

$order = new Order('1234567', TaxSystem::COMMON, 'new', false);

// Идентификатор курьера
$order->setCourierId(5);

// Адрес для получения отчёта по заказу
$order->setCallbackUrl('https://test.ru/callback-url');

// Комментарий к заказу
$order->setDescription('Комментарий к заказу');

// Информация о покупателе
$order->setClient('г.Пенза, ул.Суворова д.10 кв.25',
                  '+87654443322',
                  'client@email.com',
                  'Сергеев Виктор Сергеевич');

// Дата и время доставки
$order->setDeliveryTime('2018-02-28 14:00',
                        '2018-02-28 15:20');

// Позиции заказа

// Агент по предмету расчета 
$agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "502906602876");

$orderPosition = new OrderPosition(['oid' => '1',
                                    'name' => 'position name1',
                                    'price' => 555.0,
                                    'quantity' => 1,
                                    'type' => 'product',
                                    'vat' => Vat::RATE_NO,
                                    'agent' => $agent,
                                    'is_need_nomenclature_code' => true,
                                    'excise' => 4,
                                    'country_code' => 'country_code',
                                    'declaration_number' => 'declaration_number'
                                    ]);

// Код маркировки
$orderPosition->setNomenclatureCode('kjgldfjgdfklg234234');

$order->addPosition($orderPosition);

try {
    $orderManager->createOrder($order);
} catch (ApiValidationException $e) {
    echo $e->getMessage(), "\n";
    echo $e->getVLDCode(), "\n";
    echo $e->getDescription(), "\n";
} catch (SdkException $e) {
    echo $e->getMessage();
}