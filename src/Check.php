<?php

/**
 * This file is part of the motmom/komtet-kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Motmom\KomtetKassaSdk;

class Check
{
    const INTENT_SELL = 'sell';
    const INTENT_SELL_RETURN = 'sellReturn';

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
     *
     * @return Check
     */
    public function __construct($id, $email, $intent)
    {
        $this->id = $id;
        $this->email = $email;
        $this->intent = $intent;
    }

    /**
     * @param string $id
     * @param string $email
     *
     * @return Check
     */
    public static function createSell($id, $email)
    {
        return new static($id, $email, static::INTENT_SELL);
    }

    /**
     * @param string $id
     * @param string $email
     *
     * @return Check
     */
    public static function createSellReturn($id, $email)
    {
        return new static($id, $email, static::INTENT_SELL_RETURN);
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
