<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\AuthorisedPerson;
use Komtet\KassaSdk\Cashier;

class CashierTest extends \PHPUnit_Framework_TestCase
{
    public function testCashierNameWithInn()
    {
        $cashier = new Cashier('Иваров И.П.', '1234567890123');
        $this->assertEquals($cashier->asArray()['name'], 'Иваров И.П.');
        $this->assertEquals($cashier->asArray()['inn'], '1234567890123');
    }

    public function testCashierNameWithoutInn()
    {
        $cashier = new Cashier('Иваров И.П.');
        $this->assertEquals($cashier->asArray()['name'], 'Иваров И.П.');
        $this->assertArrayNotHasKey('inn',$cashier->asArray());
    }

    public function testAuthorisedPersonNameWithInn()
    {
        $cashier = new AuthorisedPerson('Иваров И.П.', '1234567890123');
        $this->assertEquals($cashier->asArray()['name'], 'Иваров И.П.');
        $this->assertEquals($cashier->asArray()['inn'], '1234567890123');
    }

    public function testAuthorisedPersonNameWithoutInn()
    {
        $cashier = new AuthorisedPerson('Иваров И.П.');
        $this->assertEquals($cashier->asArray()['name'], 'Иваров И.П.');
        $this->assertArrayNotHasKey('inn',$cashier->asArray());
    }
}


