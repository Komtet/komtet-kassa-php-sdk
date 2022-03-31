<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

/**
* Единица измерения
*/
class Measure
{
    const PIECE = 0;
    const GRAMM = 10;
    const KILOGRAMM = 11;
    const TON = 12;
    const CENTIMETER = 20;
    const DECIMETER = 21;
    const METER = 22;
    const SQUARE_CENTIMETER = 30;
    const SQUARE_DECIMETER = 31;
    const SQUARE_METER = 32;
    const MILLILITER = 40;
    const LITER = 41;
    const CUBIC_METER = 42;
    const KILOWATT_HOUR = 50;
    const GIGA_CALORIE = 51;
    const DAY = 70;
    const HOUR = 71;
    const MINUTE = 72;
    const SECOND = 73;
    const KILOBYTE = 80;
    const MEGABYTE = 81;
    const GIGABYTE = 82;
    const TERABYTE = 83;
    const OTHER_MEASURMENTS = 255;
}
