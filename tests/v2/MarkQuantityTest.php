<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\MarkQuantity;
use PHPUnit\Framework\TestCase;

class MarkQuantityTest extends TestCase
{
    public function testMarkQuantitySuccess()
    {
        $mark_quantity = new MarkQuantity(3, 4);
        $this->assertEquals($mark_quantity->asArray(), 
                            ['numerator' => 3,
                             'denominator' => 4]);
    }

    public function testMarkQuantitySuccess2()
    {
        $mark_quantity = new MarkQuantity(15, 1000);
        $this->assertEquals($mark_quantity->asArray(), 
                            ['numerator' => 15,
                             'denominator' => 1000]);
    }

}
