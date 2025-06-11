<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\AdditionalUserProps;
use Komtet\KassaSdk\v2\Agent;
use Komtet\KassaSdk\v2\Buyer;
use Komtet\KassaSdk\v2\Cashier;
use Komtet\KassaSdk\v2\Check;
use Komtet\KassaSdk\v2\Company;
use Komtet\KassaSdk\v2\MarkCode;
use Komtet\KassaSdk\v2\MarkQuantity;
use Komtet\KassaSdk\v2\Measure;
use Komtet\KassaSdk\v2\OperatingCheckProps;
use Komtet\KassaSdk\v2\Payment;
use Komtet\KassaSdk\v2\PaymentMethod;
use Komtet\KassaSdk\v2\PaymentObject;
use Komtet\KassaSdk\v2\Position;
use Komtet\KassaSdk\v2\SectoralCheckProps;
use Komtet\KassaSdk\v2\SectoralItemProps;
use Komtet\KassaSdk\v2\TaxSystem;
use Komtet\KassaSdk\v2\Vat;
use PHPUnit\Framework\TestCase;

class CheckTest extends TestCase
{
    private $buyer;
    private $company;
    private $check;
    private $position;
    private $payment;

    protected function setUp(): void
    {
        $clientEmail = 'test@test.ru';
        $payment_address = 'Офис 3';
        $this->buyer = new Buyer();
        $this->buyer->setEmail($clientEmail);
        $this->company = new Company(TaxSystem::COMMON, $payment_address);
        $this->check = new Check('id1', Check::INTENT_SELL, $this->buyer, $this->company);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $this->position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $this->check->addPosition($this->position);

        $this->payment = new Payment(Payment::TYPE_CARD, 100.0);
        $this->check->addPayment($this->payment);
    }

    public function testCreationMinimalCheckSuccess()
    {
        $clientEmail = 'test@test.ru';
        $payment_address = 'Офис 3';
        $buyer = new Buyer();
        $buyer->setEmail($clientEmail);
        $company = new Company(TaxSystem::COMMON, $payment_address);

        $check = new Check('id1', Check::INTENT_SELL, $buyer, $company);
        $this->assertEquals($check->asArray()['external_id'], 'id1');
        $this->assertEquals($check->asArray()['client']['email'], 'test@test.ru');
        $this->assertEquals($check->asArray()['company']['sno'], TaxSystem::COMMON);
        $this->assertEquals($check->asArray()['company']['payment_address'], 'Офис 3');

        $check = Check::createSell('id2', $buyer, $company);
        $this->assertEquals($check->asArray()['external_id'], 'id2');
        $this->assertEquals($check->asArray()['client']['email'], 'test@test.ru');
        $this->assertEquals($check->asArray()['company']['sno'], TaxSystem::COMMON);
        $this->assertEquals($check->asArray()['company']['payment_address'], 'Офис 3');

        $check = Check::createSellReturn('id3', $buyer, $company);
        $this->assertEquals($check->asArray()['external_id'], 'id3');
        $this->assertEquals($check->asArray()['client']['email'], 'test@test.ru');
        $this->assertEquals($check->asArray()['company']['sno'], TaxSystem::COMMON);
        $this->assertEquals($check->asArray()['company']['payment_address'], 'Офис 3');

        $check = Check::createBuy('id4', $buyer, $company);
        $this->assertEquals($check->asArray()['external_id'], 'id4');
        $this->assertEquals($check->asArray()['client']['email'], 'test@test.ru');
        $this->assertEquals($check->asArray()['company']['sno'], TaxSystem::COMMON);
        $this->assertEquals($check->asArray()['company']['payment_address'], 'Офис 3');

        $check = Check::createBuyReturn('id5', $buyer, $company);
        $this->assertEquals($check->asArray()['external_id'], 'id5');
        $this->assertEquals($check->asArray()['client']['email'], 'test@test.ru');
        $this->assertEquals($check->asArray()['company']['sno'], TaxSystem::COMMON);
        $this->assertEquals($check->asArray()['company']['payment_address'], 'Офис 3');
    }

