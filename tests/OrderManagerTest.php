<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\Order;
use Komtet\KassaSdk\OrderManager;
use Komtet\KassaSdk\OrderPosition;

class OrderManagerTest extends \PHPUnit_Framework_TestCase
{
  private $client;
  private $om;
  private $order;

  protected function setUp()
  {
      $this->client = $this
          ->getMockBuilder('\Komtet\KassaSdk\Client')
          ->disableOriginalConstructor()
          ->getMock();
      $this->om = new OrderManager($this->client);

      $this->order = new Order('123', 'new', 0);
      $this->order->setClient('г.Пенза, ул.Суворова д.10 кв.25',
                              '+87654443322',
                              'client@email.com',
                              'Сергеев Виктор Сергеевич');
      $this->order->setDeliveryTime('2018-02-28 14:00',
                                    '2018-02-28 15:20');
      $orderPosition = new OrderPosition(['oid' => '1',
                                          'name' => 'position name1',
                                          'price' => 555.0,
                                          'type' => 'product'
                                          ]);
      $this->order->addPosition($orderPosition);
      $this->order->addCourierId(1);
      $this->order->addCallbackUrl('https://calback_url.ru');
  }

  public function testCreateOrderSucceded()
  {
    $path = 'api/shop/v1/orders';
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
    $path = 'api/shop/v1/orders/1';
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
    $path = 'api/shop/v1/orders/1';
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
    $path = 'api/shop/v1/orders/1';
    $this->client
        ->expects($this->once())
        ->method('sendRequest')
        ->with($path)
        ->willReturn(True);
    $this->assertEquals($this->om->deleteOrder(1), True);
  }

  public function testGetOrdersSucceded()
  {
    $path = 'api/shop/v1/orders?start=0&limit=10';
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
    $path = 'api/shop/v1/orders?start=0&limit=10&courier_id=1';
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
    $path = 'api/shop/v1/orders?start=0&limit=10&courier_id=1&date_start=2019-12-12';
    $rep = ['key' => 'val'];
    $this->client
        ->expects($this->once())
        ->method('sendRequest')
        ->with($path)
        ->willReturn(['key' => 'val']);
    $this->assertEquals($this->om->getOrders('0', '10', '1', '2019-12-12'), $rep);
  }

}
