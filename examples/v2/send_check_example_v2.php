<?php
require __DIR__.'/../../autoload.php';

use Komtet\KassaSdk\v2\AdditionalUserProps;
use Komtet\KassaSdk\v2\Agent;
use Komtet\KassaSdk\v2\Buyer;
use Komtet\KassaSdk\v2\Cashier;
use Komtet\KassaSdk\v2\Check;
use Komtet\KassaSdk\v2\Client;
use Komtet\KassaSdk\v2\Company;
use Komtet\KassaSdk\v2\MarkCode;
use Komtet\KassaSdk\v2\MarkQuantity;
use Komtet\KassaSdk\v2\Measure;
use Komtet\KassaSdk\v2\OperatingCheckProps;
use Komtet\KassaSdk\v2\Payment;
use Komtet\KassaSdk\v2\PaymentMethod;
use Komtet\KassaSdk\v2\PaymentObject;
use Komtet\KassaSdk\v2\Position;
use Komtet\KassaSdk\v2\QueueManager;
use Komtet\KassaSdk\v2\SectoralCheckProps;
use Komtet\KassaSdk\v2\SectoralItemProps;
use Komtet\KassaSdk\v2\TaxSystem;
use Komtet\KassaSdk\v2\Vat;
use Komtet\KassaSdk\Exception\SdkException;
use Komtet\KassaSdk\Exception\ApiValidationException;


$key = 'YOUR_SHOP_ID'; // идентификатор магазина
$secret = 'YOUR_SHOP_SECRET'; // секретный ключ
// PSR-совместимый логгер (опциональный параметр)
$logger = null;
$client = new Client($key, $secret, $logger);
$manager = new QueueManager($client);
$manager->registerQueue('queue', 'YOUR_QUEUE_ID'); //int

// ЧЕК

// уникальный ID, предоставляемый магазином
$checkID = '';
// E-Mail клиента, на который будет отправлен E-Mail с чеком.
$clientEmail = 'test@test.ru';

// Место расчетов 
$payment_address = 'Офис 3';

// Информация о покупателе
$buyer = new Buyer($clientEmail);
$buyer->setName('Иванов А.А.'); // Покупатель
$buyer->setPhone('79099099999'); // Телефон
$buyer->setBirthdate('20.10.2000'); // Дата рождения покупателя
$buyer->setCitizenship('123'); // Числовой код страны, гражданином которой является покупатель
$buyer->setDocumentCode('12'); // Числовой код вида документа, удостоверяющего личность
$buyer->setDocumentData('Реквизиты документа, удостоверяющего личность'); // Реквизиты документа, удостоверяющего личность
$buyer->setAddress('Город, Улица д.5'); // Адрес покупателя

// Информация о компании
$company = new Company(TaxSystem::COMMON, $payment_address);
$company->setPlaceAddress('г. Москва'); // Адрес расчетов
$company->setINN('502906602876'); // ИНН организации

$check = Check::createSell($checkID, $buyer, $company); // Чек прихода
// $check = Check::createSellReturn($checkID, $buyer, $company); // Чек возврата прихода
// $check = Check::createBuy($checkID, $buyer, $company); // Чек Расхода
// $check = Check::createBuyReturn($checkID, $buyer, $company); // Чек возврата расхода

// Дополнительный реквизит пользователя 
$additional_user_props = new AdditionalUserProps('name', 'value');
$check->setAdditionalUserProps($additional_user_props);

// Отраслевой реквезит чека 
$sectoral_check_props = new SectoralCheckProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
$check->setSectoralCheckProps($sectoral_check_props);

// // Возможность добавления нескольких SectoralCheckProps
// $sectoral_check_props2 = new SectoralCheckProps('002', '01.01.2020', '2', 'значение отраслевого реквизита2');
// $check->setSectoralCheckProps($sectoral_check_props2);

// Операционный реквизит пользователя 
$operating_check_props = new OperatingCheckProps('0', 'данные операции', '12.03.2020 16:55:25');
$check->setOperatingCheckProps($operating_check_props);

// Дополнительный реквизит чека 
$check->setAdditionalCheckProps('доп. реквизит чека');

// Адрес для получения отчёта по чеку
$check->setCallbackUrl('https://test.ru/callback-url');

// Говорим, что чек нужно распечатать 
$check->setShouldPrint(true);

// Добавление кассира 
$cashier = new Cashier('Иванов И.П.', '012345678912');
$check->addCashier($cashier);

// Применение к позициям единой общей скидки на чек (например скидочного купона)
$check->applyDiscount(50);


// ПОЗИЦИИ ЧЕКА

// Ставка налога
// $vat = new Vat(Vat::RATE_NO);
// $vat = new Vat(Vat::RATE_0);
// $vat = new Vat(Vat::RATE_10);
$vat = new Vat(Vat::RATE_20);
// $vat = new Vat(Vat::RATE_110);
// $vat = new Vat(Vat::RATE_120);

