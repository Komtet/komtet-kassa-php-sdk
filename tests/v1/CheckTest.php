<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\AdditionalUserProps;
use Komtet\KassaSdk\v1\Agent;
use Komtet\KassaSdk\v1\Buyer;
use Komtet\KassaSdk\v1\CalculationMethod;
use Komtet\KassaSdk\v1\CalculationSubject;
use Komtet\KassaSdk\v1\Cashier;
use Komtet\KassaSdk\v1\Check;
use Komtet\KassaSdk\v1\Nomenclature;
use Komtet\KassaSdk\v1\Payment;
use Komtet\KassaSdk\v1\Position;
use Komtet\KassaSdk\v1\Vat;
use PHPUnit\Framework\TestCase;

class CheckTest extends TestCase
{
    public function testCreationCheckSuccess()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1, 'Адрес');
        $this->assertEquals($check->asArray()['payment_address'], 'Адрес');

        $check = Check::createSell('id2', 'test@test.test', 1, 'ул.Пушкина, д.8');
        $this->assertEquals($check->asArray()['payment_address'], 'ул.Пушкина, д.8');

        $check = Check::createSellReturn('id3', 'test@test.test', 1, 'ул.Гагарина, д.12');
        $this->assertEquals($check->asArray()['payment_address'], 'ул.Гагарина, д.12');

        $check = Check::createBuy('id4', 'test@test.test', 1, 'ул.Дзержинского, д.11');
        $this->assertEquals($check->asArray()['payment_address'], 'ул.Дзержинского, д.11');

        $check = Check::createBuyReturn('id5', 'test@test.test', 1, 'ул.Московская, д.13');
        $this->assertEquals($check->asArray()['payment_address'], 'ул.Московская, д.13');
    }

    public function testSetShouldPrint()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);
        $check->setShouldPrint(true);

        $this->assertEquals($check->asArray()['print'], true);
    }

    public function testAddCashier()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);
        $cashier = new Cashier('Иваров И.П.', '1234567890123');
        $check->addCashier($cashier);

        $this->assertEquals($check->asArray()['cashier']['name'], 'Иваров И.П.');
        $this->assertEquals($check->asArray()['cashier']['inn'], '1234567890123');
    }

    public function testApplyDiscount()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);

        $vat = new Vat(0);
        $payment1 = new Payment(Payment::TYPE_CARD, 110.0);
        $payment2 = new Payment(Payment::TYPE_CARD, 20.0);
        $position1 = new Position('position1', 100.0, 1, 100.0, $vat);
        $position2 = new Position('position2', 25.0, 2, 40.0, $vat);
        $position3 = new Position('position3', 5.0, 1, 5.0, $vat);
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

    public function testCheckWithOptionalParams() 
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);

        $vat = new Vat(0);
        $agent = new Agent(Agent::COMMISSIONAIRE, "+77777777777", "ООО 'Лютик'", "502906602876");
        $nomenclature = new Nomenclature('kjgldfjgdfklg234234');
        $payment1 = new Payment(Payment::TYPE_CARD, 110.0);
        $payment2 = new Payment(Payment::TYPE_CARD, 20.0);
        $position1 = new Position('position1', 100.0, 1, 100.0, $vat);
        $position1->setId('123');
        $position1->setMeasureName('kg');
        $position1->setCalculationMethod(CalculationMethod::FULL_PAYMENT);
        $position1->setCalculationSubject(CalculationSubject::PAY);
        $position1->setExcise(1);
        $position1->setCountryCode('123');
        $position1->setDeclarationNumber('3456');
        $position1->setAgent($agent);
        $position1->setNomenclature($nomenclature);
        $position2 = new Position('position2', 25.0, 2, 40.0, $vat);
        $position3 = new Position('position3', 5.0, 1, 5.0, $vat);
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
        $this->assertEquals($position1->asArray()['id'], '123');
        $this->assertEquals($position1->asArray()['calculation_method'], CalculationMethod::FULL_PAYMENT);
        $this->assertEquals($position1->asArray()['calculation_subject'], CalculationSubject::PAY);
        $this->assertEquals($position1->asArray()['excise'], 1);
        $this->assertEquals($position1->asArray()['country_code'], '123');
        $this->assertEquals($position1->asArray()['declaration_number'], '3456');
        $this->assertEquals($position1->asArray()['agent_info']['type'], Agent::COMMISSIONAIRE);
        $this->assertEquals($position1->asArray()['supplier_info']['phones'], ["+77777777777"]);
        $this->assertEquals($position1->asArray()['supplier_info']['name'], "ООО 'Лютик'");
        $this->assertEquals($position1->asArray()['supplier_info']['inn'], "502906602876");
        $this->assertEquals($position1->asArray()['nomenclature_code']['code'], 'kjgldfjgdfklg234234');
    }

    public function testAddBuyerInfo()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);

        $vat = new Vat(0);
        $position = new Position('position1', 100.0, 1, 100.0, $vat);
        $check->addPosition($position);

        $payment = new Payment(Payment::TYPE_CARD, 100.0);
        $check->addPayment($payment);

        $buyer = new Buyer('Пупкин П.П.', '123412341234');
        $check->addBuyer($buyer);

        $this->assertEquals($check->asArray()['client']['name'], 'Пупкин П.П.');
        $this->assertEquals($check->asArray()['client']['inn'], '123412341234');
    }

    public function testAddBuyerWithName()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);

        $vat = new Vat(0);
        $position = new Position('position1', 100.0, 1, 100.0, $vat);
        $check->addPosition($position);

        $payment = new Payment(Payment::TYPE_CARD, 100.0);
        $check->addPayment($payment);

        $buyer = new Buyer();
        $buyer->setName('Пупкин П.П.');
        $check->addBuyer($buyer);

        $this->assertEquals($check->asArray()['client']['name'], 'Пупкин П.П.');
        $this->assertArrayNotHasKey('inn', $check->asArray()['client']);
    }

    public function testAddBuyerWithINN()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);

        $vat = new Vat(0);
        $position = new Position('position1', 100.0, 1, 100.0, $vat);
        $check->addPosition($position);

        $payment = new Payment(Payment::TYPE_CARD, 100.0);
        $check->addPayment($payment);

        $buyer = new Buyer();
        $buyer->setINN('123412341234');
        $check->addBuyer($buyer);

        $this->assertEquals($check->asArray()['client']['inn'], '123412341234');
        $this->assertArrayNotHasKey('name', $check->asArray()['client']);
    }

    public function testSetAdditionalCheckProps()
    {
        $check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);
        $check->setAdditionalCheckProps("Дополнительный реквизит");

        $this->assertEquals($check->asArray()['additional_check_props'], 'Дополнительный реквизит');
    }

    public function testSetCallbackUrl()
    {
        $check = new Check('id5', 'tests@tests.test', Check::INTENT_SELL, 1);
        $check->setCallbackUrl("http://localhost:8110/index.php/shop/komtetkassa/success/");
        $this->assertEquals($check->asArray()['callback_url'],
                            'http://localhost:8110/index.php/shop/komtetkassa/success/');
    }

    public function testSetAdditionalUserProps()
    {
        $check = new Check('id5', 'tests@tests.test', Check::INTENT_SELL, 1);
        $additional_user_props = new AdditionalUserProps('name_props', 'value_props');
        $check->setAdditionalUserProps($additional_user_props);
        $this->assertEquals($check->asArray()['additional_user_props'],
                            ['name' => 'name_props', 'value' => 'value_props']);
    }
}
