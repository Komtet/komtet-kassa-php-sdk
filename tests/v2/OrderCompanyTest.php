<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\OrderCompany;
use Komtet\KassaSdk\v2\TaxSystem;
use PHPUnit\Framework\TestCase;

class OrderCompanyTest extends TestCase {

    public function testCreateOrderCompanySuccess()
    {
        $payment_address = 'Офис 3';
        $orderCompany = new OrderCompany(TaxSystem::COMMON, $payment_address);

        $this->assertEquals($orderCompany->asArray(), 
                            ['sno' => 0,
                             'payment_address' => 'Офис 3']);
    }

    public function testCreateOrderCompanyWithAllOptionalParamsSuccess()
    {
        $payment_address = 'Офис 3';
        $orderCompany = new OrderCompany(TaxSystem::COMMON, $payment_address);
        $orderCompany->setPlaceAddress('г. Москва');
        $orderCompany->setINN('502906602876');

        $this->assertEquals($orderCompany->asArray(), 
                            ['sno' => 0,
                            'payment_address' => 'Офис 3',
                            'place_address' => 'г. Москва',
                            'inn' => '502906602876']);
    }
}
