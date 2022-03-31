<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\OrderBuyer;
use PHPUnit\Framework\TestCase;

class OrderBuyerTest extends TestCase
{
    public function testCreateOrderBuyerSuccess()
    {
        $orderBuyer = new OrderBuyer('+87654443322', 
                                     'г.Пенза, ул.Суворова д.10 кв.25');
        $this->assertEquals($orderBuyer->asArray(), 
                            ['phone' => '+87654443322',
                             'address' => 'г.Пенза, ул.Суворова д.10 кв.25']);
    }

    public function testCreateOrderBuyerWithOptionalParamsSuccess()
    {
        $orderBuyer = new OrderBuyer('+87654443322', 
                                'г.Пенза, ул.Суворова д.10 кв.25',
                                'Сергеев Виктор Сергеевич',
                                '502906602876',
                                'client@email.com',
                                $coordinate = array('longitude' => '53.202838856701206',
                                                    'latitude' => '44.99768890421866'));
        $this->assertEquals($orderBuyer->asArray(), 
                            ['phone' => '+87654443322',
                             'address' => 'г.Пенза, ул.Суворова д.10 кв.25',
                             'name' => 'Сергеев Виктор Сергеевич',
                             'inn' => '502906602876',
                             'email' => 'client@email.com',
                             'coordinate' => ['longitude' => '53.202838856701206',
                                              'latitude' => '44.99768890421866']]);
    }

    public function testOrderBuyerSetCoordinate()
    {
        $orderBuyer = new OrderBuyer('+87654443322', 
                                     'г.Пенза, ул.Суворова д.10 кв.25');
        $orderBuyer->setCoordinate('53.202838856701206', '44.99768890421866');
        $this->assertEquals($orderBuyer->asArray(), 
                            ['phone' => '+87654443322',
                             'address' => 'г.Пенза, ул.Суворова д.10 кв.25',
                             'coordinate' => ['longitude' => '53.202838856701206',
                                              'latitude' => '44.99768890421866']]);
    }
}