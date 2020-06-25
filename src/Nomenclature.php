<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk;

/**
* Код товара (маркировка)
*/
class Nomenclature
{
    /**
     * @var array
     */
    private $nomenclature_code;


    public function __construct($code=null, $hex_code=null)
    {
        $this->nomenclature_code = [];

        if ($code !== null) {
            $this->setStrCode($code);
        }

        if ($hex_code !== null) {
            $this->setHexCode($hex_code);
        }
    }

    /**
     * @param string $code
     *
     */
    public function setStrCode($code) 
    {
        $this->nomenclature_code['code'] = $code;
    }

    /**
     * @param string $code
     *
     */
    public function setHexCode($code)
    {
        $this->nomenclature_code['hex_code'] = $code;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->nomenclature_code;
    }
}
