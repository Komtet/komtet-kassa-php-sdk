<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class CorrectionInfo
{
    const TYPE_SELF = 'self';
    const TYPE_INSTRUCTION = 'instruction';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $base_date;

    /**
     * @var string
     */
    private $base_number;

    /**
     * @var string
     */
    private $base_name;

    /**
     * @param string $type Correction type (Correction::TYPE_*)
     * @param string $base_date Document date (dd-mm-yyyy)
     * @param string $base_number Document number
     * @param string $base_name Document name
     *
     * @return Correction
     */
    public function __construct($type, $base_date, $base_number, $base_name)
    {
        $this->type = $type;
        $this->base_date = $base_date;
        $this->base_number = $base_number;
        $this->base_name = $base_name;
    }

    /**
     * @param string $base_date Document date (dd-mm-yyyy)
     * @param string $base_number Document number
     * @param string $base_name Document name
     *
     * @return Correction
     */
    public static function createSelf($base_date, $base_number, $base_name)
    {
        return new static(static::TYPE_SELF, $base_date, $base_number, $base_name);
    }

    /**
     * @param string $base_date Document date (dd-mm-yyyy)
     * @param string $base_number Document number
     * @param string $base_name Document name
     *
     * @return Correction
     */
    public static function createInstruction($base_date, $base_number, $base_name)
    {
        return new static(static::TYPE_INSTRUCTION, $base_date, $base_number, $base_name);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'type' => $this->type,
            'base_date' => $this->base_date,
            'base_number' => $this->base_number,
            'base_name' => $this->base_name
        ];
    }
}
