<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\Nomenclature;


class NomenclatureTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNomenclatureSuccess()
    {
        $nomenclature = new Nomenclature('aabbcc');
        $this->assertEquals($nomenclature->asArray(), ['code' => 'aabbcc']);

        $nomenclature = new Nomenclature('aabbcc', 'AABBCC');
        $this->assertEquals($nomenclature->asArray(), ['code' => 'aabbcc', 'hex_code' =>'AABBCC']);

        $nomenclature = new Nomenclature();
        $nomenclature->setStrCode('aabbcc');
        $this->assertEquals($nomenclature->asArray(), ['code' => 'aabbcc']);

        $nomenclature = new Nomenclature();
        $nomenclature->setHexCode('AABBCC');
        $this->assertEquals($nomenclature->asArray(), ['hex_code' => 'AABBCC']);
    }
}
