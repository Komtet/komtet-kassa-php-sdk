<?php

/**
* This file is part of the motmom/komtet-kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace MotmomTest\KomtetKassaSdk;

use Motmom\KomtetKassaSdk\Vat;

class VatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unexpected sum type: expects int or float, array given
     */
    public function testCreateVatWithNonNumberFailed()
    {
        new Vat([], 'no');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown VAT type: unknown
     */
    public function testCreateVatWithUnknownTypeFailed()
    {
        new Vat(0, 'unknown');
    }

    public function testCreateVatSuccess()
    {
        $this->assertEquals((new Vat(10, 10))->asArray(), ['sum' => 10, 'number' => '10']);
        $this->assertEquals((new Vat(20, 0.18))->asArray(), ['sum' => 20, 'number' => '18']);
        $this->assertEquals((new Vat(30, 118))->asArray(), ['sum' => 30, 'number' => '118']);
        $this->assertEquals((new Vat(40, '118'))->asArray(), ['sum' => 40, 'number' => '118']);
        $this->assertEquals((new Vat(50, '18/118'))->asArray(), ['sum' => 50, 'number' => '118']);
        $this->assertEquals((new Vat(60, '110'))->asArray(), ['sum' => 60, 'number' => '110']);
        $this->assertEquals((new Vat(70, 110))->asArray(), ['sum' => 70, 'number' => '110']);
        $this->assertEquals((new Vat(80, '10/100'))->asArray(), ['sum' => 80, 'number' => '110']);
        $this->assertEquals((new Vat(90, '10%'))->asArray(), ['sum' => 90, 'number' => '10']);
        $this->assertEquals((new Vat(100, '0.18'))->asArray(), ['sum' => 100, 'number' => '18']);
        $this->assertEquals((new Vat(110, '10'))->asArray(), ['sum' => 110, 'number' => '10']);
    }
}
