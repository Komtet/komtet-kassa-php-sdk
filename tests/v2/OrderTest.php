<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\AdditionalUserProps;
use Komtet\KassaSdk\v2\Agent;
use Komtet\KassaSdk\v2\MarkCode;
use Komtet\KassaSdk\v2\MarkQuantity;
use Komtet\KassaSdk\v2\OperatingCheckProps;
use Komtet\KassaSdk\v2\Order;
use Komtet\KassaSdk\v2\OrderBuyer;
use Komtet\KassaSdk\v2\OrderCompany;
use Komtet\KassaSdk\v2\OrderPosition;
use Komtet\KassaSdk\v2\Payment;
use Komtet\KassaSdk\v2\SectoralCheckProps;
use Komtet\KassaSdk\v2\SectoralItemProps;
use Komtet\KassaSdk\v2\TaxSystem;
use Komtet\KassaSdk\v2\Vat;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private $order;

    protected function setUp(): void
    {
        $this->order = new Order('123', 'new', false);

        $this->order->setOrderBuyer(new OrderBuyer(
            '+87654443322',
            'г.Пенза, ул.Суворова д.10 кв.25',
            'Сергеев Виктор Сергеевич',
            '502906602876',
            'client@email.com'
        ));

        $this->order->setCompany(new OrderCompany(
            TaxSystem::COMMON,
            'Улица Московская д.4',
            'г. Москва',
            '502906602876'
        ));

        $this->order->setDeliveryTime(
            '20.02.2022 14:00',
            '20.02.2022 15:20'
        );

        $orderPosition = new OrderPosition([
            'order_item_id' => '1',
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
    }

    public function testOrder()
    {
        $order = new Order('123', 'new', false, 200, Payment::TYPE_CASH);

        $order->setOrderBuyer(new OrderBuyer(
            '+87654443322',
            'г.Пенза, ул.Суворова д.10 кв.25',
            'Сергеев Виктор Сергеевич',
            '502906602876',
            'client@email.com'
        ));

        $order->setCompany(new OrderCompany(
            TaxSystem::COMMON,
            'Улица Московская д.4',
            'г. Москва',
            '502906602876'
        ));

        $this->assertEquals($order->asArray()['external_id'], '123');
        $this->assertEquals($order->asArray()['state'], 'new');
        $this->assertEquals($order->asArray()['is_pay_to_courier'], false);
        $this->assertEquals($order->asArray()['prepayment'], 200);
        $this->assertEquals($order->asArray()['payment_type'], Payment::TYPE_CASH);

        $this->assertEquals($order->asArray()['client']['phone'], '+87654443322');
        $this->assertEquals($order->asArray()['client']['address'], 'г.Пенза, ул.Суворова д.10 кв.25');
        $this->assertEquals($order->asArray()['client']['name'], 'Сергеев Виктор Сергеевич');
        $this->assertEquals($order->asArray()['client']['inn'], '502906602876');
        $this->assertEquals($order->asArray()['client']['email'], 'client@email.com');

        $this->assertEquals($order->asArray()['company']['sno'], TaxSystem::COMMON);
        $this->assertEquals($order->asArray()['company']['payment_address'], 'Улица Московская д.4');
        $this->assertEquals($order->asArray()['company']['place_address'], 'г. Москва');
        $this->assertEquals($order->asArray()['company']['inn'], '502906602876');

        $order->setDeliveryTime(
            '20.02.2022 14:00',
            '20.02.2022 15:20'
        );
        $this->assertEquals($order->asArray()['date_start'], '20.02.2022 14:00');
        $this->assertEquals($order->asArray()['date_end'], '20.02.2022 15:20');

        $order->setDescription('Комментарий к заказу');
        $this->assertEquals($order->asArray()['description'], 'Комментарий к заказу');

        $orderPosition = new OrderPosition([
            'order_item_id' => '1',
            'name' => 'position name1',
            'price' => 555.0,
            'quantity' => 1,
            'total' => 555.0,
            'type' => 'product_practical',
            'vat' => '20',
            'measure' => 0,
            'excise' => 5,
            'country_code' => '6',
            'declaration_number' => '7',
            'user_data' => 'доп. реквизит',
            'is_need_mark_code' => false,
        ]);

        $order->addPosition($orderPosition);
        $this->assertEquals($order->asArray()['items'][0]['order_item_id'], '1');
        $this->assertEquals($order->asArray()['items'][0]['name'], 'position name1');
        $this->assertEquals($order->asArray()['items'][0]['price'], 555.0);
        $this->assertEquals($order->asArray()['items'][0]['quantity'], 1);
        $this->assertEquals($order->asArray()['items'][0]['total'], 555.0);
        $this->assertEquals($order->asArray()['items'][0]['type'], 'product_practical');
        $this->assertEquals($order->asArray()['items'][0]['vat'], Vat::RATE_20);
        $this->assertEquals($order->asArray()['items'][0]['measure'], 0);
        $this->assertEquals($order->asArray()['items'][0]['excise'], 5);
        $this->assertEquals($order->asArray()['items'][0]['country_code'], '6');
        $this->assertEquals($order->asArray()['items'][0]['declaration_number'], '7');
        $this->assertEquals($order->asArray()['items'][0]['user_data'], 'доп. реквизит');
        $this->assertEquals($order->asArray()['items'][0]['is_need_mark_code'], false);

        $orderPosition = new OrderPosition([
            'order_item_id' => '2',
            'name' => 'position name2',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 5,
            'vat' => Vat::RATE_10,
            'measure' => 0
        ]);

        $order->addPosition($orderPosition);
        $this->assertEquals($order->asArray()['items'][1]['order_item_id'], '2');
        $this->assertEquals($order->asArray()['items'][1]['name'], 'position name2');
        $this->assertEquals($order->asArray()['items'][1]['vat'], Vat::RATE_10);
        $this->assertEquals($order->asArray()['items'][1]['total'], 500.0);
        $this->assertEquals($order->asArray()['items'][1]['measure'], 0);

        $this->assertEquals($order->asArray()['prepayment'], 200);
        $this->assertEquals($order->asArray()['payment_type'], 'cash');

        $orderPosition = new OrderPosition([
            'order_item_id' => '3',
            'name' => 'position name4',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 5,
            'vat' => Vat::RATE_10,
            'measure' => 2,
        ]);
        $orderPosition->setMarkCode(new MarkCode(MarkCode::GS1M, 'qweds234wfsd1231da675yhdfxdbg'));

        $orderPosition->setMarkQuantity(new MarkQuantity(3, 4));

        $sectoral_item_props = new SectoralItemProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
        $sectoral_item_props2 = new SectoralItemProps('002', '25.10.2020', '1', 'значение отраслевого реквизита');
        $orderPosition->setSectoralItemProps($sectoral_item_props);
        $orderPosition->setSectoralItemProps($sectoral_item_props2);

        $order->addPosition($orderPosition);

        $this->assertEquals(
            $order->asArray()['items'][2]['mark_code'],
            ['gs1m' => 'qweds234wfsd1231da675yhdfxdbg']
        );
        $this->assertEquals(
            $order->asArray()['items'][2]['mark_quantity'],
            [
                'numerator' => 3,
                'denominator' => 4
            ]
        );
        $this->assertEquals($order->asArray()['items'][2]['is_need_mark_code'], false);
        $this->assertEquals(
            $order->asArray()['items'][2]['sectoral_item_props'],
            [
                0 =>
                [
                    'federal_id' => '001',
                    'date' => '25.10.2020',
                    'number' => '1',
                    'value' => 'значение отраслевого реквизита'
                ],
                1 =>
                [
                    'federal_id' => '002',
                    'date' => '25.10.2020',
                    'number' => '1',
                    'value' => 'значение отраслевого реквизита'
                ]
            ]
        );
    }

    public function testSetCallbackUrl()
    {
        $this->order->setCallbackUrl('https://calback_url.ru');
        $this->assertEquals($this->order->asArray()['callback_url'], 'https://calback_url.ru');
    }

    public function testSetCourierId()
    {
        $this->order->setCourierId(15);
        $this->assertEquals($this->order->asArray()['courier_id'], 15);
    }

    public function testSetAdditionalCheckProps()
    {
        $this->order->setAdditionalCheckProps("Дополнительный реквизит");

        $this->assertEquals($this->order->asArray()['additional_check_props'], 'Дополнительный реквизит');
    }

    public function testSetAdditionalUserProps()
    {
        $additional_user_props = new AdditionalUserProps('order_name_props', 'order_value_props');
        $this->order->setAdditionalUserProps($additional_user_props);
        $this->assertEquals(
            $this->order->asArray()['additional_user_props'],
            ['name' => 'order_name_props', 'value' => 'order_value_props']
        );
    }

    public function testSetOneSectoralCheckProps()
    {
        $sectoral_check_props = new SectoralCheckProps('0015', '25.10.2020', '1', 'значение отраслевого реквизита');
        $this->order->setSectoralCheckProps($sectoral_check_props);
        $this->assertEquals(
            $this->order->asArray()['sectoral_check_props'][0],
            [
                'federal_id' => '0015',
                'date' => '25.10.2020',
                'number' => '1',
                'value' => 'значение отраслевого реквизита'
            ]
        );
    }

    public function testSetTwoSectoralCheckProps()
    {
        $sectoral_check_props = new SectoralCheckProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
        $sectoral_check_props1 = new SectoralCheckProps('002', '26.10.2020', '2', 'значение отраслевого реквизита2');
        $this->order->setSectoralCheckProps($sectoral_check_props);
        $this->order->setSectoralCheckProps($sectoral_check_props1);
        $this->assertEquals(
            $this->order->asArray()['sectoral_check_props'][0],
            [
                'federal_id' => '001',
                'date' => '25.10.2020',
                'number' => '1',
                'value' => 'значение отраслевого реквизита'
            ]
        );
        $this->assertEquals(
            $this->order->asArray()['sectoral_check_props'][1],
            [
                'federal_id' => '002',
                'date' => '26.10.2020',
                'number' => '2',
                'value' => 'значение отраслевого реквизита2'
            ]
        );
    }

    public function testsetOperatingCheckProps()
    {
        $operating_check_props = new OperatingCheckProps('1', 'данные операции', '12.04.2020 16:55:25');
        $this->order->setOperatingCheckProps($operating_check_props);
        $this->assertEquals(
            $this->order->asArray()['operating_check_props'],
            [
                'name' => '1',
                'value' => 'данные операции',
                'timestamp' => '12.04.2020 16:55:25'
            ]
        );
    }

    public function testOrderApplyDiscount()
    {
        $order = new Order('123', 0, 'new', false, 200, Payment::TYPE_CASH);

        $order->setOrderBuyer(new OrderBuyer(
            '+87654443322',
            'г.Пенза, ул.Суворова д.10 кв.25',
            'Сергеев Виктор Сергеевич',
            '502906602876',
            'client@email.com'
        ));

        $order->setCompany(new OrderCompany(
            TaxSystem::COMMON,
            'Улица Московская д.4',
            'г. Москва',
            '502906602876'
        ));

        $order->setDeliveryTime(
            '20.02.2022 14:00',
            '20.02.2022 15:20'
        );

        $position1 = new OrderPosition([
            'order_item_id' => '1',
            'name' => 'position name1',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 100.0,
            'vat' => Vat::RATE_10,
            'measure' => 1
        ]);
        $position2 = new OrderPosition([
            'order_item_id' => '2',
            'name' => 'position name2',
            'price' => 20.0,
            'type' => 'product',
            'quantity' => 2,
            'total' => 40.0,
            'vat' => '18',
            'measure' => 2
        ]);
        $position3 = new OrderPosition([
            'order_item_id' => '3',
            'name' => 'position name3',
            'price' => 5.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 5.0,
            'vat' => Vat::RATE_20,
            'measure' => 3
        ]);
        $order->addPosition($position1);
        $order->addPosition($position2);
        $order->addPosition($position3);
        $order->applyDiscount(15.0);

        $this->assertEquals($order->asArray()['items'][0]['order_item_id'], 1);
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

        $order->setOrderBuyer(new OrderBuyer(
            '+87654443322',
            'г.Пенза, ул.Суворова д.10 кв.25',
            'Сергеев Виктор Сергеевич',
            '502906602876',
            'client@email.com'
        ));

        $order->setCompany(new OrderCompany(
            TaxSystem::COMMON,
            'Улица Московская д.4',
            'г. Москва',
            '502906602876'
        ));

        $order->setDeliveryTime(
            '20.02.2022 14:00',
            '20.02.2022 15:20'
        );

        $agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "12345678901");

        $position1 = new OrderPosition([
            'order_item_id' => '1',
            'name' => 'position name1',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 100.0,
            'vat' => Vat::RATE_10,
            'measure' => 'kg',
            'agent' => $agent
        ]);
        $position2 = new OrderPosition([
            'order_item_id' => '2',
            'name' => 'position name2',
            'price' => 20.0,
            'type' => 'product',
            'quantity' => 2,
            'total' => 40.0,
            'vat' => '18',
            'measure' => 'kg'
        ]);
        $position3 = new OrderPosition([
            'order_item_id' => '3',
            'name' => 'position name3',
            'price' => 5.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 5.0,
            'vat' => Vat::RATE_20,
            'measure' => 'kg'
        ]);
        $order->addPosition($position1);
        $order->addPosition($position2);
        $position2->setAgent($agent);
        $order->addPosition($position3);

        $this->assertEquals($order->asArray()['items'][0]['price'], 100.0);
        $this->assertEquals($order->asArray()['items'][0]['agent_info']['type'], Agent::COMMISSIONAIRE);
        $this->assertEquals($order->asArray()['items'][0]['supplier_info']['phones'][0], "+77777777777");
        $this->assertEquals($order->asArray()['items'][0]['supplier_info']['name'], "ООО 'Лютик'");
        $this->assertEquals($order->asArray()['items'][0]['supplier_info']['inn'], "12345678901");
        $this->assertEquals($order->asArray()['items'][1]['price'], 20.0);
        $this->assertEquals($order->asArray()['items'][1]['agent_info']['type'], Agent::COMMISSIONAIRE);
        $this->assertEquals($order->asArray()['items'][1]['supplier_info']['phones'][0], "+77777777777");
        $this->assertEquals($order->asArray()['items'][1]['supplier_info']['name'], "ООО 'Лютик'");
        $this->assertEquals($order->asArray()['items'][1]['supplier_info']['inn'], "12345678901");
    }

    public function testOrderWithOptionalParamsInPosition()
    {
        $order = new Order('123', 0, 'new', false, 200, Payment::TYPE_CASH);

        $order->setOrderBuyer(new OrderBuyer(
            '+87654443322',
            'г.Пенза, ул.Суворова д.10 кв.25',
            'Сергеев Виктор Сергеевич',
            '502906602876',
            'client@email.com'
        ));

        $order->setCompany(new OrderCompany(
            TaxSystem::COMMON,
            'Улица Московская д.4',
            'г. Москва',
            '502906602876'
        ));

        $order->setDeliveryTime(
            '20.02.2022 14:00',
            '20.02.2022 15:20'
        );

        $position = new OrderPosition([
            'order_item_id' => '1',
            'name' => 'position name1',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 100.0,
            'vat' => Vat::RATE_10,
            'measure' => 0,
            'excise' => 5,
            'country_code' => '6',
            'declaration_number' => '10129000/220817/0211234',
            'user_data' => 'доп. реквизит',
            'is_need_mark_code' => true,
        ]);

        $order->addPosition($position);

        $this->assertEquals($order->asArray()['items'][0]['excise'], 5);
        $this->assertEquals($order->asArray()['items'][0]['country_code'], '6');
        $this->assertEquals($order->asArray()['items'][0]['declaration_number'], '10129000/220817/0211234');
        $this->assertEquals($order->asArray()['items'][0]['user_data'], 'доп. реквизит');
        $this->assertEquals($order->asArray()['items'][0]['is_need_mark_code'], true);
    }

    public function testOrderBuyerSetCoordinate()
    {
        $order = new Order('123', 0, 'new', false, 200, Payment::TYPE_CASH);

        $orderBuyer = new OrderBuyer(
            '+87654443322',
            'г.Пенза, ул.Суворова д.10 кв.25',
            'Сергеев Виктор Сергеевич',
            '502906602876',
            'client@email.com'
        );
        $orderBuyer->setCoordinate('53.202838856701206', '44.99768890421866');
        $order->setOrderBuyer($orderBuyer);

        $order->setCompany(new OrderCompany(
            TaxSystem::COMMON,
            'Улица Московская д.4'
        ));

        $order->setDeliveryTime(
            '20.02.2022 14:00',
            '20.02.2022 15:20'
        );

        $position = new OrderPosition([
            'order_item_id' => '1',
            'name' => 'position name1',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 100.0,
            'vat' => Vat::RATE_10,
            'measure' => 0,
        ]);

        $this->assertEquals($order->asArray()['client']['phone'], '+87654443322');
        $this->assertEquals($order->asArray()['client']['address'], 'г.Пенза, ул.Суворова д.10 кв.25');
        $this->assertEquals($order->asArray()['client']['name'], 'Сергеев Виктор Сергеевич');
        $this->assertEquals($order->asArray()['client']['inn'], '502906602876');
        $this->assertEquals($order->asArray()['client']['email'], 'client@email.com');

        $this->assertEquals(
            $order->asArray()['client']['coordinate']['longitude'],
            '53.202838856701206'
        );
        $this->assertEquals(
            $order->asArray()['client']['coordinate']['latitude'],
            '44.99768890421866'
        );
    }

    public function testOrderBuyerCoordinateInConstructor()
    {
        $order = new Order('123', 0, 'new', false, 200, Payment::TYPE_CASH);

        $orderBuyer = new OrderBuyer(
            '+87654443322',
            'г.Пенза, ул.Суворова д.10 кв.25',
            'Сергеев Виктор Сергеевич',
            '502906602876',
            'client@email.com',
            $coordinate = array(
                'longitude' => '53.202838856701206',
                'latitude' => '44.99768890421866'
            )
        );
        $order->setOrderBuyer($orderBuyer);

        $order->setCompany(new OrderCompany(
            TaxSystem::COMMON,
            'Улица Московская д.4'
        ));

        $order->setDeliveryTime(
            '20.02.2022 14:00',
            '20.02.2022 15:20'
        );

        $position = new OrderPosition([
            'order_item_id' => '1',
            'name' => 'position name1',
            'price' => 100.0,
            'type' => 'product',
            'quantity' => 1,
            'total' => 100.0,
            'vat' => Vat::RATE_10,
            'measure' => 0,
        ]);

        $this->assertEquals($order->asArray()['client']['phone'], '+87654443322');
        $this->assertEquals($order->asArray()['client']['address'], 'г.Пенза, ул.Суворова д.10 кв.25');
        $this->assertEquals($order->asArray()['client']['name'], 'Сергеев Виктор Сергеевич');
        $this->assertEquals($order->asArray()['client']['inn'], '502906602876');
        $this->assertEquals($order->asArray()['client']['email'], 'client@email.com');
        $this->assertEquals(
            $order->asArray()['client']['coordinate']['longitude'],
            '53.202838856701206'
        );
        $this->assertEquals(
            $order->asArray()['client']['coordinate']['latitude'],
            '44.99768890421866'
        );
    }
}
