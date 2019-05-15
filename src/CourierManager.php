<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Komtet\KassaSdk;

class CourierManager
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
   * Feed back the information about  couriers
   *
   * @param string $start Launch the couriers input from "start"
   * @param string $limit Bound the couriers input to the "limit" elements
   *
   * @return mixed
   */
   public function getCouriers($start='0', $limit='10')
   {
     $path = sprintf('/api/shop/v1/couriers?start=%s&limit=%s', $start, $limit);
     return $this->client->sendRequest($path);
   }

}
