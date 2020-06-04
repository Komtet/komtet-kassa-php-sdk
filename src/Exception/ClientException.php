<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\Exception;

use RuntimeException;

class ClientException extends RuntimeException implements SdkException
{
    // Список кодов
    const UNKNOWN_ERROR           = 'SYS00'; // неизвестная ошибка
    const AUTH_ERROR              = 'AUT00'; // ошибка авторизации
    const NO_ACTIVE_SERVICE_ERROR = 'SRV00'; // нет активной услуги
    const VALIDATE_ERROR          = 'VLD00'; // ошибка валидации
    const CHECK_EXISTS_ERROR      = 'VLD11'; // чек с внешнем идентификатором существует (попытка напечатать чек повторно)
    const INCORRECT_PENNY_ERROR   = 'VLD12'; // цена позиции в чеке содержит доли копеек
    const INCORRECT_SUM_ERROR     = 'VLD13'; // неверно переданная стоимость позиции
}
