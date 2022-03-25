<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\AdditionalUserProps;
use Komtet\KassaSdk\v2\AuthorisedPerson;
use Komtet\KassaSdk\v2\Buyer;
use Komtet\KassaSdk\v2\Cashier;
use Komtet\KassaSdk\v2\CorrectionInfo;
use Komtet\KassaSdk\v2\CorrectionCheck;
use Komtet\KassaSdk\v2\Company;
use Komtet\KassaSdk\v2\Measure;
use Komtet\KassaSdk\v2\OperatingCheckProps;
use Komtet\KassaSdk\v2\Payment;
use Komtet\KassaSdk\v2\PaymentMethod;
use Komtet\KassaSdk\v2\PaymentObject;
use Komtet\KassaSdk\v2\Position;
use Komtet\KassaSdk\v2\QueueManager;
use Komtet\KassaSdk\v2\SectoralCheckProps;
use Komtet\KassaSdk\v2\TaxSystem;
use Komtet\KassaSdk\v2\Vat;
use PHPUnit\Framework\TestCase;

class CorrectionCheckTest extends TestCase
{

    private $client;
    private $qm;
    private $company;
    private $check;
    private $position;
    private $payment;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Komtet\KassaSdk\v2\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->qm = new QueueManager($this->client);

        $payment_address = 'Офис 3';
        $this->company = new Company(TaxSystem::COMMON, $payment_address);
        $correction_info = new CorrectionInfo('self', '31.01.2021', '1', 'Наименование документа основания для коррекции');
        $this->check = CorrectionCheck::createSellCorrection('4815162342', $this->company, $correction_info);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $this->position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $this->payment = new Payment(Payment::TYPE_CARD, 100);
        $this->check->addPosition($this->position);
        $this->check->addPayment($this->payment);
    }

    public function testRegisterQueue()
    {
        $this->assertFalse($this->qm->hasQueue('my-queue'));
        $this->assertTrue($this->qm->registerQueue('my-queue', 'queue-id')->hasQueue('my-queue'));
    }

    public function testSellCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $payment_address = 'Офис 3';
        $company = new Company(TaxSystem::COMMON, $payment_address);
        $correction_info = new CorrectionInfo('self', '31.01.2021', '1', 'Наименование документа основания для коррекции');
        $check = CorrectionCheck::createSellCorrection('4815162342', $company, $correction_info);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $payment = new Payment(Payment::TYPE_CARD, 100);
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v2/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
        $this->assertEquals($check->asArray()['company'], 
                            ['sno' => 0,
                            'payment_address' => 'Офис 3']);
        $this->assertEquals($check->asArray()['correction_info'], 
                            ['type' => 'self',
                            'base_date' => '31.01.2021',
                            'base_number' => '1',
                            'base_name' => 'Наименование документа основания для коррекции']);
        $this->assertEquals($position->asArray(),
                            ['name' => 'name',
                            'price' => 100,
                            'quantity' => 1,
                            'total' => 100,
                            'vat' => Vat::RATE_20,
                            'measure' => Measure::MILLILITER,
                            'payment_method' => PaymentMethod::PRE_PAYMENT_FULL,
                            'payment_object' => PaymentObject::PROPERTY_RIGHT,'sectoral_item_props' => []
                            ]
                        );

    }

    public function testPutSellReturnCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $payment_address = 'Офис 3';
        $company = new Company(TaxSystem::COMMON, $payment_address);
        $correction_info = new CorrectionInfo('instruction', '31.01.2021', '1', 'Наименование документа основания для коррекции');
        $check = CorrectionCheck::createSellReturnCorrection('4815162342', $company, $correction_info);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $payment = new Payment(Payment::TYPE_CARD, 100);
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v2/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
        $this->assertEquals($check->asArray()['company'], 
                            ['sno' => 0,
                            'payment_address' => 'Офис 3']);
        $this->assertEquals($check->asArray()['correction_info'], 
                            ['type' => 'instruction',
                            'base_date' => '31.01.2021',
                            'base_number' => '1',
                            'base_name' => 'Наименование документа основания для коррекции']);
        $this->assertEquals($position->asArray(),
                            ['name' => 'name',
                            'price' => 100,
                            'quantity' => 1,
                            'total' => 100,
                            'vat' => Vat::RATE_20,
                            'measure' => Measure::MILLILITER,
                            'payment_method' => PaymentMethod::PRE_PAYMENT_FULL,
                            'payment_object' => PaymentObject::PROPERTY_RIGHT,'sectoral_item_props' => []
                            ]
                        );
    }

    public function testPutBuyCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $payment_address = 'Офис 3';
        $company = new Company(TaxSystem::COMMON, $payment_address);
        $correction_info = new CorrectionInfo('self', '31.01.2021', '1', 'Наименование документа основания для коррекции');
        $check = CorrectionCheck::createBuyCorrection('4815162342', $company, $correction_info);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $payment = new Payment(Payment::TYPE_CARD, 100);
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v2/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
        $this->assertEquals($check->asArray()['company'], 
                            ['sno' => 0,
                            'payment_address' => 'Офис 3']);
        $this->assertEquals($check->asArray()['correction_info'], 
                            ['type' => 'self',
                            'base_date' => '31.01.2021',
                            'base_number' => '1',
                            'base_name' => 'Наименование документа основания для коррекции']);
        $this->assertEquals($position->asArray(),
                            ['name' => 'name',
                            'price' => 100,
                            'quantity' => 1,
                            'total' => 100,
                            'vat' => Vat::RATE_20,
                            'measure' => Measure::MILLILITER,
                            'payment_method' => PaymentMethod::PRE_PAYMENT_FULL,
                            'payment_object' => PaymentObject::PROPERTY_RIGHT,'sectoral_item_props' => []
                            ]
                        );
    }

    public function testPutBuyReturnCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $payment_address = 'Офис 3';
        $company = new Company(TaxSystem::COMMON, $payment_address);
        $correction_info = new CorrectionInfo('instruction', '31.01.2021', '1', 'Наименование документа основания для коррекции');
        $check = CorrectionCheck::createBuyReturnCorrection('4815162342', $company, $correction_info);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $payment = new Payment(Payment::TYPE_CARD, 100);
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v2/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
        $this->assertEquals($check->asArray()['company'], 
                            ['sno' => 0,
                            'payment_address' => 'Офис 3']);
        $this->assertEquals($check->asArray()['correction_info'], 
                            ['type' => 'instruction',
                            'base_date' => '31.01.2021',
                            'base_number' => '1',
                            'base_name' => 'Наименование документа основания для коррекции']);
        $this->assertEquals($position->asArray(),
                            ['name' => 'name',
                            'price' => 100,
                            'quantity' => 1,
                            'total' => 100,
                            'vat' => Vat::RATE_20,
                            'measure' => Measure::MILLILITER,
                            'payment_method' => PaymentMethod::PRE_PAYMENT_FULL,
                            'payment_object' => PaymentObject::PROPERTY_RIGHT,'sectoral_item_props' => []
                            ]
                        );
    }

    public function testSetBuyer()
    {
        $buyer = new Buyer('test@test.ru');
        $this->check->setBuyer($buyer);

        $this->assertEquals($this->check->asArray()['client']['email'], 'test@test.ru');
    }

    public function testAddBuyerWithAllOptionalParams()
    {
        $buyer = new Buyer('test@test.ru');
        $buyer->setName('name');
        $buyer->setINN('0123456789');
        $buyer->setPhone('+79099099999');
        $buyer->setBirthdate('02.03.1995');
        $buyer->setCitizenship('Citizenship');
        $buyer->setDocumentCode('564');
        $buyer->setDocumentData('02.03.1995');
        $buyer->setAddress('ул. Московская');
        $this->check->setBuyer($buyer);

        $this->assertEquals($this->check->asArray()['client'], 
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

    public function testAddCashierNameWithInn()
    {
        $cashier = new Cashier('Иваров И.П.', '1234567890123');
        $this->check->AddCashier($cashier);
        $this->assertEquals($this->check->asArray()['cashier']['name'], 'Иваров И.П.');
        $this->assertEquals($this->check->asArray()['cashier']['inn'], '1234567890123');
    }

    public function testAddCashierNameWithoutInn()
    {
        $cashier = new Cashier('Иваров И.П.');
        $this->check->AddCashier($cashier);
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


    public function testAuthorisedPersonWithoutINN()
    {
        $authorised_person = new AuthorisedPerson('name');
        $this->check->setAuthorisedPerson($authorised_person);
        $this->assertEquals($this->check->asArray()['authorised_person'],
                            ['name' => 'name']);
    }

    public function testAuthorisedPersonWithINN()
    {
        $authorised_person = new AuthorisedPerson('name', 'inn');
        $this->check->setAuthorisedPerson($authorised_person);
        $this->assertEquals($this->check->asArray()['authorised_person'],
                            ['name' => 'name',
                             'inn' => 'inn']);
    }

    public function testSetCallbackUrl()
    {
        $this->check->setCallbackUrl("http://test.ru/success");
        $this->assertEquals($this->check->asArray()['callback_url'],
                            'http://test.ru/success');
    }

}