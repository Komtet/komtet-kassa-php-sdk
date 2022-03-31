<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\SectoralCheckProps;
use PHPUnit\Framework\TestCase;

class SectoralCheckPropsTest extends TestCase
{
    public function testSectoralCheckPropsSuccess()
    {
        $sectoral_check_props = new SectoralCheckProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
        $this->assertEquals($sectoral_check_props->asArray(), 
                            ['federal_id' => '001',
                            'date' => '25.10.2020',
                            'number' => '1',
                            'value' => 'значение отраслевого реквизита']);
    }
}
