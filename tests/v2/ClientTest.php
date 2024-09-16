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

namespace Komtet\KassaSdk\v2 {

    use Komtet\KassaSdk\Exception\ApiValidationException;

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


use PHPUnit\Framework\TestCase;

    class ClientTest extends TestCase
    {
        private $client;
        private $check;
        private $buyer;
        private $company;

        protected function setUp(): void
        {
            $this->client = new Client('key', 'secret');

            $clientEmail = 'test@test.ru';
            $payment_address = 'Офис 3';
            $this->buyer = new Buyer();
            $this->buyer->setEmail($clientEmail);
            $this->company = new Company(TaxSystem::COMMON, $payment_address);
            $this->check = new Check('id1', Check::INTENT_SELL, $this->buyer, $this->company);
    

            $payment = new Payment(Payment::TYPE_CARD, 110.98);

            $vat = new Vat(Vat::RATE_20);
            $measure = Measure::MILLILITER;
            $payment_method = PaymentMethod::PRE_PAYMENT_FULL;
            $payment_object = PaymentObject::PROPERTY_RIGHT;
            $position = new Position('name', 110.98, 1, 110.98, $vat, $measure, $payment_method, $payment_object);

            $this->check->addPayment($payment);
            $this->check->addPosition($position);

            global $curlMonkeyPatchEnabled;
            $curlMonkeyPatchEnabled = true;
        }

        protected function tearDown(): void
        {
            global $curlMonkeyPatchEnabled;
            $curlMonkeyPatchEnabled = false;
        }

        public function testRestorePrecisions()
        {
            $system_php_serialize_precision = ini_get('serialize_precision');
            $system_php_precision = ini_get('precision');

            $path = 'api/shop/v2/queues/queue-id/task';

            $this->client->sendRequest($path, $this->check->asArray());

            $this->assertEquals($system_php_serialize_precision, ini_get('serialize_precision'));
            $this->assertEquals($system_php_precision, ini_get('precision'));
        }

        public function testJsonEncode()
        {
            $path = 'api/shop/v2/queues/queue-id/task';
            $this->client->sendRequest($path, $this->check->asArray());

            global $dataVariable;
            $decodedCheck = json_decode($dataVariable);

            $this->assertEquals($decodedCheck->positions[0]->total,
                                $this->check->getPositions()[0]->getTotal());
        }

        public function testAPIValidationException()
        {
            $title = 'Чек с внешнем идентификатором test123 существует';
            $code = 'VLD11';
            $description = 'Проверьте корректность передаваемых идентификаторов. Измените идентификатор, либо прекратите попытки по передаче данного чека в очередь.';
            $status = 422;

            try {
                throw new ApiValidationException($title, $code, $description, $status);
            } catch (ApiValidationException $e) {
                $this->assertEquals($e->getMessage(), $title);
                $this->assertEquals($e->getVLDCode(), $code);
                $this->assertEquals($e->getDescription(), $description);
            }
        }
    }
}
