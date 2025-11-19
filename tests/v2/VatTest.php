<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\Vat;
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
        $this->assertEquals((new Vat('0'))->getRate(), '0');
        $this->assertEquals((new Vat('0.0'))->getRate(), '0');
        $this->assertEquals((new Vat('0%'))->getRate(), '0');

        $this->assertEquals((new Vat(5))->getRate(), '5');
        $this->assertEquals((new Vat(0.05))->getRate(), '5');
        $this->assertEquals((new Vat('5'))->getRate(), '5');
        $this->assertEquals((new Vat('0.05'))->getRate(), '5');
        $this->assertEquals((new Vat('5%'))->getRate(), '5');

        $this->assertEquals((new Vat(7))->getRate(), '7');
        $this->assertEquals((new Vat(0.07))->getRate(), '7');
        $this->assertEquals((new Vat('7'))->getRate(), '7');
        $this->assertEquals((new Vat('0.07'))->getRate(), '7');
        $this->assertEquals((new Vat('7%'))->getRate(), '7');

        $this->assertEquals((new Vat(10))->getRate(), '10');
        $this->assertEquals((new Vat(0.1))->getRate(), '10');
        $this->assertEquals((new Vat(0.10))->getRate(), '10');
        $this->assertEquals((new Vat('10'))->getRate(), '10');
        $this->assertEquals((new Vat('0.1'))->getRate(), '10');
        $this->assertEquals((new Vat('0.10'))->getRate(), '10');
        $this->assertEquals((new Vat('10%'))->getRate(), '10');

        $this->assertEquals((new Vat(0.2))->getRate(), '20');
        $this->assertEquals((new Vat(0.20))->getRate(), '20');
        $this->assertEquals((new Vat(20))->getRate(), '20');
        $this->assertEquals((new Vat('20'))->getRate(), '20');
        $this->assertEquals((new Vat('0.2'))->getRate(), '20');
        $this->assertEquals((new Vat('0.20'))->getRate(), '20');
        $this->assertEquals((new Vat('20%'))->getRate(), '20');

        $this->assertEquals((new Vat(0.22))->getRate(), '22');
        $this->assertEquals((new Vat(22))->getRate(), '22');
        $this->assertEquals((new Vat('22'))->getRate(), '22');
        $this->assertEquals((new Vat('0.22'))->getRate(), '22');
        $this->assertEquals((new Vat('22%'))->getRate(), '22');

        $this->assertEquals((new Vat(105))->getRate(), '105');
        $this->assertEquals((new Vat('105'))->getRate(), '105');
        $this->assertEquals((new Vat('5/105'))->getRate(), '105');

        $this->assertEquals((new Vat(107))->getRate(), '107');
        $this->assertEquals((new Vat('107'))->getRate(), '107');
        $this->assertEquals((new Vat('7/107'))->getRate(), '107');

        $this->assertEquals((new Vat(110))->getRate(), '110');
        $this->assertEquals((new Vat('110'))->getRate(), '110');
        $this->assertEquals((new Vat('10/110'))->getRate(), '110');

        $this->assertEquals((new Vat(120))->getRate(), '120');
        $this->assertEquals((new Vat('120'))->getRate(), '120');
        $this->assertEquals((new Vat('20/120'))->getRate(), '120');

        $this->assertEquals((new Vat(122))->getRate(), '122');
        $this->assertEquals((new Vat('122'))->getRate(), '122');
        $this->assertEquals((new Vat('22/122'))->getRate(), '122');
    }
}
