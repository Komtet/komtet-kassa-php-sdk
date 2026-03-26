<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v1;

class Correction
{
    const TYPE_SELF = 'self';
    const TYPE_FORCED = 'forced';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $document;

    /**
     * @param string $type Correction type (Correction::TYPE_*)
     * @param string $date Document date (yyyy-mm-dd)
     * @param string $document Document number
     *
     * @return Correction
     */
    public function __construct($type, $date, $document=null)
    {
        $this->type = $type;
        $this->date = $date;

        if ($document) {
            $this->document = $document;
        }
    }

    /**
     * @param string $date Document date (yyyy-mm-dd)
     * @param string $document Document number
     *
     * @return Correction
     */
    public static function createSelf($date, $document=null)
    {
        return new static(static::TYPE_SELF, $date, $document);
    }

    /**
     * @param string $date Document date (yyyy-mm-dd)
     * @param string $document Document number
     *
     * @return Correction
     */
    public static function createForced($date, $document)
    {
        return new static(static::TYPE_FORCED, $date, $document);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'type' => $this->type,
            'date' => $this->date
        ];

        if ($this->document !== null) {
            $result['document'] = $this->document;
        }

        return $result;
    }
}
