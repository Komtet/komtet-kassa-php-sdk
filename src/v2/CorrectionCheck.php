<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

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
     * @var Buyer
     */
    private $buyer;

    /**
     * @var Company
     */
    private $company;

    /**
     * @var CorrectionInfo
     */
    private $correctionInfo;

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
     * @var string
     */
    private $additionalCheckProps;

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
     * @var AuthorisedPerson
     */
    private $authorisedPerson;

    /**
     * @var bool
     */
    private $internet;

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
     * @param string $intent One of CorrectionCheck::INTENT_* constants
     * @param Company $company
     * @param CorrectionInfo $correction_info Correction data
     *
     * @return CorrectionCheck
     */
    public function __construct($id, $intent, Company $company, CorrectionInfo $correction_info)
    {
        $this->id = $id;
        $this->intent = $intent;
        $this->company = $company;
        $this->correctionInfo = $correction_info;
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param Company $company
     * @param CorrectionInfo $correction_info Correction data
     *
     * @return CorrectionCheck
     */
    public static function createSellCorrection($id, Company $company, CorrectionInfo $correction_info)
    {
        return new static($id, static::INTENT_SELL_CORRECTION, $company, $correction_info);
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param Company $company
     * @param CorrectionInfo $correction_info Correction data
     *
     * @return CorrectionCheck
     */
    public static function createSellReturnCorrection($id, Company $company, CorrectionInfo $correction_info)
    {
        return new static($id, static::INTENT_SELL_RETURN_CORRECTION, $company, $correction_info);
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param Company $company
     * @param CorrectionInfo $correction_info Correction data
     *
     * @return CorrectionCheck
     */
    public static function createBuyCorrection($id, Company $company, CorrectionInfo $correction_info)
    {
        return new static($id, static::INTENT_BUY_CORRECTION, $company, $correction_info);
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param Company $company
     * @param CorrectionInfo $correction_info Correction data
     *
     * @return CorrectionCheck
     */
    public static function createBuyReturnCorrection($id, Company $company, CorrectionInfo $correction_info)
    {
        return new static($id, static::INTENT_BUY_RETURN_CORRECTION, $company, $correction_info);
    }

    /**
     * @param Buyer $buyer
     *
     * @return CorrectionCheck
     */
    public function setBuyer(Buyer $buyer)
    {
        $this->buyer = $buyer;

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
     * @param SectoralCheckProps $sectoralCheckProps
     *
     * @return CorrectionCheck
     */
    public function setSectoralCheckProps(SectoralCheckProps $sectoral_check_props)
    {
        $this->sectoralCheckProps[] = $sectoral_check_props;

        return $this;
    }

    /**
     * @param OperatingCheckProps $operatingCheckProps
     *
     * @return CorrectionCheck
     */
    public function setOperatingCheckProps(OperatingCheckProps $operating_check_props)
    {
        $this->operatingCheckProps = $operating_check_props;

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
            'company' => $this->company->asArray(),
            'correction_info' => $this->correctionInfo->asArray(),
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
            )
        ];

        if ($this->buyer !== null) {
            $result['client'] = $this->buyer->asArray();
        }

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

        if ($this->authorisedPerson !== null) {
            $result['authorised_person'] = $this->authorisedPerson->asArray();
        }

        if ($this->internet !== null) {
            $result['internet'] = $this->internet;
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
