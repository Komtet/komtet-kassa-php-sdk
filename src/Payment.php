<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk;

class Payment
{
    const TYPE_CARD = 'card';
    const TYPE_CASH = 'cash';

    /**
     * @var string
     */
    private $type;

    /**
     * @var int|float
     */
    private $sum;

    /**
     * @param string $type Form of payment
     * @param int|float $sum Amount
     *
     * @return Payment
     */
    public function __construct($type, $sum)
    {
        $this->type = $type;
        $this->sum = $sum;
    }

    /**
     * @param int|float $sum Amount
     *
     * @return Payment
     */
    public static function createCard($sum)
    {
        return new static(static::TYPE_CARD, $sum);
    }

    /**
     * @param int|float $sum Amount
     *
     * @return Payment
     */
    public static function createCash($sum)
    {
        return new static(static::TYPE_CASH, $sum);
    }

    /**
     * @return int|float
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'type' => $this->type,
            'sum' => $this->sum
        ];
    }
}
