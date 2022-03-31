<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\Company;
use Komtet\KassaSdk\v2\TaxSystem;
use PHPUnit\Framework\TestCase;

class CompanyTest extends TestCase {

    public function testCreateCompanySuccess()
    {
        $payment_address = 'Офис 3';
        $company = new Company(TaxSystem::COMMON, $payment_address);

        $this->assertEquals($company->asArray(), 
                            ['sno' => 0,
                             'payment_address' => 'Офис 3']);
    }

    public function testCreateCompanyWithAllOptionalParamsSuccess()
    {
        $payment_address = 'Офис 3';
        $company = new Company(TaxSystem::COMMON, $payment_address);
        $company->setPlaceAddress('г. Москва');
        $company->setINN('502906602876');

        $this->assertEquals($company->asArray(), 
                            ['sno' => 0,
                            'payment_address' => 'Офис 3',
                            'place_address' => 'г. Москва',
                            'inn' => '502906602876']);
    }
}
