<?php
require __DIR__.'/../../autoload.php';

use Komtet\KassaSdk\v2\AdditionalUserProps;
use Komtet\KassaSdk\v2\Agent;
use Komtet\KassaSdk\v2\Client;
use Komtet\KassaSdk\v2\MarkCode;
use Komtet\KassaSdk\v2\MarkQuantity;
use Komtet\KassaSdk\v2\OperatingCheckProps;
use Komtet\KassaSdk\v2\Order;
use Komtet\KassaSdk\v2\OrderBuyer;
use Komtet\KassaSdk\v2\OrderCompany;
use Komtet\KassaSdk\v2\OrderPosition;
use Komtet\KassaSdk\v2\OrderManager;
use Komtet\KassaSdk\v2\SectoralCheckProps;
use Komtet\KassaSdk\v2\SectoralItemProps;
use Komtet\KassaSdk\v2\TaxSystem;
use Komtet\KassaSdk\Exception\SdkException;
use Komtet\KassaSdk\Exception\ApiValidationException;


$key = 'YOUR_SHOP_ID'; // идентификатор магазина
$secret = 'YOUR_SHOP_SECRET'; // секретный ключ
// PSR-совместимый логгер (опциональный параметр)
$logger = null;
$client = new Client($key, $secret, $logger);
$orderManager = new OrderManager($client);

$order = new Order('123456', 'new', true);

// Информация о компании
$orderCompany = new OrderCompany(TaxSystem::COMMON, 'Улица Московская д.4');
$orderCompany->setPlaceAddress('г. Москва'); // Адрес расчетов
$orderCompany->setINN('502906602876'); // ИНН организации
$order->setCompany($orderCompany);

// Дата и время доставки
$order->setDeliveryTime('20.02.2022 14:00',
                        '20.02.2022 15:20');

// Информация о покупателе
$orderBuyer = new OrderBuyer('+87654443322', 
                             'г.Пенза, ул.Суворова д.10 кв.25',
                             'Сергеев Виктор Сергеевич',
                             '502906602876',
                             'client@email.com');

// Координаты доставки заказа
$orderBuyer->setCoordinate('123', '780');

$order->setOrderBuyer($orderBuyer);

// Идентификатор курьера
$order->setCourierId(5);

// Адрес для получения отчёта по заказу
$order->setCallbackUrl('https://test.ru/callback-url');

// Комментарий к заказу
$order->setDescription('Комментарий к заказу');

// Дополнительный реквизит пользователя 
$additional_user_props = new AdditionalUserProps('name', 'value');
$order->setAdditionalUserProps($additional_user_props);

// Отраслевой реквезит чека 
$sectoral_check_props = new SectoralCheckProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
$order->setSectoralCheckProps($sectoral_check_props);

// Возможность добавления нескольких SectoralCheckProps
$sectoral_check_props2 = new SectoralCheckProps('002', '01.01.2020', '2', 'значение отраслевого реквизита2');
$order->setSectoralCheckProps($sectoral_check_props2);

// Операционный реквизит пользователя 
$operating_check_props = new OperatingCheckProps('0', 'данные операции', '12.03.2020 16:55:25');
$order->setOperatingCheckProps($operating_check_props);

// Дополнительный реквизит чека 
$order->setAdditionalCheckProps('доп. реквизит чека');

// Позиции заказа
$orderPosition = new OrderPosition(['order_item_id' => '1',
                                    'name' => 'position name1',
                                    'price' => 555.0,
                                    'quantity' => 1,
                                    'total' => 555.0,
                                    'type' => 'service',
                                    'vat' => '20',
                                    'measure' => 0,
                                    'excise' => 5,
                                    'country_code' => '6',
                                    'declaration_number' => '7',
                                    'user_data' => 'доп. реквизит',
                                    'is_need_mark_code' => false,
                                    ]);

// Агент по предмету расчета 
$agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "502906602876");
$orderPosition->setAgent($agent);

// Отраслевой реквизит предмета расчета
$sectoral_item_props = new SectoralItemProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
$orderPosition->setSectoralItemProps($sectoral_item_props);

// Возможность добавления нескольких SectoralItemProps
$sectoral_item_props2 = new SectoralItemProps('002', '26.01.1935', '15', 'значение отраслевого реквизита2');
$orderPosition->setSectoralItemProps($sectoral_item_props2);

// Дробное количество маркированного товара
$mark_quantity = new MarkQuantity(1, 2);
$orderPosition->setMarkQuantity($mark_quantity);

// Атрибуты кода товара (маркировки)
$mark_code = new MarkCode(MarkCode::GS1M, '0123455g54drgdfsgre54st5ergdfg');
$orderPosition->setMarkCode($mark_code);

$order->addPosition($orderPosition);

$order->applyDiscount(100);

try {
    $orderManager->createOrder($order);
} catch (ApiValidationException $e) {
    echo $e->getMessage(), "\n";
    echo $e->getVLDCode(), "\n";
    echo $e->getDescription(), "\n";
} catch (SdkException $e) {
    echo $e->getMessage();
}
