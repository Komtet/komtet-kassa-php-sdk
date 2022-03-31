<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\SectoralItemProps;
use PHPUnit\Framework\TestCase;

class SectoralItemPropsTest extends TestCase
{
    public function testSectoralItemPropsSuccess()
    {
        $sectoral_item_props = new SectoralItemProps('00500', '29.11.2021', '100', 'значение отраслевого реквизита5');
        $this->assertEquals($sectoral_item_props->asArray(), 
                            ['federal_id' => '00500',
                            'date' => '29.11.2021',
                            'number' => '100',
                            'value' => 'значение отраслевого реквизита5']);
    }
}
