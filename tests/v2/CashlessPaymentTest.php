<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v1\CashlessPayment;
use PHPUnit\Framework\TestCase;

class CashlessPaymentTest extends TestCase
{
    public function testCreateCashlessPaymentSuccess()
    {
        $payment = new CashlessPayment(100.50, 1, 'payment_id_123');
        $this->assertEquals(
            $payment->asArray(),
            [
                'sum' => 100.50,
                'method' => 1,
                'id' => 'payment_id_123'
            ]
        );
    }

    public function testCreateCashlessPaymentWithAdditionalInfo()
    {
        $payment = new CashlessPayment(200, 2, 'payment_id_456');
        $payment->setAdditionalInfo('Дополнительная информация о платеже');

        $this->assertEquals(
            $payment->asArray(),
            [
                'sum' => 200,
                'method' => 2,
                'id' => 'payment_id_456',
                'additionalInfo' => 'Дополнительная информация о платеже'
            ]
        );
    }
}
