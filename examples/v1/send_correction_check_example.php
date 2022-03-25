<?php
require __DIR__.'/../../autoload.php';

use Komtet\KassaSdk\v1\AuthorisedPerson;
use Komtet\KassaSdk\v1\AdditionalUserProps;
use Komtet\KassaSdk\v1\Cashier;
use Komtet\KassaSdk\v1\Client;
use Komtet\KassaSdk\v1\CorrectionCheck;
use Komtet\KassaSdk\v1\Correction;
use Komtet\KassaSdk\v1\QueueManager;
use Komtet\KassaSdk\v1\Payment;
use Komtet\KassaSdk\v1\Position;
use Komtet\KassaSdk\v1\TaxSystem;
use Komtet\KassaSdk\v1\Vat;
use Komtet\KassaSdk\Exception\SdkException;
use Komtet\KassaSdk\Exception\ApiValidationException;


$key = 'YOUR_SHOP_ID';
$secret = 'YOUR_SHOP_SECRET';
// PSR-совместимый логгер (опциональный параметр)
$logger = null;
$client = new Client($key, $secret, $logger);
$manager = new QueueManager($client);
$manager->registerQueue('queue', 'YOUR_QUEUE_ID'); //int

// ЧЕК КОРРЕКЦИИ

// createSelf для самостоятельной коррекции
// createForced для коррекции по предписанию
$correction = Correction::createSelf(
    '2012-12-21', // Дата документа коррекции в формате yyyy-mm-dd
    '4815162342', // Номер документа коррекции
    'description' // Описание коррекции
);

$correctionCheck = CorrectionCheck::createSellCorrection('12345', TaxSystem::COMMON, $correction); // Чек коррекции прихода
// $correctionCheck = CorrectionCheck::createSellReturnCorrection('123', TaxSystem::COMMON, $correction); // Чек возврата коррекции прихода
// $correctionCheck = CorrectionCheck::createBuyCorrection('123', TaxSystem::COMMON, $correction); // Чек коррекции расхода
// $correctionCheck = CorrectionCheck::createBuyReturnCorrection('123', TaxSystem::COMMON, $correction); // Чек возврата коррекции прихода

// Печатать ли чек
$correctionCheck->setShouldPrint(false);

// Данные уполномоченного лица
$authorised_person = new AuthorisedPerson('name', 'inn');
$correctionCheck->setAuthorisedPerson($authorised_person);

// Добавление кассира 
$cashier = new Cashier('Иванов И.П.', '012345678912');
$correctionCheck->addCashier($cashier);

// Дополнительный реквизит чека 
$correctionCheck->setAdditionalCheckProps('доп. реквизит');

// Адрес для получения отчёта по чеку
$correctionCheck->setCallbackUrl('https://test.ru/callback-url');

// Дополнительный реквизит пользователя 
$additional_user_props = new AdditionalUserProps('name', 'value');
$correctionCheck->setAdditionalUserProps($additional_user_props);

// ПОЗИЦИИ ЧЕКА КОРРЕКЦИИ

$vat = new Vat(Vat::RATE_20);

// Позиция в чеке: имя, цена, кол-во, общая стоимость, налог
$position = new Position('name', 100, 1, 100, $vat);

$correctionCheck->addPosition($position);

// // Опционально можно установить:
// // Идентификатор позиции
// $position->setId('123');

// // Единицу измерения
// $position->setMeasureName('Кг.');

// // Cпособ рассчета
// $position->setCalculationMethod(CalculationMethod::FULL_PAYMENT);

// // Признак рассчета
// $position->setCalculationSubject(CalculationSubject::PRODUCT);

// // Агента по предмету расчета
// $agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "12345678901");
// $position->setAgent($agent);

// // Код маркировки
// $nomenclature = new Nomenclature('kjgldfjgdfklg234234');
// $position->setNomenclature($nomenclature);

// // Сумма акциза
// $position->setExcise(25);

// // Цифровой код страны происхождения товара
// $position->setCountryCode('5');

// // Номер таможенной декларации
// $position->setDeclarationNumber('15');

// Итоговая сумма расчёта
$payment = new Payment(Payment::TYPE_CARD, 50);
$correctionCheck->addPayment($payment);

$payment = new Payment(Payment::TYPE_CASH, 50);
$correctionCheck->addPayment($payment);

// Добавляем чек в очередь.
try {
    $manager->putCheck($correctionCheck, 'queue');
} catch (ApiValidationException $e) {
    echo $e->getMessage(), "\n";
    echo $e->getVLDCode(), "\n";
    echo $e->getDescription(), "\n";
} catch (SdkException $e) {
    echo $e->getMessage();
}
