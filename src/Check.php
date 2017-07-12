<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk;

class Check
{
    const INTENT_SELL = 'sell';
    const INTENT_SELL_RETURN = 'sellReturn';

    /**
     * Common tax system
     */
    const TS_COMMON = 0;

    /**
     * Simplified tax system: Income
     */
    const TS_SIMPLIFIED_IN = 1;

    /**
     * Simplified tax system: Income - Outgo
     */
    const TS_SIMPLIFIED_IN_OUT = 2;

    /**
     * An unified tax on imputed income
     */
    const TS_UTOII = 3;

    /**
     * Unified social tax
     */
    const TS_UST = 4;

    /**
     * Patent
     */
    const TS_PATENT = 5;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $intent;

    /**
     * @var int
     */
    private $taxSystem;

    /**
     * @var bool
     */
    private $shouldPrint = false;

    /**
     * @var Payment[]
     */
    private $payments = [];

    /**
     * @var Position[]
     */
    private $positions = [];

    /**
     * @param string $id An unique ID provided by an online store
     * @param string $email User E-Mail
     * @param string $intent Check::INTENT_SELL or Check::INTENT_SELL_RETURN
     * @param int    $taxSystem See Check::TS_*
     *
     * @return Check
     */
    public function __construct($id, $email, $intent, $taxSystem)
    {
        $this->id = $id;
        $this->email = $email;
        $this->intent = $intent;
        $this->taxSystem = $taxSystem;
    }

    /**
     * @param string $id
     * @param string $email
     * @param int    $taxSystem
     *
     * @return Check
     */
    public static function createSell($id, $email, $taxSystem)
    {
        return new static($id, $email, static::INTENT_SELL, $taxSystem);
    }

    /**
     * @param string $id
     * @param string $email
     * @param int    $taxSystem
     *
     * @return Check
     */
    public static function createSellReturn($id, $email, $taxSystem)
    {
        return new static($id, $email, static::INTENT_SELL_RETURN, $taxSystem);
    }

    /**
     * @param bool $value
     *
     * @return Check
     */
    public function setShouldPrint($value)
    {
        $this->shouldPrint = (bool) $value;

        return $this;
    }

    /**
     * @param Payment $payment
     *
     * @return Check
     */
    public function addPayment(Payment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * @param Position $position
     *
     * @return Check
     */
    public function addPosition(Position $position)
    {
        $this->positions[] = $position;

        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'task_id' => $this->id,
            'user' => $this->email,
            'print' => $this->shouldPrint,
            'intent' => $this->intent,
            'sno' => $this->taxSystem,
            'payments' => array_map(
                function ($payment) {
                    return $payment->asArray();
                },
                $this->payments
            ),
            'positions' => array_map(
                function ($position) {
                    return $position->asArray();
                },
                $this->positions
            ),
        ];
    }
}
