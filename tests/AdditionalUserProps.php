<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\AdditionalUserProps;


class AdditionalUserPropsTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateAdditionalUserPropsSuccess()
    {
        $additional_user_props = new AdditionalUserProps('props_name', 'props_value');
        $this->assertEquals($additional_user_props->asArray(), 
                            ['name' => 'props_name', 'value' => 'props_value']);
    }
}