    public function testCreationFullCheckSuccess()
    {
        $checkID = '';
        $clientEmail = 'test@test.ru';
        $payment_address = 'Офис 3';

        $buyer = new Buyer();
        $buyer->setEmail($clientEmail);
        $buyer->setName('name');
        $buyer->setINN('0123456789');
        $buyer->setPhone('+79099099999');
        $buyer->setBirthdate('02.03.1995');
        $buyer->setCitizenship('Citizenship');
        $buyer->setDocumentCode('564');
        $buyer->setDocumentData('02.03.1995');
        $buyer->setAddress('ул. Московская');

        $company = new Company(TaxSystem::COMMON, $payment_address);
        $company->setPlaceAddress('г. Москва');
        $company->setINN('502906602876');

        $check = Check::createSell($checkID, $buyer, $company);
        $additional_user_props = new AdditionalUserProps('name', 'value');
        $check->setAdditionalUserProps($additional_user_props);
        $sectoral_check_props = new SectoralCheckProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
        $sectoral_check_props2 = new SectoralCheckProps('002', '25.10.2020', '1', 'значение отраслевого реквизита');
        $check->setSectoralCheckProps($sectoral_check_props);
        $check->setSectoralCheckProps($sectoral_check_props2);
        $operating_check_props = new OperatingCheckProps('0', 'данные операции', '12.03.2020 16:55:25');
        $check->setOperatingCheckProps($operating_check_props);
        $check->setAdditionalCheckProps('доп. реквизит чека');
        $check->setCallbackUrl('https://test.ru/callback-url');
        $check->setShouldPrint(true);
        $cashier = new Cashier('Иванов И.П.', '012345678912');
        $check->addCashier($cashier);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $position->setId('123456');
        $agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "502906602876");
        $position->setAgent($agent);
        $sectoral_item_props = new SectoralItemProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
        $sectoral_item_props2 = new SectoralItemProps('002', '25.10.2020', '1', 'значение отраслевого реквизита');
        $position->setSectoralItemProps($sectoral_item_props);
        $position->setSectoralItemProps($sectoral_item_props2);
        $position->setUserData('Дополнительный реквизит предмета расчета');
        $position->setExcise(25);
        $position->setCountryCode('5');
        $position->setDeclarationNumber('15');
        $mark_quantity = new MarkQuantity(1, 2);
        $position->setMarkQuantity($mark_quantity);
        $mark_code = new MarkCode(MarkCode::GS1M, '0123455g54drgdfsgre54st5ergdfg');
        $position->setMarkCode($mark_code);
        $check->addPosition($position);
        $check->applyDiscount(50);
        $payment = new Payment(Payment::TYPE_CASH, 50);
        $check->addPayment($payment);

        $this->assertEquals($check->asArray()['client'],
                            ['email' => 'test@test.ru',
                            'name' => 'name',
                            'inn' => '0123456789',
                            'phone' => '+79099099999',
                            'birthdate' => '02.03.1995',
                            'citizenship' => 'Citizenship',
                            'document_code' => '564',
                            'document_data' => '02.03.1995',
                            'address' => 'ул. Московская']);
        $this->assertEquals($check->asArray()['company'],
                            ['sno' => 0,
                            'payment_address' => 'Офис 3',
                            'place_address' => 'г. Москва',
                            'inn' => '502906602876']);
        $this->assertEquals($check->asArray()['additional_user_props'],
                            ['name' => 'name',
                            'value' => 'value']);
        $this->assertEquals($check->asArray()['sectoral_check_props'][0],
                            ['federal_id' => '001',
                            'date' => '25.10.2020',
                            'number' => '1',
                            'value' => 'значение отраслевого реквизита']);
        $this->assertEquals($check->asArray()['sectoral_check_props'][1],
                            ['federal_id' => '002',
                            'date' => '25.10.2020',
                            'number' => '1',
                            'value' => 'значение отраслевого реквизита']);
        $this->assertEquals($check->asArray()['operating_check_props'],
                             ['name' => '0',
                             'value' => 'данные операции',
                             'timestamp' => '12.03.2020 16:55:25']);
        $this->assertEquals($check->asArray()['additional_check_props'], 'доп. реквизит чека');
        $this->assertEquals($check->asArray()['callback_url'],
         'https://test.ru/callback-url');
        $this->assertEquals($check->asArray()['print'], true);
        $this->assertEquals($check->asArray()['cashier'],
                            ['name' => 'Иванов И.П.',
                             'inn' => '012345678912']);

