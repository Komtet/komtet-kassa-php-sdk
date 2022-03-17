<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

/**
* Код товара (маркировка)
*/
class MarkCode
{
    /**
     * Код товара, формат которого не идентифицирован, как один из реквизитов
     */
    const UNKNOWN = 'unknown';

    /**
     * Код товара в формате EAN-8
     */
    const EAN8 = 'ean8';

    /**
     * Код товара в формате EAN-13
     */
    const EAN13 = 'ean13';

    /**
     * Код товара в формате ITF-14
     */
    const ITF14 = 'itf14';

    /**
     * Код товара в формате GS1, нанесенный на товар, подлежащий (не подлежащий) маркировке средствами идентификации
     */
    const GS1M = 'gs1m';

    /**
     * Код товара в формате короткого кода маркировки, нанесенный на товар, подлежащий маркировке средствами идентификации
     */
    const SHORT = 'short';

    /**
     * Контрольно-идентификационный знак мехового изделия
     */
    const FUR = 'fur';

    /**
     * Код товара в формате ЕГАИС-2.0
     */
    const EGAIS20 = 'egais20';

    /**
     * Код товара в формате ЕГАИС-3.0
     */
    const EGAIS30 = 'egais30';

    // /**
    //  * @var string
    //  */
    // private $mark_type;

    // /**
    //  * @var string
    //  */
    // private $value;

    /**
     * @var array
     */
    private $mark_code;


    public function __construct($mark_type, $value)
    {
        # $this->mark_type = $mark_type;
        # $this->value = $value;
        $this->mark_code = [
            $mark_type => $value
        ];

        // $this->mark_code = [];
        // $this->setMarkCode($mark_type, $value);
    }

    // /**
    //  * @param string $mark_type
    //  * @param string $value
    //  * 
    //  */
    // public function setMarkCode($mark_type, $value) 
    // {
    //     $this->mark_code[$mark_type] = $value;
    // }


    /**
     * @return array
     */
    public function asArray()
    {
        return $this->mark_code;
    }
}
