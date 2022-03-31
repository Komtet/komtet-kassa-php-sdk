<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\Agent;
use Komtet\KassaSdk\v1\CalculationMethod;
use Komtet\KassaSdk\v1\CalculationSubject;
use Komtet\KassaSdk\v1\Cashier;
use Komtet\KassaSdk\v1\Check;
use Komtet\KassaSdk\v1\Nomenclature;
use Komtet\KassaSdk\v1\Payment;
use Komtet\KassaSdk\v1\Position;
use Komtet\KassaSdk\v1\QueueManager;
use Komtet\KassaSdk\v1\TaxSystem;
use Komtet\KassaSdk\v1\Vat;
use PHPUnit\Framework\TestCase;

class QueueManagerTest extends TestCase
{
    private $client;
    private $qm;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Komtet\KassaSdk\v1\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->qm = new QueueManager($this->client);
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
            ->with($this->equalTo('api/shop/v1/queues/queue-id'))
            ->willReturn(['state' => 'active']);
        $this->assertTrue($this->qm->isQueueActive('my-queue'));
    }

    public function testIsQueueActiveFalseWithPassive()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo('api/shop/v1/queues/queue-id'))
            ->willReturn(['state' => 'passive']);
        $this->assertFalse($this->qm->isQueueActive('my-queue'));
    }

    public function testIsQueueActiveFalseWithNonArray()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo('api/shop/v1/queues/queue-id'))
            ->willReturn('string');
        $this->assertFalse($this->qm->isQueueActive('my-queue'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Default queue is not set
     */
    public function testPutCheckFailedWithoutDefaultQueue()
    {
        $check = $this->getMockBuilder('\Komtet\KassaSdk\v1\Check')->disableOriginalConstructor()->getMock();
        $this->qm->putCheck($check);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown queue "my-queue"
     */
    public function testPutCheckFailedWithUnregisteredQueue()
    {
        $check = $this->getMockBuilder('\Komtet\KassaSdk\v1\Check')->disableOriginalConstructor()->getMock();
        $this->qm->putCheck($check, 'my-queue');
    }

    public function testPutCheckToDefaultQueueSucceeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $vat = new Vat('20%');
        $position = new Position('name', 100, 1, 100, $vat);
        $position->setId('123');
        $payment = new Payment(Payment::TYPE_CARD, 100);

        $check = Check::createSell('id', 'user@host', TaxSystem::COMMON);
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'value'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check), $rep);
    }

    public function testPutCheckToCustomQueueSucceeded()
    {
        $this->qm->registerQueue('default-queue', 'default-id');
        $this->qm->setDefaultQueue('default-queue');
        $this->qm->registerQueue('my-queue', 'queue-id');

        $vat = new Vat('no');
        $position = (new Position('name', 100, 1, 100, $vat))->setMeasureName('Kg');
        $payment = new Payment(Payment::TYPE_CASH, 100);
        $this->assertEquals($payment->getSum(), 100);

        $check = Check::createSellReturn('id', 'user@host', TaxSystem::COMMON)->setShouldPrint(true);
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $this->assertTrue($data['print']);
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
    }

    public function testPutBuyCheckSucceeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $vat = new Vat('20%');
        $position = new Position('name', 100, 1, 100, $vat);
        $position->setId('123');
        $payment = new Payment(Payment::TYPE_CARD, 100);

        $check = Check::createBuy('id', 'user@host', TaxSystem::COMMON);
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'value'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check), $rep);
    }

    public function testPutBuyReturnCheckSucceeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $vat = new Vat('20%');
        $position = new Position('name', 100, 1, 100, $vat);
        $position->setId('123');
        $payment = new Payment(Payment::TYPE_CARD, 100);

        $check = Check::createBuyReturn('id', 'user@host', TaxSystem::COMMON);
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'value'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check), $rep);
    }

    public function testPutCheckFFD105Succeeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $vat = new Vat('20%');
        $position = new Position('name', 100, 1, 100, $vat);
        $position->setId('123');
        $position->setCalculationMethod(CalculationMethod::FULL_PAYMENT);
        $position->setCalculationSubject(CalculationSubject::PRODUCT);
        $position->setExcise(19.89);
        $position->setCountryCode('643');
        $position->setDeclarationNumber('10129000/220817/0211234');

        $agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "12345678901");
        $position->setAgent($agent);

        $nomenclature = new Nomenclature('019876543210123421sgEKKPPcS25y5');
        $position->setNomenclature($nomenclature);

        $check = Check::createSell('id', 'user@host', TaxSystem::COMMON);
        $check->addPosition($position);

        $payment = new Payment(Payment::TYPE_CARD, 100);
        $check->addPayment($payment);

        $cashier = new Cashier('Иваров И.П.', '1234567890123');
        $check->addCashier($cashier);

        $data = $check->asArray();
        $this->assertTrue(array_key_exists('supplier_info', $data['positions'][0]));
        $this->assertFalse(array_key_exists('supplier_info', $data['positions'][0]['agent_info']));
        $this->assertEquals(
            json_encode($data['positions'][0]['nomenclature_code']),
            '{"code":"019876543210123421sgEKKPPcS25y5"}'
        );

        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'value'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);

        $this->assertEquals($this->qm->putCheck($check), $rep);
    }
}
