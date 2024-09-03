<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\AdditionalUserProps;
use Komtet\KassaSdk\v1\AuthorisedPerson;
use Komtet\KassaSdk\v1\Buyer;
use Komtet\KassaSdk\v1\Cashier;
use Komtet\KassaSdk\v1\Correction;
use Komtet\KassaSdk\v1\CorrectionCheck;
use Komtet\KassaSdk\v1\Payment;
use Komtet\KassaSdk\v1\Position;
use Komtet\KassaSdk\v1\QueueManager;
use Komtet\KassaSdk\v1\TaxSystem;
use Komtet\KassaSdk\v1\Vat;
use PHPUnit\Framework\TestCase;

class CorrectionCheckTest extends TestCase
{
    private $client;
    private $qm;

    protected function setUp(): void
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


    public function testPutSellCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $correction = Correction::createSelf('2012-12-21', '4815162342', 'description');
        $check = CorrectionCheck::createSellCorrection('4815162342', TaxSystem::PATENT, $correction);
        $vat = new Vat(Vat::RATE_20);
        $position = new Position('name', 100, 1, 100, $vat);
        $payment = new Payment(Payment::TYPE_CARD, 100);
        $cashier = new Cashier('Иваров И.П.', '1234567890123');
        $check->addCashier($cashier);
        $check->addPosition($position);
        $check->addPayment($payment);
        $authorised_person = new AuthorisedPerson('Иваров И.И.', '123456789012');
        $check->setAuthorisedPerson($authorised_person);
        $buyer = new Buyer('Пупкин П.П.', '123412341234');
        $check->addBuyer($buyer);
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);
        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
        $this->assertEquals($check->asArray()['client']['name'], 'Пупкин П.П.');
        $this->assertEquals($check->asArray()['client']['inn'], '123412341234');
    }

    public function testPutSellReturnCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $correction = Correction::createForced('2012-12-21', '4815162342', 'description');
        $check = CorrectionCheck::createSellReturnCorrection('4815162342', TaxSystem::PATENT, $correction);
        $vat = new Vat(Vat::RATE_20);
        $position = new Position('name', 100, 1, 100, $vat);
        $payment = new Payment(Payment::TYPE_CARD, 100);
        $cashier = new Cashier('Иваров И.П.', '1234567890123');
        $check->addCashier($cashier);
        $check->addPosition($position);
        $check->addPayment($payment);
        $authorised_person = new AuthorisedPerson('Иваров И.И.', '123456789012');
        $check->setAuthorisedPerson($authorised_person);
        $buyer = new Buyer();
        $buyer->setName('Пупкин П.П.');
        $check->addBuyer($buyer);
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);
        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
        $this->assertEquals($check->asArray()['client']['name'], 'Пупкин П.П.');
        $this->assertArrayNotHasKey('inn', $check->asArray()['client']);
    }

    public function testPutBuyCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $correction = Correction::createSelf('2012-12-21', '4815162342', 'description');
        $check = CorrectionCheck::createBuyCorrection('4815162342', TaxSystem::PATENT, $correction);
        $vat = new Vat(Vat::RATE_20);
        $position = new Position('name', 100, 1, 100, $vat);
        $payment = new Payment(Payment::TYPE_CARD, 100);
        $cashier = new Cashier('Иваров И.П.', '1234567890123');
        $check->addCashier($cashier);
        $check->addPosition($position);
        $check->addPayment($payment);
        $authorised_person = new AuthorisedPerson('Иваров И.И.', '123456789012');
        $check->setAuthorisedPerson($authorised_person);
        $buyer = new Buyer();
        $buyer->setINN('123412341234');
        $check->addBuyer($buyer);
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);
        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
        $this->assertEquals($check->asArray()['client']['inn'], '123412341234');
        $this->assertArrayNotHasKey('name', $check->asArray()['client']);
    }

    public function testPutBuyReturnCorrectionCheckSucceded()
    {
        $this->qm->registerQueue('my-queue', 'queue-id');
        $correction = Correction::createSelf('2012-12-21', '4815162342', 'description');
        $check = CorrectionCheck::createBuyReturnCorrection('4815162342', TaxSystem::PATENT, $correction);
        $vat = new Vat(Vat::RATE_20);
        $position = new Position('name', 100, 1, 100, $vat);
        $payment = new Payment(Payment::TYPE_CARD, 100);
        $cashier = new Cashier('Иваров И.П.', '1234567890123');
        $additional_user_props = new AdditionalUserProps('name_props', 'value_props');
        $authorised_person = new AuthorisedPerson('Иваров И.И.', '123456789012');
        $check->addCashier($cashier);
        $check->addPosition($position);
        $check->addPayment($payment);
        $check->setAdditionalCheckProps("Дополнительный реквизит");
        $check->setAdditionalUserProps($additional_user_props);
        $check->setAuthorisedPerson($authorised_person);
        $check->setShouldPrint(true);
        $check->setCallbackUrl("http://localhost:8110/index.php/shop/komtetkassa/success/");
        $data = $check->asArray();
        $path = 'api/shop/v1/queues/queue-id/task';
        $rep = ['key' => 'val'];
        $this->client->expects($this->once())->method('sendRequest')->with($path, $data)->willReturn($rep);
        $this->assertEquals($this->qm->putCheck($check, 'my-queue'), $rep);
    }
}