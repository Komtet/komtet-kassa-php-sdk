<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\Check;
use Komtet\KassaSdk\Correction;
use Komtet\KassaSdk\CorrectionCheck;
use Komtet\KassaSdk\Payment;
use Komtet\KassaSdk\Position;
use Komtet\KassaSdk\QueueManager;
use Komtet\KassaSdk\TaxSystem;
use Komtet\KassaSdk\Vat;

class QueueManagerTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $qm;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Komtet\KassaSdk\Client')
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
        $check = $this->getMockBuilder('\Komtet\KassaSdk\Check')->disableOriginalConstructor()->getMock();
        $this->qm->putCheck($check);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown queue "my-queue"
     */
    public function testPutCheckFailedWithUnregisteredQueue()
    {
        $check = $this->getMockBuilder('\Komtet\KassaSdk\Check')->disableOriginalConstructor()->getMock();
        $this->qm->putCheck($check, 'my-queue');
    }

    public function testPutCheckToDefaultQueueSucceeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $vat = new Vat('18%');
        $position = new Position('name', 100, 1, 100, 0, $vat);
        $position->setId('123');
        $payment = Payment::createCard(100);

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
        $position = (new Position('name', 100, 1, 100, 0, $vat))->setMeasureName('Kg');
        $payment = Payment::createCash(100);
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

    public function testPutSellCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $correction = Correction::createSelf('2012-12-21', '4815162342', 'description');
        $check = CorrectionCheck::createSell('4815162342', '4815162342', TaxSystem::PATENT, $correction);
        $payment = Payment::createCard(4815);
        $vat = new Vat('118');
        $check->setPayment($payment, $vat);
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);
        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
    }

    public function testPutSellReturnCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $correction = Correction::createForced('2012-12-21', '4815162342', 'description');
        $check = CorrectionCheck::createSellReturn('4815162342', '4815162342', TaxSystem::PATENT, $correction);
        $payment = Payment::createCard(4815);
        $vat = new Vat('118');
        $check->setPayment($payment, $vat);
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);
        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
    }
}