// Еденица измерения
// $measure = Measure::PIECE;
// $measure = Measure::GRAMM;
// $measure = Measure::KILOGRAMM;
// $measure = Measure::TON;
// $measure = Measure::CENTIMETER;
// $measure = Measure::DECIMETER;
// $measure = Measure::METER;
// $measure = Measure::SQUARE_CENTIMETER;
// $measure = Measure::SQUARE_DECIMETER;
// $measure = Measure::SQUARE_METER;
$measure = Measure::MILLILITER;
// $measure = Measure::LITER;
// $measure = Measure::CUBIC_METER;
// $measure = Measure::KILOWATT_HOUR;
// $measure = Measure::GIGA_CALORIE;
// $measure = Measure::DAY;
// $measure = Measure::HOUR;
// $measure = Measure::MINUTE;
// $measure = Measure::SECOND;
// $measure = Measure::KILOBYTE;
// $measure = Measure::MEGABYTE;
// $measure = Measure::GIGABYTE;
// $measure = Measure::TERABYTE;
// $measure = Measure::OTHER_MEASURMENTS;

// Cпособ расчета
// $payment_method = PaymentMethod::FULL_PAYMENT;
$payment_method = PaymentMethod::PRE_PAYMENT_FULL;
// $payment_method = PaymentMethod::PRE_PAYMENT_PART;
// $payment_method = PaymentMethod::ADVANCE;
// $payment_method = PaymentMethod::CREDIT_PART;
// $payment_method = PaymentMethod::CREDIT_PAY;
// $payment_method = PaymentMethod::CREDIT;

// Предмет расчета
// $payment_object = PaymentObject::PRODUCT;
// $payment_object = PaymentObject::PRODUCT_PRACTICAL;
// $payment_object = PaymentObject::WORK;
// $payment_object = PaymentObject::SERVICE;
// $payment_object = PaymentObject::GAMBLING_BET;
// $payment_object = PaymentObject::GAMBLING_WIN;
// $payment_object = PaymentObject::LOTTERY_BET;
// $payment_object = PaymentObject::LOTTERY_WIN;
// $payment_object = PaymentObject::RID;
// $payment_object = PaymentObject::PAYMENT;
// $payment_object = PaymentObject::COMMISSION;
// $payment_object = PaymentObject::COMPOSITE;
// $payment_object = PaymentObject::PAY;
// $payment_object = PaymentObject::OTHER;
$payment_object = PaymentObject::PROPERTY_RIGHT;
// $payment_object = PaymentObject::NON_OPERATING;
// $payment_object = PaymentObject::INSURANCE;
// $payment_object = PaymentObject::SALES_TAX;
// $payment_object = PaymentObject::RESORT_FEE;
// $payment_object = PaymentObject::DEPOSIT;
// $payment_object = PaymentObject::CONSUMPTION;
// $payment_object = PaymentObject::SOLE_PROPRIETOR_CPI_CONTRIBUTINS;
// $payment_object = PaymentObject::CPI_CONTRIBUTINS;
// $payment_object = PaymentObject::SOLE_PROPRIETOR_CMI_CONTRIBUTINS;
// $payment_object = PaymentObject::CMI_CONTRIBUTINS;
// $payment_object = PaymentObject::CSI_CONTRIBUTINS;
// $payment_object = PaymentObject::CASINO_PAYMENT;
// $payment_object = PaymentObject::PAYMENT_OF_THE_MONEY;
// $payment_object = PaymentObject::ATNM;
// $payment_object = PaymentObject::ATM;
// $payment_object = PaymentObject::TNM;
// $payment_object = PaymentObject::TM;

// Позиция в чеке: имя, цена, кол-во, общая стоимость, налог, еденица измерения, способ расчета, предмет расчета
$position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);

// Задаём id позиции
$position->setId('123456');

// Агент по предмету расчета 
$agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "502906602876");
$position->setAgent($agent);

// Отраслевой реквизит предмета расчета
$sectoral_item_props = new SectoralItemProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
$position->setSectoralItemProps($sectoral_item_props);

// // Возможность добавления нескольких SectoralItemProps
// $sectoral_item_props2 = new SectoralItemProps('002', '26.01.1935', '15', 'значение отраслевого реквизита2');
// $position->setSectoralItemProps($sectoral_item_props2);

// Дополнительный реквизит предмета расчета
$position->setUserData('Дополнительный реквизит предмета расчета');

// Сумма акциза
$position->setExcise(25);

// Цифровой код страны происхождения товара
$position->setCountryCode('5');

// Номер таможенной декларации
$position->setDeclarationNumber('15');

// Дробное количество маркированного товара
$mark_quantity = new MarkQuantity(1, 2);
$position->setMarkQuantity($mark_quantity);

// Атрибуты кода товара (маркировки)
$mark_code = new MarkCode(MarkCode::GS1M, '0123455g54drgdfsgre54st5ergdfg');
$position->setMarkCode($mark_code);

$check->addPosition($position);

// Итоговая сумма расчёта
$payment = new Payment(Payment::TYPE_CARD, 50);
$check->addPayment($payment);

$payment = new Payment(Payment::TYPE_CASH, 50);
$check->addPayment($payment);

// Добавляем чек в очередь.
try {
    $manager->putCheck($check, 'queue');
} catch (ApiValidationException $e) {
    echo $e->getMessage(), "\n";
    echo $e->getVLDCode(), "\n";
    echo $e->getDescription(), "\n";
} catch (SdkException $e) {
    echo $e->getMessage();
}
