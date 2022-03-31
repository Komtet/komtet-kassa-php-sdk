<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\AdditionalUserProps;
use PHPUnit\Framework\TestCase;

class AdditionalUserPropsTest extends TestCase
{
    public function testCreateAdditionalUserPropsSuccess()
    {
        $additional_user_props = new AdditionalUserProps('props_name', 'props_value');
        $this->assertEquals($additional_user_props->asArray(), 
                            ['name' => 'props_name', 'value' => 'props_value']);
    }
}
