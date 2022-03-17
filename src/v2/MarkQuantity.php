<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class MarkQuantity
{
    /**
     * @var int|float
     */
    private $numerator;

    /**
     * @var int|float
     */
    private $denominator;


    /**
     * @param int|float $numerator The numerator of the fractional part of the subject of calculation
     * @param int|float $denominator The denominator of the fractional part of the subject of calculation
     *
     * @return MarkQuantity
     */
    public function __construct($numerator, $denominator)
    {
        $this->numerator = $numerator;
        $this->denominator = $denominator;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'numerator' => $this->numerator,
            'denominator' => $this->denominator,
        ];
    }
}
