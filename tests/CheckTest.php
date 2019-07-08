<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\Agent;
use Komtet\KassaSdk\Buyer;
use Komtet\KassaSdk\Check;
use Komtet\KassaSdk\Payment;
use Komtet\KassaSdk\Position;
use Komtet\KassaSdk\Vat;

class CheckTest extends \PHPUnit_Framework_TestCase
{
    public function testPaymentAddress()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1, 'Адрес');
        $this->assertEquals($check->asArray()['payment_address'], 'Адрес');

        $check = Check::createSell('id2', 'test@test.test', 1, 'ул.Пушкина, д.8');
        $this->assertEquals($check->asArray()['payment_address'], 'ул.Пушкина, д.8');

        $check = Check::createSellReturn('id3', 'test@test.test', 1, 'ул.Гагарина, д.12');
        $this->assertEquals($check->asArray()['payment_address'], 'ул.Гагарина, д.12');

        $check = Check::createBuy('id4', 'test@test.test', 1, 'ул.Дзержинского, д.11');
        $this->assertEquals($check->asArray()['payment_address'], 'ул.Дзержинского, д.11');

        $check = Check::createBuyReturn('id5', 'test@test.test', 1, 'ул.Московская, д.13');
        $this->assertEquals($check->asArray()['payment_address'], 'ул.Московская, д.13');
    }

    public function testApplyDiscount()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);

        $vat = new Vat(0);
        $payment1 = new Payment(Payment::TYPE_CARD, 110.0);
        $payment2 = new Payment(Payment::TYPE_CARD, 20.0);
        $position1 = new Position('position1', 100.0, 1, 100.0, 0, $vat);
        $position2 = new Position('position2', 25.0, 2, 40.0, 10.0, $vat);
        $position3 = new Position('position3', 5.0, 1, 5.0, 0, $vat);

        $check->addPayment($payment1);
        $check->addPayment($payment2);
        $check->addPosition($position1);
        $check->addPosition($position2);
        $check->addPosition($position3);
        $discount = 15.0;

        $check->applyDiscount($discount);

        $positionsTotal = 0;
        $positions = $check->getPositions();
        foreach( $positions as $position )
        {
            $positionsTotal += $position->getTotal();
        }

        $this->assertEquals($positionsTotal, 130.0);
        $this->assertEquals($positions[0]->getTotal(), 89.66);
    }

    public function testAddBuyerInfo()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);

        $vat = new Vat(0);
        $position = new Position('position1', 100.0, 1, 100.0, 0, $vat);
        $check->addPosition($position);

        $payment = new Payment(Payment::TYPE_CARD, 100.0);
        $check->addPayment($payment);

        $buyer = new Buyer('Пупкин П.П.', '123412341234');
        $check->addBuyer($buyer);

        $this->assertEquals($check->asArray()['client']['name'], 'Пупкин П.П.');
        $this->assertEquals($check->asArray()['client']['inn'], '123412341234');
    }

    public function testAddBuyerWithoutName()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);

        $vat = new Vat(0);
        $position = new Position('position1', 100.0, 1, 100.0, 0, $vat);
        $check->addPosition($position);

        $payment = new Payment(Payment::TYPE_CARD, 100.0);
        $check->addPayment($payment);

        $buyer = new Buyer();
        $buyer->setINN('123412341234');
        $check->addBuyer($buyer);

        $this->assertEquals($check->asArray()['client']['inn'], '123412341234');
        $this->assertArrayNotHasKey('name', $check->asArray()['client']);
    }
}
