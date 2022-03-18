<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class Position
{
    /**
     * @var string
     */
    private $id = null;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int|float
     */
    private $price;

    /**
     * @var int|float
     */
    private $quantity;

    /**
     * @var int|float
     */
    private $total;

    /**
     * @var int
     */
    private $measure;

    /**
     * @var string
     */
    private $paymentMethod;

    /**
     * @var string
     */
    private $paymentObject;

    /**
     * @var Vat
     */
    private $vat;

    /**
     * @var string
     */
    private $userData = null;

    /**
     * @var int|float
     */
    private $excise = null;

    /**
     * @var string
     */
    private $countryCode = null;

    /**
     * @var string
     */
    private $declarationNumber = null;

    /**
     * @var SectoralItemProps 
     */
    private $sectoralItemProps = null;

    /**
     * @var Agent
     */
    private $agent = null;

    /**
     * @var MarkQuantity 
     */
    private $markQuantity = null;

    /**
     * @var MarkCode 
     */
    private $markCode = null;


    /**
     * @param string $name Item name
     * @param int|float $price Item price
     * @param int|float $quantity Item quanitity
     * @param int|float $total Total cost
     * @param int $measure measure
     * @param string $paymentMethod payment method
     * @param string $paymentObject payment object
     * @param Vat $vat VAT
     *
     * @return Position
     */
    public function __construct($name, $price, $quantity, $total, Vat $vat, $measure, $paymentMethod, $paymentObject)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->total = $total;
        $this->vat = $vat;
        $this->measure = $measure;
        $this->paymentMethod = $paymentMethod;
        $this->paymentObject = $paymentObject;
    }

    /**
     * @param string|null $value
     *
     * @return Position
     */
    public function setId($value)
    {
        $this->id = $value;

        return $this;
    }

    /**
     * @param int|float $value
     *
     * @return Position
     */
    public function setExcise($value)
    {
        $this->excise = $value;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return Position
     */
    public function setCountryCode($value)
    {
        $this->countryCode = $value;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return Position
     */
    public function setDeclarationNumber($value)
    {
        $this->declarationNumber = $value;

        return $this;
    }

    /**
     * @param float $total
     *
     * @return Position
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return int|float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param string $value
     *
     * @return Position
     */
    public function setUserData($value)
    {
        $this->userData = $value;

        return $this;
    }

    /**
     * @param Agent $agent
     *
     * @return Position
     */
    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * @param MarkQuantity $mark_quantity
     *
     * @return Position
     */
    public function setMarkQuantity(MarkQuantity $mark_quantity)
    {
        $this->markQuantity = $mark_quantity;

        return $this;
    }

    /**
     * @param MarkCode $mark_code
     *
     * @return Position
     */
    public function setMarkCode(MarkCode $mark_code)
    {
        $this->markCode = $mark_code;

        return $this;
    }

    /**
     * @param SectoralItemProps $sectoral_item_props
     *
     * @return Position
     */
    public function setSectoralItemProps(SectoralItemProps $sectoral_item_props)
    {
        $this->sectoralItemProps = $sectoral_item_props;

        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'vat' => $this->vat->getRate(),
            'measure' => $this->measure,
            'payment_method' => $this->paymentMethod,
            'payment_object' => $this->paymentObject
        ];

        if ($this->id !== null) {
            $result['id'] = $this->id;
        }

        if ($this->excise !== null) {
            $result['excise'] = $this->excise;
        }
        
        if ($this->countryCode !== null) {
            $result['country_code'] = $this->countryCode;
        }

        if ($this->declarationNumber !== null) {
            $result['declaration_number'] = $this->declarationNumber;
        }

        if ($this->userData !== null) {
            $result['user_data'] = $this->userData;
        }

        if ($this->agent !== null) {
            $result['agent_info'] = $this->agent->asArray();
            if (array_key_exists('supplier_info', $result['agent_info'])) {
                $result['supplier_info'] = $result['agent_info']['supplier_info'];
                unset($result['agent_info']['supplier_info']);
            }
        }

        if ($this->markQuantity !== null) {
            $result['mark_quantity'] = $this->markQuantity->asArray();
        }

        if ($this->markCode !== null) {
            $result['mark_code'] = $this->markCode->asArray();
        }

        if ($this->sectoralItemProps !== null) {
            $result['sectoral_item_props'] = [$this->sectoralItemProps->asArray()];
        }

        return $result;
    }
}