<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\TaskManager;

class TaskManagerTest extends \PHPUnit\Framework\TestCase
{
    private $client;
    private $tm;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Komtet\KassaSdk\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->tm = new TaskManager($this->client);
    }

    public function testGetTaskInfoSucceded()
    {
        $path = 'api/shop/v1/tasks/task-id';
        $rep = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($path)
            ->willReturn(['key' => 'val']);
        $this->assertEquals($this->tm->getTaskInfo('task-id'), $rep);
    }
}
