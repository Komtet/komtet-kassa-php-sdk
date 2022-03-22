<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class OrderPosition
{
    /**
     * @var string|integer
     */
    private $orderItemId;

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
     * @var Vat
     */
    private $vat;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Measure
     */
    private $measure;

    /**
     * @var Agent
     */
    private $agent = null;

    /**
     * @var int|float
     */
    private $excise;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $declarationNumber;

    /**
     * @var string
     */
    private $userData;

    /**
     * @var boolean
     */
    private $isNeedMarkCode;

    /**
     * @var MarkQuantity 
     */
    private $markQuantity = null;

    /**
     * @var MarkCode 
     */
    private $markCode = null;

    /**
     * @var SectoralItemProps[] 
     */
    private $sectoralItemProps = [];


    /**
     * @param string $orderItemId Item identifier
     * @param string $name Item name
     * @param int|float $price Item price in the check
     * @param int|float $quantity Number of items
     * @param int|float $total Item total value
     * @param Vat $vat Tax rate
     * @param string $type Product type
     * @param Measure $measure measure
     * @param Agent $agent Agent
     * @param string $excise Excise tax
     * @param string $countryCode Country code
     * @param string $declarationNumber Number of customs declaration
     * @param string $userData Additional data of the payment object
     * @param boolean $isNeedMarkCode Product code
     * @param MarkQuantity $markQuantity Fractional quantity of marked goods
     * @param MarkCode $markCode Product code 
     * @param SectoralItemProps $sectoralItemProps Industry requisite of payment object
     *
     * @return OrderPosition
     */
    public function __construct($args = [])
    {
        $defaultArgs = [
            'quantity' => 1,
            'total' => null,
            'vat' =>  Vat::RATE_NO,
            'type' => null,
            'measure' => 0,
            'agent' => null,
            'excise' => null,
            'country_code' => null,
            'declaration_number' => null,
            'user_data' => null,
            'is_need_mark_code' => false,
            'mark_quantity' => null,
            'mark_code' => null,
            'sectoral_item_props' => null,
        ];
        $args = array_merge($defaultArgs, $args);

        if ($args['total'] == null) {
            $args['total'] = $args['price'] * $args['quantity'];
        }

        $this->orderItemId = $args['order_item_id'];
        $this->name = $args['name'];
        $this->price = $args['price'];
        $this->quantity = $args['quantity'];
        $this->total = $args['total'];
        $this->vat = new Vat($args['vat']);
        $this->isNeedMarkCode = $args['is_need_mark_code'];

        if ($args['type'] !== null) {
            $this->type = $args['type'];
        }

        if ($args['measure'] !== null) {
            $this->measure = $args['measure'];
        }

        if ($args['agent'] !== null) {
            $this->agent = $args['agent'];
        }

        if ($args['excise'] !== null) {
            $this->excise = $args['excise'];
        }

        if ($args['country_code'] !== null) {
            $this->countryCode = $args['country_code'];
        }

        if ($args['declaration_number'] !== null) {
            $this->declarationNumber = $args['declaration_number'];
        }

        if ($args['user_data'] !== null) {
            $this->userData = $args['user_data'];
        }

        if ($args['mark_quantity'] !== null) {
            $this->markQuantity = $args['mark_quantity'];
        }

        if ($args['mark_code'] !== null) {
            $this->markCode = $args['mark_code'];
        }

        if ($args['sectoral_item_props'] !== null) {
            $this->sectoralItemProps = $args['sectoral_item_props'];
        }
    }

    /**
     * @return int|float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     *
     * @return OrderPosition
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @param Agent $agent
     *
     * @return OrderPosition
     */
    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * @param SectoralItemProps $sectoral_item_props
     *
     * @return OrderPosition
     */
    public function setSectoralItemProps(SectoralItemProps $sectoral_item_props)
    {
        $this->sectoralItemProps[] = $sectoral_item_props;

        return $this;
    }

    /**
     * @param MarkQuantity $mark_quantity
     *
     * @return OrderPosition
     */
    public function setMarkQuantity(MarkQuantity $mark_quantity)
    {
        $this->markQuantity = $mark_quantity;

        return $this;
    }

    /**
     * @param MarkCode $mark_code
     *
     * @return OrderPosition
     */
    public function setMarkCode(MarkCode $mark_code)
    {
        if (is_null($mark_code)) {
            $this->isNeedMarkCode = true;
        }
        $this->markCode = $mark_code;

        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'order_item_id' => $this->orderItemId,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'vat' => $this->vat->getRate(),
            'is_need_mark_code' => $this->isNeedMarkCode,
        ];

        if ($this->type !== null) {
            $result['type'] = $this->type;
        }

        if ($this->measure !== null) {
            $result['measure'] = $this->measure;
        }

        if ($this->agent !== null) {
            $result['agent_info'] = $this->agent->asArray();
            if (array_key_exists('supplier_info', $result['agent_info'])) {
                $result['supplier_info'] = $result['agent_info']['supplier_info'];
                unset($result['agent_info']['supplier_info']);
            }
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
        
        if ($this->markQuantity !== null) {
            $result['mark_quantity'] = $this->markQuantity->asArray();
        }

        if ($this->markCode !== null) {
            $result['mark_code'] = $this->markCode->asArray();
        }

        if ($this->sectoralItemProps !== null) {
            $result['sectoral_item_props'] = array_map(
                function($sectoral_item_props) {
                    return $sectoral_item_props->asArray();
                },
                $this->sectoralItemProps
            );
        }

        return $result;
    }
}
