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
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $byte_code;


    public function __construct($nomenclature_type, $code, $byte_code=null)
    {   
        $this->type = $nomenclature_type;
        $this->code = $code;
        $this->byte_code = $byte_code;
    }

    /**
     * @return array
     */
    public function asArray()
    {   
        $result = [
            'type' => $this->type,
            'code' => $this->code
        ];

        if ($this->byte_code !== null) {
            $result['byte_code'] = $this->byte_code;
        }

        return $result;
    }
}
