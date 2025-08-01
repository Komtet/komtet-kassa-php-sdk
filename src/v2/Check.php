<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

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
    private $intent;

    /**
     * @var bool
     */
    private $shouldPrint = false;

    /**
     * @var Buyer
     */
    private $buyer;

    /**
     * @var Company
     */
    private $company;

    /**
     * @var Position[]
     */
    private $positions = [];

    /**
     * @var Payment[]
     */
    private $payments = [];

    /**
     * @var Cashier
     */
    private $cashier;

    /**
     * @var AdditionalUserProps
     */
    private $additionalUserProps;

    /**
     * @var SectoralCheckProps[]
     */
    private $sectoralCheckProps = [];

    /**
     * @var OperatingCheckProps
     */
    private $operatingCheckProps;

    /**
     * @var string
     */
    private $additionalCheckProps;

    /**
     * @var bool
     */
    private $internet;

    /**
     * @var CashlessPayment[]
     */
    private $cashlessPayments = [];

    /**
     * @var int
     */
    private $timeZone;

    /**
     * @var string
     */
    private $callbackUrl;

    /**
     * @param string $id An unique ID provided by an online store
     * @param string $intent Check::INTENT_SELL, Check::INTENT_SELL_RETURN, Check::INTENT_BUY, or Check::INTENT_BUY_RETURN
     * @param Buyer $buyer
     * @param Company $company
     *
     * @return Check
     */
    public function __construct($id, $intent, Buyer $buyer, Company $company)
    {
        $this->id = $id;
        $this->intent = $intent;
        $this->buyer = $buyer;
        $this->company = $company;
    }

    /**
     * @param string $id
     * @param Buyer $buyer
     * @param Company $company
     *
     * @return Check
     */
    public static function createSell($id, Buyer $buyer, Company $company)
    {
        return new static($id, static::INTENT_SELL, $buyer, $company);
    }

    /**
     * @param string $id
     * @param Buyer $buyer
     * @param Company $company
     *
     * @return Check
     */
    public static function createSellReturn($id, Buyer $buyer, Company $company)
    {
        return new static($id, static::INTENT_SELL_RETURN, $buyer, $company);
    }

    /**
     * @param string $id
     * @param Buyer $buyer
     * @param Company $company
     *
     * @return Check
     */
    public static function createBuy($id, Buyer $buyer, Company $company)
    {
        return new static($id, static::INTENT_BUY, $buyer, $company);
    }

    /**
     * @param string $id
     * @param Buyer $buyer
     * @param Company $company
     *
     * @return Check
     */
    public static function createBuyReturn($id, Buyer $buyer, Company $company)
    {
        return new static($id, static::INTENT_BUY_RETURN, $buyer, $company);
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
    public function setAdditionalUserProps(AdditionalUserProps $additional_user_props)
    {
        $this->additionalUserProps = $additional_user_props;

        return $this;
    }

    /**
     * @param SectoralCheckProps $sectoralCheckProps
     *
     * @return Check
     */
    public function setSectoralCheckProps(SectoralCheckProps $sectoral_check_props)
    {
        $this->sectoralCheckProps[] = $sectoral_check_props;

        return $this;
    }

    /**
     * @param OperatingCheckProps $operatingCheckProps
     *
     * @return Check
     */
    public function setOperatingCheckProps(OperatingCheckProps $operating_check_props)
    {
        $this->operatingCheckProps = $operating_check_props;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return Check
     */
    public function setInternet($value)
    {
        $this->internet = (bool) $value;

        return $this;
    }

    /**
     * @param CashlessPayment $cashlessPayment
     *
     * @return Check
     */
    public function addCashlessPayment(CashlessPayment $cashlessPayment)
    {
        $this->cashlessPayments[] = $cashlessPayment;

        return $this;
    }

    /**
     * @param int $timeZone One of TimeZone::TIME_ZONE_* constants
     *
     * @return Check
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * @return int|float
     */
    public function getTotalPositionsSum()
    {
        $positionsTotal = 0;
        foreach( $this->positions as $position )
        {
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

        foreach( $checkPositions as $index => $position )
        {
            if ($index < $positionsCount-1) {
                $positionPricePercent = $position->getTotal() / $positionsTotal * 100;
                $curPositionDiscount = round($checkDiscount * $positionPricePercent / 100, 2);
                $accumulatedDiscount += $curPositionDiscount;
            }
            else {
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
        $this->callbackUrl = $callback_url;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'external_id' => $this->id,
            'intent' => $this->intent,
            'print' => $this->shouldPrint,
            'client' => $this->buyer->asArray(),
            'company' => $this->company->asArray(),
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

        if ($this->cashier !== null) {
            $result['cashier'] = $this->cashier->asArray();
        }

        if ($this->additionalCheckProps !== null) {
            $result['additional_check_props'] = $this->additionalCheckProps;
        }

        if ($this->additionalUserProps !== null) {
            $result['additional_user_props'] = $this->additionalUserProps->asArray();
        }

        if ($this->sectoralCheckProps !== null) {
            $result['sectoral_check_props'] = array_map(
                function($sectoral_check_props) {
                    return $sectoral_check_props->asArray();
                },
                $this->sectoralCheckProps
            );
        }

        if ($this->operatingCheckProps !== null) {
            $result['operating_check_props'] = $this->operatingCheckProps->asArray();
        }

        if ($this->internet !== null) {
            $result['internet'] = $this->internet;
        }

        if (!empty($this->cashlessPayments)) {
            $result['cashless_payments'] = array_map(
                function ($cashlessPayment) {
                    return $cashlessPayment->asArray();
                },
                $this->cashlessPayments
            );
        }

        if ($this->timeZone !== null) {
            $result['timezone'] = $this->timeZone;
        }

        if ($this->callbackUrl !== null) {
            $result['callback_url'] = $this->callbackUrl;
        }

        return $result;
    }
}
