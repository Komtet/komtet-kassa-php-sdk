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
     * @param string $type Correction type (Correction::TYPE_*)
     * @param string $base_date Document date (dd-mm-yyyy)
     * @param string $base_number Document number
     *
     * @return CorrectionInfo
     */
    public function __construct($type, $base_date, $base_number = null)
    {
        $this->type = $type;
        $this->base_date = $base_date;

        if ($base_number) {
            $this->base_number = $base_number;
        }
    }

    /**
     * @param string $base_date Document date (dd-mm-yyyy)
     * @param string $base_number Document number
     *
     * @return CorrectionInfo
     */
    public static function createSelf($base_date, $base_number = null)
    {
        return new static(static::TYPE_SELF, $base_date, $base_number);
    }

    /**
     * @param string $base_date Document date (dd-mm-yyyy)
     * @param string $base_number Document number
     *
     * @return CorrectionInfo
     */
    public static function createInstruction($base_date, $base_number = null)
    {
        return new static(static::TYPE_INSTRUCTION, $base_date, $base_number);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'type' => $this->type,
            'base_date' => $this->base_date,
        ];

        if ($this->base_number !== null) {
            $result['base_number'] = $this->base_number;
        }

        return $result;
    }
}
