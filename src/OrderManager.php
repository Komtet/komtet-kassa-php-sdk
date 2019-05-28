<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Komtet\KassaSdk;

class OrderManager
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }


    /**
     * Order making for delivery
     *
     * @param Order $order Order
     *
     * @return mixed
     */
    public function createOrder($order)
    {
        $path = sprintf('api/shop/v1/orders');
        return $this->client->sendRequest($path, $order->asArray());
    }


    /**
     * Updating order for delivery
     *
     * @param int $oid Order ID
     * @param Order $order Order
     *
     * @return mixed
     */
    public function updateOrder($oid, $order)
    {
        $path = sprintf('api/shop/v1/orders/%s', $oid);
        return $this->client->sendRequest($path, $order->asArray(), 'PUT');
    }


   /**
    * Viewing order information
    *
    * @param int $oid Order ID
    *
    * @return mixed
    */
    public function getOrderInfo($oid)
    {
        $path = sprintf('api/shop/v1/orders/%s', $oid);
        return $this->client->sendRequest($path);
    }


    /**
     * Delete order
     *
     * @param int $oid Order ID
     *
     * @return mixed
     */
    public function deleteOrder($oid)
    {
        $path = sprintf('api/shop/v1/orders/%s', $oid);
        return $this->client->sendRequest($path, null, 'DELETE');
    }

    /**
     * Feed the order information back
     *
     * @param string $courier_id Courier ID
     * @param string $date_start Delivery date and time
     * @param string $start Launch the order input from "start"
     * @param string $limit Bound the order input to the "limit" elements
     *
     * @return mixed
     */
    public function getOrders($start='0', $limit='10', $courier_id=null, $date_start=null)
    {
        $path = sprintf('api/shop/v1/orders?start=%s&limit=%s', $start, $limit);

        if ($courier_id !== null){
          $path .= sprintf('&courier_id=%s', $courier_id);
        }

        if ($date_start !== null){
          $path .= sprintf('&date_start=%s', $date_start);
        }
        return $this->client->sendRequest($path);
    }
}
