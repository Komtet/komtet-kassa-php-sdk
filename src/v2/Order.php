<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class Order
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $state;

    /**
     * @var bool
     */
    private $isPayToCourier = false;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var OrderPosition[]
     */
    private $items = [];

    /**
     * @var string
     */
    private $dateStart;

    /**
     * @var string
     */
    private $dateEnd;

    /**
     * @var string
     */
    private $callbackUrl;

    /**
     * @var int
     */
    private $courierId;

    /**
     * @var string
     */
    private $additionalCheckProps;

    /**
     * @var AdditionalUserProps
     */
    private $additionalUserProps;

    /**
     * @var OperatingCheckProps
     */
    private $operatingCheckProps;

    /**
     * @var SectoralCheckProps
     */
    private $sectoralCheckProps;

    /**
     * @var OrderBuyer
     */
    private $orderBuyer;

    /**
     * @var Company
     */
    private $company;

    /**
     * @var Payment
     */
    private $paymentType;

    /**
     * @var int|float
     */
    private $prepayment;

    /**
     * @param int $order_id A unique order id in a shop
     * @param string $state Order status
     * @param bool $is_pay_to_courier Payment status
     * @param int|float prepayment Prepayment
     * @param Payment $payment_type Payment type
     *
     * @return Order
     */
    public function __construct($order_id, $state=null, $is_pay_to_courier=true,
                                $prepayment = 0, $payment_type = Payment::TYPE_CARD)
    {
        $this->orderId = $order_id;
        $this->state = $state;
        $this->isPayToCourier = $is_pay_to_courier;
        $this->prepayment = $prepayment;
        $this->paymentType = $payment_type;
    }

    /**
     * @param string $phone phone of the recipient
     * @param string $address address of the recipient
     * @param string $name name of the recipient
     * @param string $inn inn of the recipient
     * @param string $email email of the recipient
     * @param array $coordinate Coordinate latitude/longitude
     *
     */
    public function setOrderBuyer(OrderBuyer $order_buyer)
    {
        $this->orderBuyer = $order_buyer;
    }

    /**
     * @param string $sno company Tax
     * @param string $paymentAddress company payment Address
     * @param string $placeAddress company place Address
     * @param string $inn company inn
     *
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @param string $date_start Initial order delivery time
     * @param string $date_end Final order delivery time
     *
     */
    public function setDeliveryTime($date_start, $date_end)
    {
        $this->dateStart = $date_start;
        $this->dateEnd = $date_end;
    }

    /**
     * @param string $description Order comment
     *
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

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
     */
    public function addPosition(OrderPosition $orderPosition)
    {
        array_push($this->items, $orderPosition);
    }

    /**
     * @param string $callback_url callback url for Order
     *
     */
    public function setCallbackUrl($callback_url)
    {
        $this->callbackUrl = $callback_url;
    }

    /**
     * @param int $courier_id ID courier
     *
     */
    public function setCourierId($courier_id)
    {
        $this->courierId = $courier_id;
    }

    /**
     * @param string $value
     *
     */
    public function setAdditionalCheckProps($value)
    {
        $this->additionalCheckProps = $value;
    }

    /**
     * @param AdditionalUserProps $additionalUserProps
     *
     */
    public function setAdditionalUserProps(AdditionalUserProps $additionalUserProps)
    {
        $this->additionalUserProps = $additionalUserProps;
    }

    /**
     * @param SectoralCheckProps $sectoralCheckProps
     *
     */
    public function setSectoralCheckProps(SectoralCheckProps $sectoralCheckProps)
    {
        $this->sectoralCheckProps = $sectoralCheckProps;
    }

    /**
     * @param OperatingCheckProps $operatingCheckProps
     *
     */
    public function setOperatingCheckProps(OperatingCheckProps $operatingCheckProps)
    {
        $this->operatingCheckProps = $operatingCheckProps;
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        return $this->items;
    }

    /**
     * @return int|float
     */
    public function getTotalPositionsSum()
    {
        $positionsTotal = 0;
        foreach ($this->items as $item) {
            $positionsTotal += $item->getTotal();
        }

        return $positionsTotal;
    }

    /**
     *
     * Применение к позициям единой общей скидки на чек (например скидочного купона)
     *
     * @param float $checkDiscount
     *
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
     * @return array
     */
    public function asArray()
    {
        $result = [
            'order_id' => $this->orderId,
            'is_pay_to_courier' => $this->isPayToCourier,
            'date_start' => $this->dateStart,
            'date_end' => $this->dateEnd,
            'client' => $this->orderBuyer->asArray(),
            'company' => $this->company->asArray(),
            'items' => array_map(
                function ($item) {
                    return $item->asArray();
                },
                $this->items
            ),
        ];

        if ($this->state !== null) {
            $result['state'] = $this->state;
        }

        if ($this->description !== null) {
            $result['description'] = $this->description;
        }

        if ($this->callbackUrl !== null) {
            $result['callback_url'] = $this->callbackUrl;
        }

        if ($this->courierId !== null) {
            $result['courier_id'] = $this->courierId;
        }

        if ($this->paymentType !== null) {
            $result['payment_type'] = $this->paymentType;
        }

        if ($this->prepayment !== null) {
            $result['prepayment'] = $this->prepayment;
        }

        if ($this->additionalCheckProps !== null) {
            $result['additional_check_props'] = $this->additionalCheckProps;
        }

        if ($this->additionalUserProps !== null) {
            $result['additional_user_props'] = $this->additionalUserProps->asArray();
        }

        if ($this->sectoralCheckProps !== null) {
            $result['sectoral_check_props'] = $this->sectoralCheckProps->asArray();
        }

        if ($this->operatingCheckProps !== null) {
            $result['operating_check_props'] = $this->operatingCheckProps->asArray();
        }

        return $result;
    }

}