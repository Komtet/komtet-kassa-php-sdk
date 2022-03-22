<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class OrderCompany {
    /**
     * @var string
     */
    private $sno;

    /**
     * @var string
     */
    private $paymentAddress;

    /**
     * @var string
     */
    private $placeAddress;

    /**
     * @var string
     */
    private $inn;

    /**
     * @param string $sno
     * @param string $payment_address
     *
     * @return Company
     */
    public function __construct($sno)
    {
        $this->sno = $sno;
    }

    /**
     * @param string $place_address
     *
     * @return Company
     */
    public function setPaymentAddress($payment_address)
    {
      $this->paymentAddress = $payment_address;

      return $this;
    }

    /**
     * @param string $place_address
     *
     * @return Company
     */
    public function setPlaceAddress($place_address)
    {
      $this->placeAddress = $place_address;

      return $this;
    }

    /**
     * @param string $inn
     *
     * @return Company
     */
    public function setINN($inn)
    {
      $this->inn = $inn;

      return $this;
    }

    public function asArray()
    {
        $company = [
          'sno' => $this->sno,
        ];

        if ($this->paymentAddress) {
            $company['payment_address'] = $this->paymentAddress;
          }

        if ($this->placeAddress) {
            $company['place_address'] = $this->placeAddress;
        }

        if ($this->inn) {
            $company['inn'] = $this->inn;
        }

        return $company;
    }
}