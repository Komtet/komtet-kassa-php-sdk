<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\Order;
use Komtet\KassaSdk\v2\OrderBuyer;
use Komtet\KassaSdk\v2\OrderCompany;
use Komtet\KassaSdk\v2\OrderManager;
use Komtet\KassaSdk\v2\OrderPosition;
use Komtet\KassaSdk\v2\TaxSystem;
use PHPUnit\Framework\TestCase;

class OrderManagerTest extends TestCase
{
    private $client;
    private $om;
    private $order;

    protected function setUp(): void
    {
        $this->client = $this
            ->getMockBuilder('\Komtet\KassaSdk\v2\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->om = new OrderManager($this->client);

        $this->order = new Order('123', 'new', false);

        $this->order->setOrderBuyer(new OrderBuyer(
            '+87654443322', 
            'г.Пенза, ул.Суворова д.10 кв.25',
            'Сергеев Виктор Сергеевич',
            '502906602876',
            'client@email.com'));

        $this->order->setCompany(new OrderCompany(
            TaxSystem::COMMON,
            'Улица Московская д.4',
            'г. Москва',
            '502906602876'
        ));

        $this->order->setDeliveryTime('20.02.2022 14:00',
        '20.02.2022 15:20');

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
        $this->order->addPosition($orderPosition);
        $this->order->setCourierId(1);
        $this->order->setCallbackUrl('https://calback_url.ru');
    }

    public function testCreateOrderSucceded()
    {
        $path = 'api/shop/v2/orders';
        $rep = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($path)
            ->willReturn(['key' => 'val']);
        $this->assertEquals($this->om->createOrder($this->order), $rep);
    }

    public function testUpdateOrderSucceded()
    {
        $path = 'api/shop/v2/orders/1';
        $rep = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($path)
            ->willReturn(['key' => 'val']);
        $this->assertEquals($this->om->updateOrder(1,$this->order), $rep);
    }

    public function testGetOrderInfoSucceded()
    {
        $path = 'api/shop/v2/orders/1';
        $rep = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($path)
            ->willReturn(['key' => 'val']);
        $this->assertEquals($this->om->getOrderInfo(1), $rep);
    }


    public function testDeleteOrderInfoSucceded()
    {
        $path = 'api/shop/v2/orders/1';
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($path)
            ->willReturn(True);
        $this->assertEquals($this->om->deleteOrder(1), True);
    }

    public function testGetOrdersSucceded()
    {
        $path = 'api/shop/v2/orders?start=0&limit=10';
        $rep = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($path)
            ->willReturn(['key' => 'val']);
        $this->assertEquals($this->om->getOrders(), $rep);
    }

    public function testGetOrdersSucceded1()
    {
        $path = 'api/shop/v2/orders?start=0&limit=10&courier_id=1';
        $rep = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($path)
            ->willReturn(['key' => 'val']);
        $this->assertEquals($this->om->getOrders('0', '10', '1'), $rep);
    }

    public function testGetOrdersSucceded2()
    {
        $path = 'api/shop/v2/orders?start=0&limit=10&courier_id=1&date_start=2019-12-12';
        $rep = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($path)
            ->willReturn(['key' => 'val']);
        $this->assertEquals($this->om->getOrders('0', '10', '1', '2019-12-12'), $rep);
    }

}
