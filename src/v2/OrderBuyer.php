<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class OrderBuyer
{
    /**
     * @var array
     */
    private $orderBuyer;

    public function __construct($phone, $address, $name=null, $inn=null, $email=null)
    {
        $this->orderBuyer = [
            'phone' => $phone,
            'address' => $address
        ];

        if ($name !== null) {
            $this->orderBuyer['name'] = $name;
        }

        if ($inn !== null) {
            $this->orderBuyer['inn'] = $inn;
        }

        if ($email !== null) {
            $this->orderBuyer['email'] = $email;
        }
    }

    /**
     * 
     *
     * @param array 
     *
     * @return OrderBuyer
     */
    public function setCoordinate($longitude, $latitude) {
        $this->orderBuyer['coordinate'] = [
            'longitude' => $longitude,
            'latitude' => $latitude
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->orderBuyer;
    }

}