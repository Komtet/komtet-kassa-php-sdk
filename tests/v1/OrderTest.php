<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\Agent;
use Komtet\KassaSdk\v1\Order;
use Komtet\KassaSdk\v1\OrderPosition;
use Komtet\KassaSdk\v1\Payment;
use Komtet\KassaSdk\v1\Vat;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testOrder()
    {
        $order = new Order('123', 0, 'new', false, 200, Payment::TYPE_CASH);

        $this->assertEquals($order->asArray()['order_id'], '123');
        $this->assertEquals($order->asArray()['sno'], 0);
        $this->assertEquals($order->asArray()['state'], 'new');
        $this->assertEquals($order->asArray()['is_paid'], false);
        $this->assertEquals($order->asArray()['prepayment'], 200);
        $this->assertEquals($order->asArray()['payment_type'], Payment::TYPE_CASH);

        $order->setClient(
            'г.Пенза, ул.Суворова д.10 кв.25',
            '+87654443322',
            'client@email.com',
            'Сергеев Виктор Сергеевич'
        );

        $this->assertEquals($order->asArray()['client_name'], 'Сергеев Виктор Сергеевич');
        $this->assertEquals($order->asArray()['client_address'], 'г.Пенза, ул.Суворова д.10 кв.25');
        $this->assertEquals($order->asArray()['client_phone'], '+87654443322');
        $this->assertEquals($order->asArray()['client_email'], 'client@email.com');

        $order->setDeliveryTime(
            '2018-02-28 14:00',
            '2018-02-28 15:20'
        );
        $this->assertEquals($order->asArray()['date_start'], '2018-02-28 14:00');
        $this->assertEquals($order->asArray()['date_end'], '2018-02-28 15:20');

        $order->setDescription('Комментарий к заказу');
        $this->assertEquals($order->asArray()['description'], 'Комментарий к заказу');

        $orderPosition = new OrderPosition([
            'oid' => '1',
            'name' => 'position name1',
            'price' => 555.0,
            'type' => 'product_practical'
        ]);

        $order->addPosition($orderPosition);
        $this->assertEquals($order->asArray()['items'][0]['order_item_id'], '1');
        $this->assertEquals($order->asArray()['items'][0]['name'], 'position name1');
        $this->assertEquals($order->asArray()['items'][0]['vat'], Vat::RATE_NO);
        $this->assertEquals($order->asArray()['items'][0]['total'], 555.0);
        $this->assertEquals($order->asArray()['items'][0]['type'], 'product_practical');

        $orderPosition = new OrderPosition([
            'oid' => '2',
            'name' => 'position name2',
            'price' => 100.0,
            'type' => 'service',
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
        $this->assertEquals($order->asArray()['items'][1]['type'], 'service');

        $order->setCourierId(1);
        $this->assertEquals($order->asArray()['courier_id'], 1);

        $order->setCallbackUrl('https://calback_url.ru');
        $this->assertEquals($order->asArray()['callback_url'], 'https://calback_url.ru');

        $this->assertEquals($order->asArray()['prepayment'], 200);
        $this->assertEquals($order->asArray()['payment_type'], 'cash');

        $orderPosition = new OrderPosition([
            'oid' => '3',
            'name' => 'position name3',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 5,
            'vat' => Vat::RATE_10,
            'measure_name' => 'kg',
            'nomenclature_code' => '019876543210123421sgEKKPPcS25y5'
        ]);
        $order->addPosition($orderPosition);
        $this->assertEquals(
            $order->asArray()['items'][2]['nomenclature_code'],
            '019876543210123421sgEKKPPcS25y5'
        );
        $this->assertEquals($order->asArray()['items'][2]['is_need_nomenclature_code'], false);

        $orderPosition = new OrderPosition([
            'oid' => '4',
            'name' => 'position name4',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 5,
            'vat' => Vat::RATE_10,
            'measure_name' => 'kg',
        ]);
        $orderPosition->setNomenclatureCode('019876543210123421sgEKKPPcS25y5');

        $order->addPosition($orderPosition);
        $this->assertEquals(
            $order->asArray()['items'][3]['nomenclature_code'],
            '019876543210123421sgEKKPPcS25y5'
        );
        $this->assertEquals($order->asArray()['items'][3]['is_need_nomenclature_code'], false);

        $orderPosition = new OrderPosition([
            'oid' => '5',
            'name' => 'position name5',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 5,
            'vat' => Vat::RATE_10,
            'measure_name' => 'kg',
        ]);
        $orderPosition->setNomenclatureCode(NULL);

        $order->addPosition($orderPosition);
        $this->assertArrayNotHasKey('nomenclature_code', $order->asArray()['items'][4]);
        $this->assertTrue($order->asArray()['items'][4]['is_need_nomenclature_code']);
    }

    public function testOrderApplyDiscount()
    {
        $order = new Order('123', 0, 'new', false, 200, Payment::TYPE_CASH);
        $position1 = new OrderPosition([
            'oid' => '1',
            'name' => 'position name1',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 100.0,
            'vat' => Vat::RATE_10,
            'measure_name' => 'kg'
        ]);
        $position2 = new OrderPosition([
            'oid' => '2',
            'name' => 'position name2',
            'price' => 20.0,
            'type' => 'product',
            'quantity' => 2,
            'total' => 40.0,
            'vat' => '18',
            'measure_name' => 'kg'
        ]);
        $position3 = new OrderPosition([
            'oid' => '3',
            'name' => 'position name3',
            'price' => 5.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 5.0,
            'vat' => Vat::RATE_20,
            'measure_name' => 'kg'
        ]);
        $order->addPosition($position1);
        $order->addPosition($position2);
        $order->addPosition($position3);
        $order->applyDiscount(15.0);

        $this->assertEquals($order->asArray()['items'][0]['price'], 100.0);
        $this->assertEquals($order->asArray()['items'][1]['price'], 20.00);
        $this->assertEquals($order->asArray()['items'][2]['price'], 5.0);

        $this->assertEquals($order->asArray()['items'][0]['total'], 89.66);
        $this->assertEquals($order->asArray()['items'][1]['total'], 35.86);
        $this->assertEquals($order->asArray()['items'][2]['total'], 4.48);
    }

    public function testOrderWithAgent()
    {
        $order = new Order('123', 0, 'new', false, 200, Payment::TYPE_CASH);

        $agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "12345678901");

        $position1 = new OrderPosition([
            'oid' => '1',
            'name' => 'position name1',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 100.0,
            'vat' => Vat::RATE_10,
            'measure_name' => 'kg',
            'agent' => $agent
        ]);
        $position2 = new OrderPosition([
            'oid' => '2',
            'name' => 'position name2',
            'price' => 20.0,
            'type' => 'product',
            'quantity' => 2,
            'total' => 40.0,
            'vat' => '18',
            'measure_name' => 'kg'
        ]);
        $position3 = new OrderPosition([
            'oid' => '3',
            'name' => 'position name3',
            'price' => 5.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 5.0,
            'vat' => Vat::RATE_20,
            'measure_name' => 'kg'
        ]);
        $order->addPosition($position1);
        $order->addPosition($position2);
        $order->addPosition($position3);

        $this->assertEquals($order->asArray()['items'][0]['price'], 100.0);
        $this->assertEquals($order->asArray()['items'][0]['agent_info']['type'], Agent::COMMISSIONAIRE);
        $this->assertEquals($order->asArray()['items'][0]['supplier_info']['phones'][0], "+77777777777");
        $this->assertEquals($order->asArray()['items'][0]['supplier_info']['name'], "ООО 'Лютик'");
        $this->assertEquals($order->asArray()['items'][0]['supplier_info']['inn'], "12345678901");
    }

    public function testOrderWithExice()
    {
        $order = new Order('123', 0, 'new', false, 200, Payment::TYPE_CASH);

        $position = new OrderPosition([
            'oid' => '1',
            'name' => 'position name1',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 100.0,
            'vat' => Vat::RATE_10,
            'measure_name' => 'kg',
            'excise' => 19.89,
            'country_code' => '643',
            'declaration_number' => '10129000/220817/0211234'
        ]);

        $order->addPosition($position);

        $this->assertEquals($order->asArray()['items'][0]['excise'], 19.89);
        $this->assertEquals($order->asArray()['items'][0]['country_code'], '643');
        $this->assertEquals($order->asArray()['items'][0]['declaration_number'], '10129000/220817/0211234');
    }

    public function testOrderClientLatitudeLongitude()
    {
        $order = new Order('123', 0, 'new', false, 200, Payment::TYPE_CASH);
        $order->setClient(
            'г.Пенза, ул.Суворова д.144а',
            '+79273784183',
            'client@email.com',
            'Иванов Иван Петрович',
            $coordinate = array(
                'latitude' => '53.202838856701206',
                'longitude' => '44.99768890421866'
            )
        );

        $this->assertEquals($order->asArray()['client_name'], 'Иванов Иван Петрович');
        $this->assertEquals($order->asArray()['client_email'], 'client@email.com');

        $this->assertEquals(
            $order->asArray()['client_coordinate']['latitude'],
            '53.202838856701206'
        );
        $this->assertEquals(
            $order->asArray()['client_coordinate']['longitude'],
            '44.99768890421866'
        );
    }
}
