<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\AuthorisedPerson;
use PHPUnit\Framework\TestCase;

class AuthorisedPersonTest extends TestCase {
    public function testCreateAuthorisedPersonSuccess()
    {
        $authorised_person = new AuthorisedPerson('name', 5023435256);
        $this->assertEquals($authorised_person->asArray(), 
                            ['name' => 'name', 'inn' => 5023435256]);
    }
}