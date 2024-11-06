<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\Vat;
use PHPUnit\Framework\TestCase;

class VatTest extends TestCase
{
    public function testCreateVatWithUnknownRateFailed()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown VAT rate: unknown');

        new Vat('unknown');
    }

    public function testCreateVatSuccess()
    {
        $this->assertEquals((new Vat(0.0))->getRate(), '0');
        $this->assertEquals((new Vat(0))->getRate(), '0');
        $this->assertEquals((new Vat(10))->getRate(), '10');
        $this->assertEquals((new Vat(0.2))->getRate(), '20');
        $this->assertEquals((new Vat(0.20))->getRate(), '20');
        $this->assertEquals((new Vat(20.0))->getRate(), '20');
        $this->assertEquals((new Vat(110))->getRate(), '110');
        $this->assertEquals((new Vat(120))->getRate(), '120');
        $this->assertEquals((new Vat('0.0'))->getRate(), '0');
        $this->assertEquals((new Vat('10'))->getRate(), '10');
        $this->assertEquals((new Vat('10%'))->getRate(), '10');
        $this->assertEquals((new Vat('0.20'))->getRate(), '20');
        $this->assertEquals((new Vat('110'))->getRate(), '110');
        $this->assertEquals((new Vat('10/110'))->getRate(), '110');
        $this->assertEquals((new Vat('120'))->getRate(), '120');
        $this->assertEquals((new Vat('20/120'))->getRate(), '120');

        $this->assertEquals((new Vat(18))->getRate(), '20');
        $this->assertEquals((new Vat(0.18))->getRate(), '20');
        $this->assertEquals((new Vat(18.0))->getRate(), '20');
        $this->assertEquals((new Vat('18'))->getRate(), '20');
        $this->assertEquals((new Vat('0.18'))->getRate(), '20');
        $this->assertEquals((new Vat('118'))->getRate(), '120');
        $this->assertEquals((new Vat('18/118'))->getRate(), '120');
    }
}
