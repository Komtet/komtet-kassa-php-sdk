<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

class SectoralCheckProps
{
    /**
     * @var string
     */
    private $federal_id;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $federal_id FOIV id
     * @param string $date creation date
     * @param string $number document number
     * @param string $value industry attribute value
     *
     * @return SectoralCheckProps
     */
    public function __construct($federal_id, $date, $number, $value)
    {
        $this->federal_id = $federal_id;
        $this->date = $date;
        $this->number = $number;
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'federal_id' => $this->federal_id,
            'date' => $this->date,
            'number' => $this->number,
            'value' => $this->value
        ];
    }
}
