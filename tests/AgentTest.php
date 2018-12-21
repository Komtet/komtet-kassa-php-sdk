<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\Agent;

class AgentTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $agent = new Agent(Agent::PAYMENT_AGENT, '+79998887766', 'агент', '1234567890');
        $this->assertEquals(
            json_encode($agent->asArray()),
            '{"type":"payment_agent","supplier_info":{'.
                '"phones":["+79998887766"],'.
                '"name":"\u0430\u0433\u0435\u043d\u0442",'.
                '"inn":"1234567890"'.
            '}}'
        );
    }

    public function testSupplier()
    {
        $agent = new Agent(Agent::PAYMENT_AGENT);
        $agent->setSupplierInfo('агент', ['+79998887766'], '1234567890');

        $data = $agent->asArray();
        $this->assertEquals(
            json_encode($agent->asArray()),
            '{"type":"payment_agent","supplier_info":{'.
                '"phones":["+79998887766"],'.
                '"name":"\u0430\u0433\u0435\u043d\u0442",'.
                '"inn":"1234567890"'.
            '}}'
        );
    }

    public function testPayingAgentInfo()
    {
        $agent = new Agent(Agent::PAYMENT_AGENT);
        $agent->setPayingAgentInfo('оплата', ['+79998887766']);
        $this->assertEquals(
            json_encode($agent->asArray()),
            '{"type":"payment_agent","paying_agent":{'.
                '"operation":"\u043e\u043f\u043b\u0430\u0442\u0430",'.
                '"phones":["+79998887766"]'.
            '}}'
        );
    }

    public function testReceivePaymentsOperatorInfo()
    {
        $agent = new Agent(Agent::PAYMENT_AGENT);
        $agent->setReceivePaymentsOperatorInfo(['+79998887766']);
        $this->assertEquals(
            json_encode($agent->asArray()),
            '{"type":"payment_agent","receive_payments_operator":{'.
                '"phones":["+79998887766"]'.
            '}}'
        );
    }

    public function testMoneyTransferOperatorInfo()
    {
        $agent = new Agent(Agent::PAYMENT_AGENT);
        $agent->setMoneyTransferOperatorInfo('агент', ['+79998887766'], 'ул.Пушкина', '1234567890');

        $data = $agent->asArray();
        $this->assertEquals(
            json_encode($agent->asArray()),
            '{"type":"payment_agent","money_transfer_operator":{'.
                '"name":"\u0430\u0433\u0435\u043d\u0442",'.
                '"phones":["+79998887766"],'.
                '"address":"\u0443\u043b.\u041f\u0443\u0448\u043a\u0438\u043d\u0430",'.
                '"inn":"1234567890"'.
            '}}'
        );
    }
}
