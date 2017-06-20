<?php

/**
 * This file is part of the motmom/komtet-kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Motmom\KomtetKassaSdk;

class Vat
{
    /**
     * Without VAT
     */
    const TYPE_NO = 'no';

    /**
     * 0%
     */
    const TYPE_0 = '0';

    /**
     * 10%
     */
    const TYPE_10 = '10';

    /**
     * 18%
     */
    const TYPE_18 = '18';

    /**
     * 10/110
     */
    const TYPE_110 = '110';

    /**
     * 18/118
     */
    const TYPE_118 = '118';

    private $mode;
    private $sum;
    private $type;

    /**
     * @param int|float $sum Amount in RUB
     * @param string|int|float $type See Vat::TYPE_*
     *
     * @return Vat
     */
    public function __construct($sum, $type)
    {
        if (!is_int($sum) && !is_float($sum)) {
            throw new \InvalidArgumentException(sprintf('Unexpected sum type: expects int or float, %s given', gettype($sum)));
        }
        if (!is_string($type)) {
            $type = (string) $type;
        }
        $type = str_replace(['0.', '%'], '', $type);
        switch ($type) {
            case '10/100':
                $type = static::TYPE_110;
                break;
            case '18/118':
                $type = static::TYPE_118;
                break;
            default:
                if (!in_array($type, $this->getAvailableTypes())) {
                    throw new \InvalidArgumentException(sprintf('Unknown VAT type: %s', $type));
                }
        }
        $this->sum = $sum;
        $this->type = $type;
    }

    private function getAvailableTypes()
    {
        return [
            static::TYPE_NO,
            static::TYPE_0,
            static::TYPE_10,
            static::TYPE_18,
            static::TYPE_110,
            static::TYPE_118,
        ];
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'sum' => $this->sum,
            'number' => $this->type
        ];
    }
}
