<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\CourierManager;

class CourierManagerTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $cm;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Komtet\KassaSdk\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cm = new CourierManager($this->client);
    }

    public function testGetCouriersSucceded()
    {
        $path = 'api/shop/v1/couriers?start=0&limit=10';
        $rep = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($path)
            ->willReturn(['key' => 'val']);
        $this->assertEquals($this->cm->getCouriers(), $rep);
    }
}
