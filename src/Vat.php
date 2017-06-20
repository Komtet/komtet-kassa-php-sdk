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
    const MODE_POSITION = 'position';
    const MODE_UNIT = 'unit';

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
    const TYPE_18 = '18%';

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
     * @param string $mode Vat::MODE_POSITION or Vat::MODE_UNIT
     * @param int|float $sum Amount in RUB
     * @param string $type Vat::TYPE_*
     *
     * @return Vat
     */
    public function __construct($mode, $sum, $type)
    {
        $this->mode = $mode;
        $this->sum = $sum;
        $this->type = $type;
    }

    /**
     * @param int|float $sum Amount in RUB
     * @param string $type Vat::TYPE_*
     *
     * @return Vat
     */
    public static function createPosition($sum, $number)
    {
        return new static(static::MODE_POSITION, $sum, $number);
    }

    /**
     * @param int|float $sum Amount in RUB
     * @param string $type Vat::TYPE_*
     *
     * @return Vat
     */
    public static function createUnit($sum, $number)
    {
        return new static(static::MODE_UNIT, $sum, $number);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'mode' => $this->mode,
            'sum' => $this->sum,
            'number' => $this->type
        ];
    }
}
