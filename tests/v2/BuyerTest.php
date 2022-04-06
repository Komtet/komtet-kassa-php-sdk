<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\Buyer;
use PHPUnit\Framework\TestCase;

class BuyerTest extends TestCase 
{
    public function testCreateBuyerSuccess()
    {
        $buyer = new Buyer();
        $buyer->setEmail('test@test.ru');
        $this->assertEquals($buyer->asArray(), 
                            ['email' => 'test@test.ru']);
    }

    public function testCreateBuyerWithOptionalParams()
    {
        $buyer = new Buyer();
        $buyer->setEmail('test@test.ru');
        $buyer->setName('name');
        $buyer->setINN('0123456789');
        $buyer->setPhone('+79099099999');
        $buyer->setBirthdate('02.03.1995');
        $buyer->setCitizenship('Citizenship');
        $buyer->setDocumentCode('564');
        $buyer->setDocumentData('02.03.1995');
        $buyer->setAddress('ул. Московская');
        $this->assertEquals($buyer->asArray(), 
                            ['email' => 'test@test.ru',
                            'name' => 'name',
                            'inn' => '0123456789',
                            'phone' => '+79099099999',
                            'birthdate' => '02.03.1995',
                            'citizenship' => 'Citizenship',
                            'document_code' => '564',
                            'document_data' => '02.03.1995',
                            'address' => 'ул. Московская']);
    }
}