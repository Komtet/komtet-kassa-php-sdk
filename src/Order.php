<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk;

class Order
{
    /**
     * @var int
     */
    private $order_id;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $sno;

    /**
     * @var bool
     */
    private $is_paid=false;

    /**
     * @var string
     */
    private $description='';

    /**
     * @var OrderPosition[]
     */
    private $items=[];

    /**
     * @var string
     */
    private $client_name;

    /**
     * @var string
     */
    private $client_address;

    /**
     * @var string
     */
    private $client_phone;

    /**
     * @var string
     */
    private $client_email;

    /**
     * @var string
     */
    private $date_start;

    /**
     * @var string
     */
    private $date_end;

    /**
     * @var string
     */
    private $callback_url;

    /**
     * @var int
     */
    private $courier_id;

    /**
     * @var Payment
     */
    private $payment_type;

    /**
     * @var int|float
     */
    private $prepayment;

    /**
     * @param int $oid A unique order id in a shop
     * @param string $state Order status
     * @param string $sno Tax system
     * @param bool $is_paid Payment status
     * @param int|float prepayment Prepayment
     * @param Payment $payment_type Payment type
     *
     * @return Order
     */
    public function __construct($order_id, $state=null, $sno=null, $is_paid=false,
                                $prepayment = 0, $payment_type = Payment::TYPE_CARD)
    {
        $this->order_id = $order_id;
        $this->is_paid = $is_paid;

        $this->state = $state;
        $this->sno = $sno;

        $this->prepayment = $prepayment;
        $this->payment_type = $payment_type;
    }

    /**
     * @param string $address Address of the recipient
     * @param string $phone Phone of the recipient
     * @param string $email Email of the recipient
     * @param string $name Name of the recipient
     *
     */
    public function setClient($address, $phone, $email=null, $name=null)
    {
        $this->client_address = $address;
        $this->client_phone = $phone;

        $this->client_email = $email;
        $this->client_name = $name;
    }
    /**
     * @param string $date_start Initial order delivery time
     * @param string $date_end Final order delivery time
     *
     */
    public function setDeliveryTime($date_start, $date_end)
    {
        $this->date_start = $date_start;
        $this->date_end = $date_end;
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
     * @param string $oid Item identifier
     * @param string $name Item
     * @param int|float $price Item price in the check
     * @param int|float $quantity Number of items
     * @param int|float $total Item total value
     * @param string $vat Tax rate
     * @param string $type Order type
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
    public function setСallbackUrl($callback_url)
    {
        $this->callback_url = $callback_url;
    }

    /**
     * @param int $courier_id ID courier
     *
     */
    public function setCourierId($courier_id)
    {
        $this->courier_id = $courier_id;
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
        foreach( $this->items as $item )
        {
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
     * @return Order
     */
    public function applyDiscount($checkDiscount)
    {
        $positionsTotal = $this->getTotalPositionsSum();
        $checkPositions = $this->getPositions();

        $positionsCount = count($checkPositions);
        $accumulatedDiscount = 0;

        foreach ( $checkPositions as $index => $position ) {
            if ( $index < $positionsCount-1 ) {
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
            'order_id' => $this->order_id,
            'client_address' => $this->client_address,
            'client_phone' => $this->client_phone,
            'is_paid' => $this->is_paid,
            'description' => $this->description,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'items' => array_map(
                function ($item) {
                    return $item->asArray();
                },
                $this->items
            ),
        ];

        if ($this->client_email !== null) {
            $result['client_email'] = $this->client_email;
        }

        if ($this->client_name !== null) {
            $result['client_name'] = $this->client_name;
        }

        if ($this->sno !== null) {
            $result['sno'] = $this->sno;
        }

        if ($this->state !== null) {
            $result['state'] = $this->state;
        }

        if ($this->courier_id !== null) {
            $result['courier_id'] = $this->courier_id;
        }

        if ($this->callback_url !== null) {
            $result['callback_url'] = $this->callback_url;
        }

        if ($this->payment_type !== null) {
            $result['payment_type'] = $this->payment_type;
        }

        if ($this->prepayment !== null) {
            $result['prepayment'] = $this->prepayment;
        }

        return $result;
    }

}
