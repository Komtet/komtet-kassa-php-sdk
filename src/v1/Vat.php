<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v1;

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
     * 5%
     */
    const RATE_5 = '5';

    /**
     * 7%
     */
    const RATE_7 = '7';

    /**
     * 10%
     */
    const RATE_10 = '10';

    /**
     * 20%
     */
    const RATE_20 = '20';

    /**
     * 22%
     */
    const RATE_22 = '22';

    /**
     * 5/105
     */
    const RATE_105 = '105';

    /**
     * 7/107
     */
    const RATE_107 = '107';

    /**
     * 10/110
     */
    const RATE_110 = '110';

    /**
     * 20/120
     */
    const RATE_120 = '120';

    /**
     * 22/122
     */
    const RATE_122 = '122';

    private $rate;

    /**
     * @param string|int|float $rate See Vat::RATE_*
     *
     * @return Vat
     */
    public function __construct($rate)
    {
        if (is_string($rate) && strpos($rate, '.') !== false) {
            $rate = (float)$rate;
        }
    
        if (is_float($rate)) {
            if ($rate < 1) {
                $rate = (string)(int)($rate * 100);
            } else {
                $rate = (string)(int)$rate;
            }
        }
        else {
            $rate = str_replace('%', '', (string)$rate);
        }

        $rateMapping = [
            '5/105' => static::RATE_105,
            '7/107' => static::RATE_107,
            '10/110' => static::RATE_110,
            '20/120' => static::RATE_120,
            '22/122' => static::RATE_122,
        ];

        $rate = $rateMapping[$rate] ?? $rate;

        if (!in_array($rate, [
            static::RATE_NO,
            static::RATE_0,
            static::RATE_5,
            static::RATE_7,
            static::RATE_10,
            static::RATE_20,
            static::RATE_22,
            static::RATE_105,
            static::RATE_107,
            static::RATE_110,
            static::RATE_120,
            static::RATE_122
        ])) {
            throw new \InvalidArgumentException(sprintf('Unknown VAT rate: %s', $rate));
        }

        $this->rate = $rate;
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }
}
