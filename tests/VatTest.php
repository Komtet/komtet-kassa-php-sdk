<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\Vat;

class VatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown VAT rate: unknown
     */
    public function testCreateVatWithUnknownRateFailed()
    {
        new Vat('unknown');
    }

    public function testCreateVatSuccess()
    {
        $this->assertEquals((new Vat(10))->getRate(), '10');
        $this->assertEquals((new Vat(0.18))->getRate(), '18');
        $this->assertEquals((new Vat(118))->getRate(), '118');
        $this->assertEquals((new Vat('118'))->getRate(), '118');
        $this->assertEquals((new Vat('18/118'))->getRate(), '118');
        $this->assertEquals((new Vat('110'))->getRate(), '110');
        $this->assertEquals((new Vat(110))->getRate(), '110');
        $this->assertEquals((new Vat('10/110'))->getRate(), '110');
        $this->assertEquals((new Vat('10%'))->getRate(), '10');
        $this->assertEquals((new Vat('0.18'))->getRate(), '18');
        $this->assertEquals((new Vat('10'))->getRate(), '10');
    }
}
