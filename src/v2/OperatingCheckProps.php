<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class OperatingCheckProps
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $timestamp;
    
    /**
     * @param string $name
     * @param string $value
     * @param string $timestamp
     * @return OperatingCheckProps
     */
    public function __construct($name, $value, $timestamp)
    {
        $this->name = $name;
        $this->value = $value;
        $this->timestamp = $timestamp;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'name' => $this->name,
            'value' => $this->value,
            'timestamp' => $this->timestamp
        ];

        return $result;
    }
}
