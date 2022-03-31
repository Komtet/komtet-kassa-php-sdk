<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk\v2;

/**
* Признак рассчета
*/
class PaymentObject
{
    /**
     * Товар, за исключением подакцизного товара
     */
    const PRODUCT = 'product';

    /**
     * Подакцизный товар
     */
    const PRODUCT_PRACTICAL = 'product_practical';

    /**
     * Работа
     */
    const WORK = 'work';

    /**
     * Услуга
     */
    const SERVICE = 'service';

    /**
     * Прием ставок при осуществлении деятельности по проведению азартных игр
     */
    const GAMBLING_BET = 'gambling_bet';

    /**
     * Выплата денежных средств в виде выигрыша при осуществлении деятельности по проведению
     * азартных игр
     */
    const GAMBLING_WIN = 'gambling_win';

    /**
     * Прием денежных средств при реализации лотерейных билетов, электронных лотерейных билетов,
     * приеме лотерейных ставок при осуществлении деятельности по проведению лотерей
     */
    const LOTTERY_BET = 'lottery_bet';

    /**
     * Выплате денежных средств в виде выигрыша при осуществлении деятельности по проведению
     * лотерей
     */
    const LOTTERY_WIN = 'lottery_win';

    /**
     * Предоставление прав на использование результатов интеллектуальной деятельности или средств
     * индивидуализации «ПРЕДОСТАВЛЕНИЕ РИД» или «РИД»
     */
    const RID = 'rid';

    /**
     * Об авансе, задатке, предоплате, кредите, взносе в счет оплаты, пени, штрафе, вознаграждении,
     * бонусе и ином аналогичном предмете расчета
     */
    const PAYMENT = 'payment';

    /**
     * Вознаграждении пользователя, являющегося платежным агентом (субагентом), банковским
     * платежным агентом (субагентом), комиссионером, поверенным или иным агентом
     */
    const COMMISSION = 'commission';

    /**
     * О предмете расчета, состоящем из предметов, каждому из которых может быть присвоено
     * значение от «0» до «11» (0-11 -- это вышеперечисленные)
     */
    const COMPOSITE = 'composite';

    /**
     * Взнос в счет оплаты пени, штрафа, вознаграждения, бонуса или
     * иного аналогичного предмета расчета
     */
    const PAY = 'pay';

    /**
     * О предмете расчета, не относящемуся к предметам расчета, которым может быть присвоено
     * значение от «0» до «12» (0-12 -- это вышеперечисленные)
     */
    const OTHER = 'other';

    /**
     * Передача имущественного права
     */
    const PROPERTY_RIGHT = 'property_right';

    /**
     * Внереализационный доход
     */
    const NON_OPERATING = 'non_operating';

    /**
     * Страховые взносы
     */
    const INSURANCE = 'insurance';

    /**
     * Торговый сбор
     */
    const SALES_TAX = 'sales_tax';

    /**
     * Курортный сбор
     */
    const RESORT_FEE = 'resort_fee';

    /**
     * Залог
     */
    const DEPOSIT = 'deposit';

    /**
     * Расход
     */
    const CONSUMPTION = 'consumption';

    /**
     * взносы на ОПС ИП
     */
    const SOLE_PROPRIETOR_CPI_CONTRIBUTINS = 'sole_proprietor_cpi_contributins';

    /**
     * взносы на ОПС
     */
    const CPI_CONTRIBUTINS = 'cpi_contributins';

    /**
     * взносы на ОМС ИП
     */
    const SOLE_PROPRIETOR_CMI_CONTRIBUTINS = 'sole_proprietor_cmi_contributins';

    /**
     * взносы на ОМС
     */
    const CMI_CONTRIBUTINS = 'cmi_contributins';

    /**
     * взносы на ОСС
     */
    const CSI_CONTRIBUTINS = 'csi_contributins';

    /**
     * платеж казино
     */
    const CASINO_PAYMENT = 'casino_payment';

    /**
     * выдача денежных средств банковским платежным агентом
     */
    const PAYMENT_OF_THE_MONEY = 'payment_of_the_money';

    /**
     * Реализация подакцизного товара, подлежащего маркировке       средством идентификации, но не имеющего кода маркировки
     */
    const ATNM = 'atnm';

    /**
     * Реализация подакцизного товара, подлежащего маркировке средством идентификации, и имеющего код маркировки
     */
    const ATM = 'atm';

    /**
     * Реализация товара, подлежащего маркировке средством идентификации, но не имеющего кода маркировки, за исключением подакцизного товара
     */
    const TNM = 'tnm';

    /**
     * Реализация товара, подлежащего маркировке средством идентификации, и имеющего код маркировки, за исключением подакцизного товара
     */
    const TM = 'tm';
}
