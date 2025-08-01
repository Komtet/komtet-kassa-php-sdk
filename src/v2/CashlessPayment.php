<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class CashlessPayment
{
    /**
     * @var int|float
     */
    private $sum;

    /**
     * @var int
     */
    private $method;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $additionalInfo;

    /**
     * @param int|float $sum Сумма оплаты безналичными
     * @param int $method Признак способа оплаты безналичными
     * @param string $id Идентификатор безналичной оплаты
     *
     * @return CashlessPayment
     */
    public function __construct($sum, $method, $id)
    {
        $this->sum = $sum;
        $this->method = $method;
        $this->id = $id;
    }

    /**
     * @param string $additionalInfo Дополнительные сведения о безналичной оплате
     *
     * @return CashlessPayment
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'sum' => $this->sum,
            'method' => $this->method,
            'id' => $this->id
        ];

        if ($this->additionalInfo !== null) {
            $result['additionalInfo'] = $this->additionalInfo;
        }

        return $result;
    }
}
