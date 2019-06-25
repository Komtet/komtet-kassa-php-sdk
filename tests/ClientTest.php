<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace {
    $curlMonkeyPatchEnabled = false;
    $dataVariable = null;
}

namespace Komtet\KassaSdk {

    function curl_exec($descriptor) {
        global $curlMonkeyPatchEnabled;
        if (isset($curlMonkeyPatchEnabled) && $curlMonkeyPatchEnabled === true) {
            return True;
        }
        else
        {
            return call_user_func_array('\curl_exec', func_get_args());
        }
    }

    function curl_getinfo($descriptor, $key) {
        global $curlMonkeyPatchEnabled;
        if (isset($curlMonkeyPatchEnabled) &&
            $curlMonkeyPatchEnabled === true &&
            $key == CURLINFO_HTTP_CODE) {
                return 200;
        }
        else
        {
            return call_user_func_array('\curl_getinfo', func_get_args());
        }
    }

    function curl_setopt($descriptor, $key, $option) {
        global $curlMonkeyPatchEnabled;
        if (isset($curlMonkeyPatchEnabled) &&
            $curlMonkeyPatchEnabled === true &&
            $key == CURLOPT_POSTFIELDS) {
                global $dataVariable;
                $dataVariable = $option;
            }
        return call_user_func_array('\curl_setopt', func_get_args());
    }

    class ClientTest extends \PHPUnit_Framework_TestCase
    {
        private $client;
        private $check;

        protected function setUp()
        {
            $this->client = new Client('key', 'secret');
            
            $this->check = new Check('id1', 'test@test.test', Check::INTENT_SELL, 1);
            
            $payment = new Payment(Payment::TYPE_CARD, 110.98);
            $position = new Position('position', 110.98, 1, 110.98, 0, new Vat(0));
            
            $this->check->addPayment($payment);
            $this->check->addPosition($position);

            global $curlMonkeyPatchEnabled;
            $curlMonkeyPatchEnabled = true;
        }

        protected function tearDown()
        {
            global $curlMonkeyPatchEnabled;
            $curlMonkeyPatchEnabled = false;
        }

        public function testRestorePrecisions()
        {
            $system_php_serialize_precision = ini_get('serialize_precision');
            $system_php_precision = ini_get('precision');

            $path = 'api/shop/v1/queues/queue-id/task';

            $this->client->sendRequest($path, $this->check->asArray());

            $this->assertEquals($system_php_serialize_precision, ini_get('serialize_precision'));
            $this->assertEquals($system_php_precision, ini_get('precision'));
        }

        public function testJsonEncode()
        {
            $this->client->sendRequest($path, $this->check->asArray());

            global $dataVariable;
            $decodedCheck = json_decode($dataVariable);

            $this->assertEquals($decodedCheck->positions[0]->total,
                                $this->check->getPositions()[0]->getTotal());
        }
    }
}
