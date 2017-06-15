<?php

namespace MotmomTest\CloudKassaSdk;

use Motmom\CloudKassaSdk\Check;
use Motmom\CloudKassaSdk\Payment;
use Motmom\CloudKassaSdk\Position;
use Motmom\CloudKassaSdk\QueueManager;
use Motmom\CloudKassaSdk\Vat;
use PHPUnit\Framework\TestCase;

class QueueManagerTest extends TestCase
{
    private $client;
    private $qm;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Motmom\CloudKassaSdk\Client')
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
        $check = $this->getMockBuilder('\Motmom\CloudKassaSdk\Check')->disableOriginalConstructor()->getMock();
        $this->qm->putCheck($check);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown queue "my-queue"
     */
    public function testPutCheckFailedWithUnregisteredQueue()
    {
        $check = $this->getMockBuilder('\Motmom\CloudKassaSdk\Check')->disableOriginalConstructor()->getMock();
        $this->qm->putCheck($check, 'my-queue');
    }

    public function testPutCheckToDefaultQueueSucceeded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $this->qm->setDefaultQueue('my-queue');

        $vat = Vat::createPosition(0, Vat::TYPE_NO);
        $position = new Position('name', 100, 1, 100, 0, $vat);
        $payment = Payment::createCard(100);

        $check = Check::createSell('id', 'user@host');
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn([]);

        $this->assertNull($this->qm->putCheck($check));
    }

    public function testPutCheckToCustomQueueSucceeded()
    {
        $this->qm->registerQueue('default-queue', 'default-id');
        $this->qm->setDefaultQueue('default-queue');
        $this->qm->registerQueue('my-queue', 'queue-id');

        $vat = Vat::createUnit(0, Vat::TYPE_NO);
        $position = new Position('name', 100, 1, 100, 0, $vat);
        $payment = Payment::createCash(100);

        $check = Check::createSellReturn('id', 'user@host')->setShouldPrint(true);
        $check->addPosition($position);
        $check->addPayment($payment);
        $data = $check->asArray();
        $this->assertTrue($data['print']);
        $path = 'api/shop/v1/queues/queue-id/task';
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn([]);

        $this->assertNull($this->qm->putCheck($check, 'my-queue'));
    }
}
