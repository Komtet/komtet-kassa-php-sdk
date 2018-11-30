<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\Check;
use Komtet\KassaSdk\Payment;
use Komtet\KassaSdk\Position;
use Komtet\KassaSdk\Vat;

class CheckTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);
    }

    public function testApplyDiscount()
    {
        $vat = new Vat(0);
        $payment1 = new Payment(Payment::TYPE_CARD, 110.0);
        $payment2 = new Payment(Payment::TYPE_CARD, 15.0);
        $position1 = new Position('position1', 100.0, 1, 100.0, 0, $vat);
        $position2 = new Position('position2', 25.0, 2, 40.0, 10.0, $vat);

        $this->check->addPayment($payment1);
        $this->check->addPayment($payment2);
        $this->check->addPosition($position1);
        $this->check->addPosition($position2);
        $discount = 15.0;

        $this->check->applyDiscount($discount);

        $positionsTotal = 0;
        foreach( $this->check->getPositions() as $position )
        {
            $positionsTotal += $position->getTotal();
        }

        $this->assertEquals($positionsTotal, $this->check->getTotalPaymentsSum());
    }
}
