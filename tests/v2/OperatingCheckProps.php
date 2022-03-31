<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\OperatingCheckProps;
use PHPUnit\Framework\TestCase;

class OperatingCheckPropsTest extends TestCase
{
    public function testOperatingCheckProps()
    {
        $operating_check_props = new OperatingCheckProps('name', 'данные операции', '12.03.2020 16:55:25');
        $this->assertEquals($operating_check_props->asArray(), 
                            ['name' => 'name', 
                             'value' => 'данные операции',
                             'timestamp' => '12.03.2020 16:55:25']);
    }
}
