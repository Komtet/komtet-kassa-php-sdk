<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk;

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
        $this->sum = $sum;
        $this->rate = static::normalizeRate($rate);
    }

    /**
     * Calculates from price
     *
     * @param int|float $price Price in RUB
     * @param string|int|float $rate See Vat::RATE_*
     *
     * @return Vat
     */
    public static function calculate($price, $rate)
    {
        if (is_int($price)) {
            $price = floatval($price);
        } elseif (!is_float($price)) {
            throw new \InvalidArgumentException(sprintf('Unexpected price type: expects int or float, %s given', gettype($price)));
        }
        $price = floatval($price);
        $rate = static::normalizeRate($rate);
        switch ($rate) {
            case static::RATE_0:
                $sum = 0.0;
                break;
            case static::RATE_10:
                $sum = 10.0 * $price / 100;
                break;
            case static::RATE_18:
                $sum = 18.0 * $price / 100;
                break;
            case static::RATE_110:
                $sum = $price * (10.0 / 110.0);
                break;
            case static::RATE_118:
                $sum = $price * (18.0 / 118.0);
                break;
            default:
                throw new \LogicException(sprintf('Unable to calculate for rate "%s"', $rate));
        }
        return new static($sum, $rate);
    }

    private static function normalizeRate($rate)
    {
        if (!is_string($rate)) {
            $rate = (string) $rate;
        }
        $rate = str_replace(['0.', '%'], '', $rate);
        switch ($rate) {
            case '10/110':
                $rate = static::RATE_110;
                break;
            case '18/118':
                $rate = static::RATE_118;
                break;
            default:
                if (!in_array($rate, static::getAvailableRates())) {
                    throw new \InvalidArgumentException(sprintf('Unknown VAT rate: %s', $rate));
                }
        }
        return $rate;
    }

    private static function getAvailableRates()
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
