<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 namespace Komtet\KassaSdk;

 class OrderPosition
 {
    /**
     * @var string
     */
    private $oid;

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
     * @var string
     */
   private $measure_name;

    /**
     * @param string $oid Item identifier
     * @param string $name Item
     * @param int|float $price Item price in the check
     * @param int|float $quantity Number of items
     * @param int|float $total Item total value
     * @param string $vat Tax rate
     * @param string $type Order type
     *
     * @return OrderPosition
     */
    public function __construct($args=[])
    {
        $defaultArgs = [
            'vat' =>  Vat::RATE_NO,
            'total' => null,
            'measure_name' => null,
            'type' => null,
            'quantity' => 1
        ];
        $args = array_merge($defaultArgs, $args);

        if ($args['total'] == null){
            $args['total'] = $args['price'] * $args['quantity'];
        }

        $this->oid = $args['oid'];
        $this->name = $args['name'];
        $this->price = $args['price'];
        $this->quantity = $args['quantity'];
        $this->total = $args['total'];
        $this->vat = new Vat($args['vat']);

        if ($args['measure_name'] !== null){
            $this->measure_name = $args['measure_name'];
        }

        if ($args['type'] !== null){
            $this->type = $args['type'];
        }
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'order_item_id' => $this->oid,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'vat' => $this->vat->getRate()
        ];

        if ($this->measure_name !== null){
            $result['measure_name'] = $this->measure_name;
        }

        if ($this->type !== null){
            $result['type'] = $this->type;
        }

        return $result;
    }
 }
