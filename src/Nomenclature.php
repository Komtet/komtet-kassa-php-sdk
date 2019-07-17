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
     * Меховые изделия
     */
    const FURS = 'furs';

    /**
     * Лекарства
     */
    const MEDICINES = 'medicines';

    /**
     * Табачная продукция
     */
    const TOBACCO = 'tobacco';

    /**
     * Обувь
     */
    const SHOES = 'shoes';

    /**
     * @var array
     */
    private $nomenclature_code;


    public function __construct($nomenclature_type, $gtin, $serial_number)
    {
        $this->nomenclature_code = [
            'type' => $nomenclature_type,
            'gtin' => $gtin,
            'serial' => $serial_number
        ];
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->nomenclature_code;
    }
}
