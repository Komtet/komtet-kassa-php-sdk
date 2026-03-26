<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v1;

class TaxSystem
{
    /**
     * ОСН
     */
    const COMMON = 0;

    /**
    * УСН Доходы
    */
    const SIMPLIFIED_IN = 1;

    /**
     * УСН Доходы минус расходы
     */
    const SIMPLIFIED_IN_OUT = 2;

    /**
     * ЕСН
     */
    const UST = 4;

    /**
     * Патент
     */
    const PATENT = 5;
}
