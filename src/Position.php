<?php

/**
 * This file is part of the motmom/cloud-kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Motmom\CloudKassaSdk;

class Position
{
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
     * @var int|float
     */
    private $discount;

    /**
     * @var Vat
     */
    private $vat;

    /**
     * @param string $name Item name
     * @param int|float $price Item price
     * @param int|float $quantity Item quanitity
     * @param int|float $total Total cost
     * @param int|float $discount Discount size in RUB
     * @param Vat $vat VAT
     *
     * @return Position
     */
    public function __construct($name, $price, $quantity, $total, $discount, Vat $vat)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->total = $total;
        $this->discount = $discount;
        $this->vat = $vat;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'discount' => $this->discount,
            'vat' => $this->vat->asArray(),
        ];
    }
}
