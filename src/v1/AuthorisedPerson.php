<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v1;

class AuthorisedPerson
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int|float
     */
    private $inn;

    /**
     * @param string $name
     * @param string $inn
     *
     * @return AuthorisedPerson
     */
    public function __construct($name, $inn=null)
    {
        $this->name = $name;
        $this->inn = $inn;
    }

    /**
     * @return array
     */
    public function asArray()
    {    
        $result = ['name' => $this->name];

        if ($this->inn !== null) {
            $result['inn'] = $this->inn;
        }

        return $result;
    }
}
