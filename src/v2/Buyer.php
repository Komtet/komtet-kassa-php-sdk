<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class Buyer
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $inn;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $birthdate;

    /**
     * @var string
     */
    private $citizenship;

    /**
     * @var string
     */
    private $documentCode;

    /**
     * @var string
     */
    private $documentData;

    /**
     * @var string
     */
    private $address;

    /**
     *
     * @return Buyer
     */
    public function __construct() {}

    /**
     * @param string $email
     *
     * @return Buyer
     */
    public function setEmail($email)
    {
      $this->email = $email;
      return $this;
    }

    /**
     * @param string $name
     *
     * @return Buyer
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @param string $inn
     *
     * @return Buyer
     */
    public function setINN($inn)
    {
      $this->inn = $inn;
      return $this;
    }

    /**
     * @param string $phone
     *
     * @return Buyer
     */
    public function setPhone($phone)
    {
      $this->phone = $phone;
      return $this;
    }

    /**
     * @param string $birthdate
     *
     * @return Buyer
     */
    public function setBirthdate($birthdate)
    {
      $this->birthdate = $birthdate;
      return $this;
    }

    /**
     * @param string $citizenship
     *
     * @return Buyer
     */
    public function setCitizenship($citizenship)
    {
      $this->citizenship = $citizenship;
      return $this;
    }

    /**
     * @param string $document_code
     *
     * @return Buyer
     */
    public function setDocumentCode($document_code)
    {
      $this->documentCode = $document_code;
      return $this;
    }

    /**
     * @param string $document_data
     *
     * @return Buyer
     */
    public function setDocumentData($document_data)
    {
      $this->documentData = $document_data;
      return $this;
    }

    /**
     * @param string $address
     *
     * @return Buyer
     */
    public function setAddress($address)
    {
      $this->address = $address;
      return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $buyer = [];

        if ($this->email) {
          $buyer['email'] = $this->email;
        }

        if ($this->name) {
          $buyer['name'] = $this->name;
        }

        if ($this->inn) {
          $buyer['inn'] = $this->inn;
        }

        if ($this->phone) {
          $buyer['phone'] = $this->phone;
        }

        if ($this->birthdate) {
          $buyer['birthdate'] = $this->birthdate;
        }

        if ($this->citizenship) {
          $buyer['citizenship'] = $this->citizenship;
        }

        if ($this->documentCode) {
          $buyer['document_code'] = $this->documentCode;
        }

        if ($this->documentData) {
          $buyer['document_data'] = $this->documentData;
        }

        if ($this->address) {
          $buyer['address'] = $this->address;
        }

        return $buyer;
    }
}
