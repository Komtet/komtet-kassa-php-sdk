<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v1;

class Buyer
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int|float
     */
    private $inn;

    /**
     * @param string $name
     * @param string $inn
     *
     * @return Buyer
     */
    public function __construct($name=null, $inn=null)
    {
        $this->name = $name;
        $this->inn = $inn;
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
     * @return array
     */
    public function asArray()
    {
        $data = [];

        if ($this->name) {
          $data['name'] = $this->name;
        }

        if ($this->inn) {
          $data['inn'] = $this->inn;
        }

        return $data;
    }
}
