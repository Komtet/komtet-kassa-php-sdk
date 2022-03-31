<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\Buyer;
use PHPUnit\Framework\TestCase;

class BuyerTest extends TestCase {
    public function testCreateBuyerSuccess()
    {
        $buyer = new Buyer('name', 5023435256);
        $this->assertEquals($buyer->asArray(), 
                            ['name' => 'name', 'inn' => 5023435256]);
    }
}