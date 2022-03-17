<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class Company {
    /**
     * @var TaxSystem
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
    public function __construct(TaxSystem $sno, $payment_address)
    {
        $this->sno = $sno;
        $this->paymentAddress = $payment_address;
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
          'paymentAddress' => $this->paymentAddress
        ];

        if ($this->placeAddress) {
          $company['place_address'] = $this->placeAddress;
        }

        if ($this->inn) {
          $company['inn'] = $this->inn;
        }

        return $company;
    }
}