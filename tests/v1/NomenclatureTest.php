<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\Nomenclature;
use PHPUnit\Framework\TestCase;

class NomenclatureTest extends TestCase
{
    public function testCreateNomenclatureSuccess()
    {
        $nomenclature = new Nomenclature('aabbcc');
        $this->assertEquals($nomenclature->asArray(), ['code' => 'aabbcc']);

        $nomenclature = new Nomenclature('aabbcc', 'AABBCC');
        $this->assertEquals($nomenclature->asArray(), ['code' => 'aabbcc', 'hex_code' =>'AABBCC']);

        $nomenclature = new Nomenclature();
        $nomenclature->setCode('aabbcc');
        $this->assertEquals($nomenclature->asArray(), ['code' => 'aabbcc']);

        $nomenclature = new Nomenclature();
        $nomenclature->setHexCode('AABBCC');
        $this->assertEquals($nomenclature->asArray(), ['hex_code' => 'AABBCC']);
    }
}