        $this->assertEquals($position->asArray(),
                            ['id' => '123456',
                            'name' => 'name',
                            'price' => 100,
                            'quantity' => 1,
                            'total' => 50.0,
                            'vat' => Vat::RATE_20,
                            'measure' => Measure::MILLILITER,
                            'payment_method' => PaymentMethod::PRE_PAYMENT_FULL,
                            'payment_object' => PaymentObject::PROPERTY_RIGHT,
                            'agent_info' => ['type' => Agent::COMMISSIONAIRE],
                            'supplier_info' => ['phones' => [+77777777777],
                                                'name' => "ООО 'Лютик'",
                                                'inn' => "502906602876"],
                            'sectoral_item_props' =>
                                               [0 =>
                                               ['federal_id' => '001',
                                               'date' => '25.10.2020',
                                               'number' => '1',
                                               'value' => 'значение отраслевого реквизита'],
                                               1 =>
                                               ['federal_id' => '002',
                                                'date' => '25.10.2020',
                                                'number' => '1',
                                                'value' => 'значение отраслевого реквизита'
                                               ]
                            ],
                            'user_data' => 'Дополнительный реквизит предмета расчета',
                            'excise' => 25,
                            'country_code' => '5',
                            'declaration_number' => '15',
                            'mark_quantity' => ['numerator' => 1,
                                                'denominator' => 2],
                            'mark_code' => ['gs1m' => '0123455g54drgdfsgre54st5ergdfg']
                        ]);
    }

    public function testAddBuyerWithAllOptionalParams()
    {
        $buyer = new Buyer();
        $buyer->setEmail('test@test.ru');
        $buyer->setName('name');
        $buyer->setINN('0123456789');
        $buyer->setPhone('+79099099999');
        $buyer->setBirthdate('02.03.1995');
        $buyer->setCitizenship('Citizenship');
        $buyer->setDocumentCode('564');
        $buyer->setDocumentData('02.03.1995');
        $buyer->setAddress('ул. Московская');
        $company = new Company(TaxSystem::COMMON, 'Офис 3');

        $check = Check::createSell('123', $buyer, $company);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $check->addPosition($position);

        $this->assertEquals($check->asArray()['client'],
                            ['email' => 'test@test.ru',
                            'name' => 'name',
                            'inn' => '0123456789',
                            'phone' => '+79099099999',
                            'birthdate' => '02.03.1995',
                            'citizenship' => 'Citizenship',
                            'document_code' => '564',
                            'document_data' => '02.03.1995',
                            'address' => 'ул. Московская']);
    }

    public function testAddCompanyWithAllOptionalParams()
    {
        $buyer = new Buyer();
        $buyer->setEmail('test@test.ru');
        $company = new Company(TaxSystem::COMMON, 'Офис 3');
        $company->setPlaceAddress('г. Москва');
        $company->setINN('502906602876');

        $check = Check::createSell('123', $buyer, $company);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $check->addPosition($position);

        $this->assertEquals($check->asArray()['company'],
                            ['sno' => 0,
                            'payment_address' => 'Офис 3',
                            'place_address' => 'г. Москва',
                            'inn' => '502906602876']);
    }

    public function testCreateWithTrueWholesaleFlag()
    {
        $buyer = new Buyer();
        $buyer->setEmail('test@test.ru');
        $company = new Company(TaxSystem::COMMON, 'Офис 3');
        $company->setPlaceAddress('г. Москва');
        $company->setINN('502906602876');

        $check = Check::createSell('123', $buyer, $company);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $position->setWholesale(true);
        $check->addPosition($position);

        $this->assertEquals($check->asArray()['positions'][0]['wholesale'], true);
    }

    public function testCreateWithFalseWholesaleFlag()
    {
        $buyer = new Buyer();
        $buyer->setEmail('test@test.ru');
        $company = new Company(TaxSystem::COMMON, 'Офис 3');
        $company->setPlaceAddress('г. Москва');
        $company->setINN('502906602876');

        $check = Check::createSell('123', $buyer, $company);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $position->setWholesale(false);
        $check->addPosition($position);

        $this->assertEquals($check->asArray()['positions'][0]['wholesale'], false);
    }

    public function testSetShouldPrint()
    {
        $this->check->setShouldPrint(true);

        $this->assertEquals($this->check->asArray()['print'], true);
    }

    public function testAddCashierWithINN()
    {
        $cashier = new Cashier('Иваров И.П.', '1234567890123');
        $this->check->addCashier($cashier);

        $this->assertEquals($this->check->asArray()['cashier']['name'], 'Иваров И.П.');
        $this->assertEquals($this->check->asArray()['cashier']['inn'], '1234567890123');
    }

    public function testAddCashierWithoutINN()
    {
        $cashier = new Cashier('Иваров И.П.');
        $this->check->addCashier($cashier);

        $this->assertEquals($this->check->asArray()['cashier']['name'], 'Иваров И.П.');
        $this->assertArrayNotHasKey('inn',$cashier->asArray());
    }

     public function testSetAdditionalCheckProps()
     {
         $this->check->setAdditionalCheckProps("Дополнительный реквизит");

         $this->assertEquals($this->check->asArray()['additional_check_props'], 'Дополнительный реквизит');
     }

     public function testSetAdditionalUserProps()
     {
         $additional_user_props = new AdditionalUserProps('name_props', 'value_props');
         $this->check->setAdditionalUserProps($additional_user_props);
         $this->assertEquals($this->check->asArray()['additional_user_props'],
                             ['name' => 'name_props', 'value' => 'value_props']);
     }

     public function testSetOneSectoralCheckProps()
     {
         $sectoral_check_props = new SectoralCheckProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
         $this->check->setSectoralCheckProps($sectoral_check_props);
         $this->assertEquals($this->check->asArray()['sectoral_check_props'][0],
                             ['federal_id' => '001',
                             'date' => '25.10.2020',
                             'number' => '1',
                             'value' => 'значение отраслевого реквизита']);
     }

     public function testSetThreeSectoralCheckProps()
     {
         $sectoral_check_props = new SectoralCheckProps('001', '25.10.2020', '1', 'значение отраслевого реквизита');
         $sectoral_check_props1 = new SectoralCheckProps('002', '26.10.2020', '2', 'значение отраслевого реквизита2');
         $sectoral_check_props2 = new SectoralCheckProps('003', '27.10.2020', '3', 'значение отраслевого реквизита3');
         $this->check->setSectoralCheckProps($sectoral_check_props);
         $this->check->setSectoralCheckProps($sectoral_check_props1);
         $this->check->setSectoralCheckProps($sectoral_check_props2);
         $this->assertEquals($this->check->asArray()['sectoral_check_props'][0],
                             ['federal_id' => '001',
                             'date' => '25.10.2020',
                             'number' => '1',
                             'value' => 'значение отраслевого реквизита']);
         $this->assertEquals($this->check->asArray()['sectoral_check_props'][1],
                             ['federal_id' => '002',
                             'date' => '26.10.2020',
                             'number' => '2',
                             'value' => 'значение отраслевого реквизита2']);
         $this->assertEquals($this->check->asArray()['sectoral_check_props'][2],
                             ['federal_id' => '003',
                             'date' => '27.10.2020',
                             'number' => '3',
                             'value' => 'значение отраслевого реквизита3']);
     }

     public function testsetOperatingCheckProps()
     {
         $operating_check_props = new OperatingCheckProps('0', 'данные операции', '12.03.2020 16:55:25');
         $this->check->setOperatingCheckProps($operating_check_props);
         $this->assertEquals($this->check->asArray()['operating_check_props'],
                             ['name' => '0',
                             'value' => 'данные операции',
                             'timestamp' => '12.03.2020 16:55:25']);
     }

    public function testApplyDiscount()
    {
        $clientEmail = 'test@test.ru';
        $payment_address = 'Офис 3';
        $buyer = new Buyer();
        $buyer->setEmail($clientEmail);
        $company = new Company(TaxSystem::COMMON, $payment_address);
        $check = new Check('id1', Check::INTENT_SELL, $buyer, $company);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;

        $position1 = new Position('position1', 100.0, 1, 100.0, $vat, $measure, $payment_method, $payment_object);
        $position2 = new Position('position2', 25.0, 2, 40.0, $vat, $measure, $payment_method, $payment_object);
        $position3 = new Position('position3', 5.0, 1, 5.0, $vat, $measure, $payment_method, $payment_object);

        $payment1 = new Payment(Payment::TYPE_CARD, 110.0);
        $payment2 = new Payment(Payment::TYPE_CARD, 20.0);

        $check->addPayment($payment1);
        $check->addPayment($payment2);
        $check->addPosition($position1);
        $check->addPosition($position2);
        $check->addPosition($position3);

        $discount = 15.0;

        $check->applyDiscount($discount);

        $positionsTotal = 0;
        $positions = $check->getPositions();
        foreach( $positions as $position )
        {
            $positionsTotal += $position->getTotal();
        }

        $this->assertEquals($positionsTotal, 130.0);
        $this->assertEquals($positions[0]->getTotal(), 89.66);
     }

    public function testSetCallbackUrl()
    {
        $this->check->setCallbackUrl("http://localhost:8110/index.php/shop/komtetkassa/success/");
        $this->assertEquals($this->check->asArray()['callback_url'],
                            'http://localhost:8110/index.php/shop/komtetkassa/success/');
    }
}
