<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v1;

class CorrectionCheck
{
    const INTENT_SELL_CORRECTION = 'sellCorrection';
    const INTENT_SELL_RETURN_CORRECTION = 'sellReturnCorrection';
    const INTENT_BUY_CORRECTION = 'buyCorrection';
    const INTENT_BUY_RETURN_CORRECTION = 'buyReturnCorrection';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $intent;

    /**
     * @var int
     */
    private $taxSystem;

    /**
     * @var Position[]
     */
    private $positions = [];

    /**
     * @var bool
     */
    private $shouldPrint = false;

    /**
     * @var Correction
     */
    private $correction;

    /**
     * @var AuthorisedPerson
     */
    private $authorisedPerson;

    /**
     * @var Cashier
     */
    private $cashier;

    /**
     * @var Buyer
     */
    private $buyer;

    /**
     * @var string
     */
    private $paymentAddress;

    /**
     * @var string
     */
    private $placeAddress;

    /**
     * @var string
     */
    private $additionalCheckProps;

    /**
     * @var AdditionalUserProps
     */
    private $additionalUserProps;

    /**
     * @var Payment[]
     */
    private $payments = [];

    /**
     * @var bool
     */
    private $internet;

    /**
     * @var string
     */
    private $callbackUrl;

    /**
     * @param string $id An unique ID provided by an online store
     * @param string $intent One of CorrectionCheck::INTENT_* constants
     * @param int $taxSystem One of TaxSystem::* constants
     * @param Correction $correction Correction data
     *
     * @return CorrectionCheck
     */
    public function __construct($id, $intent, $taxSystem, Correction $correction, $paymentAddress=null, $placeAddress=null)
    {
        $this->id = $id;
        $this->intent = $intent;
        $this->taxSystem = $taxSystem;
        $this->correction = $correction;
        $this->paymentAddress = $paymentAddress;
        $this->placeAddress = $placeAddress;
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param int $taxSystem One of TaxSystem::* constants
     * @param Correction $correction Correction data
     *
     * @return CorrectionCheck
     */
    public static function createSellCorrection($id, $taxSystem, Correction $correction)
    {
        return new static($id, static::INTENT_SELL_CORRECTION, $taxSystem, $correction);
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param int $taxSystem One of TaxSystem::* constants
     * @param Correction $correction Correction data
     *
     * @return CorrectionCheck
     */
    public static function createSellReturnCorrection($id, $taxSystem, Correction $correction)
    {
        return new static($id, static::INTENT_SELL_RETURN_CORRECTION, $taxSystem, $correction);
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param int $taxSystem One of TaxSystem::* constants
     * @param Correction $correction Correction data
     *
     * @return CorrectionCheck
     */
    public static function createBuyCorrection($id, $taxSystem, Correction $correction)
    {
        return new static($id, static::INTENT_BUY_CORRECTION, $taxSystem, $correction);
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param int $taxSystem One of TaxSystem::* constants
     * @param Correction $correction Correction data
     *
     * @return CorrectionCheck
     */
    public static function createBuyReturnCorrection($id, $taxSystem, Correction $correction)
    {
        return new static($id, static::INTENT_BUY_RETURN_CORRECTION, $taxSystem, $correction);
    }

    /**
     * @param bool $value
     *
     * @return CorrectionCheck
     */
    public function setShouldPrint($value)
    {
        $this->shouldPrint = (bool) $value;

        return $this;
    }

    /**
     * @param Position $position
     *
     * @return CorrectionCheck
     */
    public function addPosition(Position $position)
    {
        $this->positions[] = $position;

        return $this;
    }

    /**
     * @param Payment $payment
     *
     * @return CorrectionCheck
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
     * @return CorrectionCheck
     */
    public function addCashier(Cashier $cashier)
    {
        $this->cashier = $cashier;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return CorrectionCheck
     */
    public function setAdditionalCheckProps($value)
    {
        $this->additionalCheckProps = $value;

        return $this;
    }

    /**
     * @param AdditionalUserProps $additionalUserProps
     *
     * @return CorrectionCheck
     */
    public function setAdditionalUserProps(AdditionalUserProps $additional_user_props)
    {
        $this->additionalUserProps = $additional_user_props;

        return $this;
    }

    /**
     * @param AuthorisedPerson $authorised_person
     *
     * @return CorrectionCheck
     */
    public function setAuthorisedPerson(AuthorisedPerson $authorised_person)
    {
        $this->authorisedPerson = $authorised_person;

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
     * @param string $callback_url callback url for CorrectionCheck
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
            'correction' => $this->correction->asArray()
        ];

        if ($this->buyer !== null) {
            $result['client'] = $this->buyer->asArray();
        }

        if ($this->shouldPrint !== null) {
            $result['print'] = $this->shouldPrint;
        }

        if ($this->authorisedPerson !== null) {
            $result['authorised_person'] = $this->authorisedPerson->asArray();
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

        if ($this->internet !== null) {
            $result['internet'] = $this->internet;
        }

        if ($this->callbackUrl !== null) {
            $result['callback_url'] = $this->callbackUrl;
        }

        return $result;
    }
}
