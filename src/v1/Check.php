<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v1;

class Check
{
    const INTENT_SELL = 'sell';
    const INTENT_SELL_RETURN = 'sellReturn';
    const INTENT_BUY = 'buy';
    const INTENT_BUY_RETURN = 'buyReturn';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $userContact;

    /**
     * @var string
     */
    private $intent;

    /**
     * @var int
     */
    private $taxSystem;

    /**
     * @var string
     */
    private $paymentAddress;

    /**
     * @var string
     */
    private $placeAddress;

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
     * @var Buyer
     */
    private $buyer;

    /**
     * @var Cashier
     */
    private $cashier;

    /**
     * @var string
     */
    private $additionalCheckProps;

    /**
     * @var AdditionalUserProps
     */
    private $additionalUserProps;

    /**
     * @var string
     */
    private $callback_url;

    /**
     * @param string $id An unique ID provided by an online store
     * @param string $userContact User E-Mail or phone
     * @param string $intent Check::INTENT_SELL, Check::INTENT_SELL_RETURN, Check::INTENT_BUY, or Check::INTENT_BUY_RETURN
     * @param int    $taxSystem See Check::TS_*
     * @param string $paymentAddress
     * @param string $placeAddress
     *
     * @return Check
     */
    public function __construct($id, $userContact, $intent, $taxSystem, $paymentAddress = null, $placeAddress = null)
    {
        $this->id = $id;
        $this->userContact = $userContact;
        $this->intent = $intent;
        $this->taxSystem = $taxSystem;
        $this->paymentAddress = $paymentAddress;
        $this->placeAddress = $placeAddress;
    }

    /**
     * @param string $id
     * @param string $userContact
     * @param int    $taxSystem
     * @param string $paymentAddress
     * @param string $placeAddress
     *
     * @return Check
     */
    public static function createSell($id, $userContact, $taxSystem, $paymentAddress = null, $placeAddress = null)
    {
        return new static($id, $userContact, static::INTENT_SELL, $taxSystem, $paymentAddress, $placeAddress);
    }

    /**
     * @param string $id
     * @param string $userContact
     * @param int    $taxSystem
     * @param string $paymentAddress
     * @param string $placeAddress
     *
     * @return Check
     */
    public static function createSellReturn($id, $userContact, $taxSystem, $paymentAddress = null, $placeAddress = null)
    {
        return new static(
            $id,
            $userContact,
            static::INTENT_SELL_RETURN,
            $taxSystem,
            $paymentAddress,
            $placeAddress
        );
    }

    /**
     * @param string $id
     * @param string $userContact
     * @param int    $taxSystem
     * @param string $paymentAddress
     * @param string $placeAddress
     *
     * @return Check
     */
    public static function createBuy($id, $userContact, $taxSystem, $paymentAddress = null, $placeAddress = null)
    {
        return new static($id, $userContact, static::INTENT_BUY, $taxSystem, $paymentAddress, $placeAddress);
    }

    /**
     * @param string $id
     * @param string $userContact
     * @param int    $taxSystem
     * @param string $paymentAddress
     * @param string $placeAddress
     *
     * @return Check
     */
    public static function createBuyReturn($id, $userContact, $taxSystem, $paymentAddress = null, $placeAddress = null)
    {
        return new static(
            $id,
            $userContact,
            static::INTENT_BUY_RETURN,
            $taxSystem,
            $paymentAddress,
            $placeAddress
        );
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
     * @param Buyer $buyer
     *
     * @return Check
     */
    public function addBuyer(Buyer $buyer)
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @param Cashier $cashier
     *
     * @return Check
     */
    public function addCashier(Cashier $cashier)
    {
        $this->cashier = $cashier;

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
     * @param string $value
     *
     * @return Check
     */
    public function setAdditionalCheckProps($value)
    {
        $this->additionalCheckProps = $value;

        return $this;
    }

    /**
     * @param AdditionalUserProps $additionalUserProps
     *
     * @return Check
     */
    public function setAdditionalUserProps($additionalUserProps)
    {
        $this->additionalUserProps = $additionalUserProps;

        return $this;
    }

    /**
     * @return int|float
     */
    public function getTotalPositionsSum()
    {
        $positionsTotal = 0;
        foreach ($this->positions as $position) {
            $positionsTotal += $position->getTotal();
        }

        return $positionsTotal;
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     *
     * Применение к позициям единой общей скидки на чек (например скидочного купона)
     *
     * @param float $checkDiscount
     *
     * @return Check
     */
    public function applyDiscount($checkDiscount)
    {
        $positionsTotal = $this->getTotalPositionsSum();
        $checkPositions = $this->getPositions();

        $positionsCount = count($checkPositions);
        $accumulatedDiscount = 0;

        foreach ($checkPositions as $index => $position) {
            if ($index < $positionsCount - 1) {
                $positionPricePercent = $position->getTotal() / $positionsTotal * 100;
                $curPositionDiscount = round($checkDiscount * $positionPricePercent / 100, 2);
                $accumulatedDiscount += $curPositionDiscount;
            } else {
                $curPositionDiscount = round($checkDiscount - $accumulatedDiscount, 2);
            }

            $position->setTotal($position->getTotal() - $curPositionDiscount);
        }

        return $this;
    }

    /**
     * @param string $callback_url callback url for Check
     *
     */
    public function setCallbackUrl($callback_url)
    {
        $this->callback_url = $callback_url;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'external_id' => $this->id,
            'user' => $this->userContact,
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

        if ($this->buyer !== null) {
            $result['client'] = $this->buyer->asArray();
        }

        if ($this->cashier !== null) {
            $result['cashier'] = $this->cashier->asArray();
        }

        if ($this->paymentAddress !== null) {
            $result['payment_address'] = $this->paymentAddress;
        }

        if ($this->placeAddress !== null) {
            $result['place_address'] = $this->placeAddress;
        }

        if ($this->additionalCheckProps !== null) {
            $result['additional_check_props'] = $this->additionalCheckProps;
        }

        if ($this->additionalUserProps !== null) {
            $result['additional_user_props'] = $this->additionalUserProps->asArray();
        }

        if ($this->callback_url !== null) {
            $result['callback_url'] = $this->callback_url;
        }

        return $result;
    }
}
