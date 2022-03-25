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
use Komtet\KassaSdk\v2\QueueManager;
use Komtet\KassaSdk\v2\SectoralCheckProps;
use Komtet\KassaSdk\v2\SectoralItemProps;
use Komtet\KassaSdk\v2\TaxSystem;
use Komtet\KassaSdk\v2\Vat;
use PHPUnit\Framework\TestCase;

class QueueManagerTest extends TestCase
{
    private $buyer;
    private $check;
    private $client;
    private $company;
    private $payment;
    private $position;
    private $qm;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Komtet\KassaSdk\v2\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->qm = new QueueManager($this->client);

        $clientEmail = 'test@test.ru';
        $payment_address = 'Офис 3';
        $this->buyer = new Buyer($clientEmail);
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

    public function testRegisterQueue()
    {
        $this->assertFalse($this->qm->hasQueue('my-queue'));
        $this->assertTrue($this->qm->registerQueue('my-queue', 'queue-id')->hasQueue('my-queue'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetDefaultQueueFailedWithUnregisteredQueue()
    {
        $this->qm->setDefaultQueue('my-queue');
    }

    public function testSetDefaultQueueSucceeded()
    {
        $qm = $this->qm->registerQueue('my-queue', 'queue-id')->setDefaultQueue('my-queue');
        $this->assertEquals($qm, $this->qm);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsQueueActiveFailedWithUnregisteredQueue()
    {
        $this->qm->isQueueActive('my-queue');
    }

    public function testIsQueueActiveTrue()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo('api/shop/v2/queues/queue-id'))
            ->willReturn(['state' => 'active']);
        $this->assertTrue($this->qm->isQueueActive('my-queue'));
    }

    public function testIsQueueActiveFalseWithPassive()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo('api/shop/v2/queues/queue-id'))
            ->willReturn(['state' => 'passive']);
        $this->assertFalse($this->qm->isQueueActive('my-queue'));
    }

    public function testIsQueueActiveFalseWithNonArray()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo('api/shop/v2/queues/queue-id'))
            ->willReturn('string');
        $this->assertFalse($this->qm->isQueueActive('my-queue'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Default queue is not set
     */
    public function testPutCheckFailedWithoutDefaultQueue()
    {
        $check = $this->getMockBuilder('\Komtet\KassaSdk\v2\Check')->disableOriginalConstructor()->getMock();
        $this->qm->putCheck($check);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown queue "my-queue"
     */
    public function testPutCheckFailedWithUnregisteredQueue()
    {
        $check = $this->getMockBuilder('\Komtet\KassaSdk\v2\Check')->disableOriginalConstructor()->getMock();
        $this->qm->putCheck($check, 'my-queue');
    }

    public function testPutCheckToDefaultQueueSucceeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $data = $this->check->asArray();
        $path = 'api/shop/v2/queues/queue-id/task';
        $rep = ['key' => 'value'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($this->check), $rep);
    }

    public function testPutCheckToCustomQueueSucceeded()
    {
        $this->qm->registerQueue('default-queue', 'default-id');
        $this->qm->setDefaultQueue('default-queue');
        $this->qm->registerQueue('my-queue', 'queue-id');

        $data = $this->check->asArray();
        $path = 'api/shop/v2/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($this->check, 'my-queue'), $rep);
    }

    public function testPutBuyCheckSucceeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $clientEmail = 'test@test.ru';
        $payment_address = 'Офис 3';
        $buyer = new Buyer($clientEmail);
        $company = new Company(TaxSystem::COMMON, $payment_address);
        $check = Check::createBuy('id1', $buyer, $company);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $check->addPosition($position);

        $payment = new Payment(Payment::TYPE_CARD, 100.0);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v2/queues/queue-id/task';
        $rep = ['key' => 'value'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check), $rep);
    }

    public function testPutBuyReturnCheckSucceeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $clientEmail = 'test@test.ru';
        $payment_address = 'Офис 3';
        $buyer = new Buyer($clientEmail);
        $company = new Company(TaxSystem::COMMON, $payment_address);
        $check = Check::createBuyReturn('id1', $buyer, $company);

        $vat = new Vat(Vat::RATE_20);
        $measure = Measure::MILLILITER;
        $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
        $payment_object = PaymentObject::PROPERTY_RIGHT;
        $position = new Position('name', 100, 1, 100, $vat, $measure, $payment_method, $payment_object);
        $check->addPosition($position);

        $payment = new Payment(Payment::TYPE_CARD, 100.0);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v2/queues/queue-id/task';
        $rep = ['key' => 'value'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check), $rep);
    }

    public function testPutCheckFFD_1_2_Succeeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $checkID = '';
        $clientEmail = 'test@test.ru';
        $payment_address = 'Офис 3';

        $buyer = new Buyer($clientEmail);
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

        $data = $check->asArray();
        $this->assertTrue(array_key_exists('supplier_info', $data['positions'][0]));
        $this->assertFalse(array_key_exists('supplier_info', $data['positions'][0]['agent_info']));
        $this->assertEquals(
            json_encode($data['positions'][0]['mark_code']),
            '{"gs1m":"0123455g54drgdfsgre54st5ergdfg"}'
        );

        $path = 'api/shop/v2/queues/queue-id/task';
        $rep = ['key' => 'value'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check), $rep);
    }
}
