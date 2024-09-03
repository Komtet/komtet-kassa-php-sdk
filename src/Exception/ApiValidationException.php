<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\Exception;

class ApiValidationException extends \RuntimeException implements SdkException
{
    protected $title = '';
    protected $vldCode = '';
    protected $description = '';

    public function __construct($title, $vldCode, $description, $respCode, Exception $previous = null) {
        $this->title = $title;
        $this->vldCode = $vldCode;
        $this->description = $description;

        parent::__construct($title, $respCode, $previous);
    }

    public function getTitle() {
        return $this->title;
    }

    public function getVLDCode() {
        return $this->vldCode;
    }

    public function getDescription() {
        return $this->description;
    }
}
