<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\Order;
use Komtet\KassaSdk\OrderPosition;
use Komtet\KassaSdk\Vat;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    public function testOrder(){

        $order = new Order('123', 'new', 0);
        $this->assertEquals($order->asArray()['order_id'], '123');
        $this->assertEquals($order->asArray()['state'], 'new');
        $this->assertEquals($order->asArray()['sno'], 0);

        $order->setClient('г.Пенза, ул.Суворова д.10 кв.25',
                          '+87654443322',
                          'client@email.com',
                          'Сергеев Виктор Сергеевич');

        $this->assertEquals($order->asArray()['client_name'], 'Сергеев Виктор Сергеевич');
        $this->assertEquals($order->asArray()['client_address'], 'г.Пенза, ул.Суворова д.10 кв.25');
        $this->assertEquals($order->asArray()['client_phone'], '+87654443322');
        $this->assertEquals($order->asArray()['client_email'], 'client@email.com');

        $order->setDeliveryTime('2018-02-28 14:00',
                                '2018-02-28 15:20');
        $this->assertEquals($order->asArray()['date_start'], '2018-02-28 14:00');
        $this->assertEquals($order->asArray()['date_end'], '2018-02-28 15:20');

        $order->setDescription('Комментарий к заказу');
        $this->assertEquals($order->asArray()['description'], 'Комментарий к заказу');

        $orderPosition = new OrderPosition(['oid' => '1',
                                            'name' => 'position name1',
                                            'price' => 555.0,
                                            'type' => 'product'
                                            ]);

        $order->addPosition($orderPosition);
        $this->assertEquals($order->asArray()['items'][0]['order_item_id'], '1');
        $this->assertEquals($order->asArray()['items'][0]['name'], 'position name1');
        $this->assertEquals($order->asArray()['items'][0]['vat'], Vat::RATE_NO);
        $this->assertEquals($order->asArray()['items'][0]['total'], 555.0);

        $orderPosition = new OrderPosition(['oid' => '2',
                                            'name' => 'position name2',
                                            'price' => 100.0,
                                            'type' => 'product',
                                            'quantity' => 5,
                                            'vat' => Vat::RATE_10,
                                            'measure_name' => 'kg'
                                            ]);

        $order->addPosition($orderPosition);
        $this->assertEquals($order->asArray()['items'][1]['order_item_id'], '2');
        $this->assertEquals($order->asArray()['items'][1]['name'], 'position name2');
        $this->assertEquals($order->asArray()['items'][1]['vat'], Vat::RATE_10);
        $this->assertEquals($order->asArray()['items'][1]['total'], 500.0);
        $this->assertEquals($order->asArray()['items'][1]['measure_name'], 'kg');

        $order->setCourierId(1);
        $this->assertEquals($order->asArray()['courier_id'], 1);

        $order->setСallbackUrl('https://calback_url.ru');
        $this->assertEquals($order->asArray()['callback_url'], 'https://calback_url.ru');

    }
}
