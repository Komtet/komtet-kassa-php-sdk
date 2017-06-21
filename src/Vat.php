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
    const RATE_NO = 'no';

    /**
     * 0%
     */
    const RATE_0 = '0';

    /**
     * 10%
     */
    const RATE_10 = '10';

    /**
     * 18%
     */
    const RATE_18 = '18';

    /**
     * 10/110
     */
    const RATE_110 = '110';

    /**
     * 18/118
     */
    const RATE_118 = '118';

    private $mode;
    private $sum;
    private $rate;

    /**
     * @param int|float $sum Amount in RUB
     * @param string|int|float $rate See Vat::RATE_*
     *
     * @return Vat
     */
    public function __construct($sum, $rate)
    {
        if (!is_int($sum) && !is_float($sum)) {
            throw new \InvalidArgumentException(sprintf('Unexpected sum type: expects int or float, %s given', gettype($sum)));
        }
        if (!is_string($rate)) {
            $rate = (string) $rate;
        }
        $rate = str_replace(['0.', '%'], '', $rate);
        switch ($rate) {
            case '10/100':
                $rate = static::RATE_110;
                break;
            case '18/118':
                $rate = static::RATE_118;
                break;
            default:
                if (!in_array($rate, $this->getAvailableRates())) {
                    throw new \InvalidArgumentException(sprintf('Unknown VAT rate: %s', $rate));
                }
        }
        $this->sum = $sum;
        $this->rate = $rate;
    }

    private function getAvailableRates()
    {
        return [
            static::RATE_NO,
            static::RATE_0,
            static::RATE_10,
            static::RATE_18,
            static::RATE_110,
            static::RATE_118,
        ];
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'sum' => $this->sum,
            'number' => $this->rate
        ];
    }
}
