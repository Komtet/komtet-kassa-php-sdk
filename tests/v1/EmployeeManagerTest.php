<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v1;

use Komtet\KassaSdk\v1\Employee;
use Komtet\KassaSdk\v1\EmployeeManager;
use Komtet\KassaSdk\v1\EmployeeType;
use PHPUnit\Framework\TestCase;

class EmployeeManagerTest extends TestCase
{
    private $client;
    private $manager;
    private $employee;


    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Komtet\KassaSdk\v1\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->manager = new EmployeeManager($this->client);

        $this->employee = new Employee(
            EmployeeType::CASHIER,
            'Full Name',
            'test_login',
            'test_password',
            'POS_KEY'
        );

        $this->employee->setPaymentAddress('payment address');
        $this->employee->setAccessSettings(true, true, true);
    }

    public function testGetEmployeesSucceded()
    {
        $result = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($result);
        $this->assertEquals($this->manager->getEmployees(), $result);
    }

    public function testGetEmployeesByTypeSucceded()
    {
        $result = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($result);
        $this->assertEquals($this->manager->getEmployees(EmployeeType::DRIVER), $result);
    }

    public function testGetEmployeeInfoSucceded()
    {
        $result = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($result);
        $this->assertEquals($this->manager->getEmployeeInfo(1), $result);
    }

    public function testCreateEmployeeSucceded()
    {
        $result = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($result);
        $this->assertEquals($this->manager->createEmployee($this->employee), $result);
    }

    public function testUpdateEmployeeSucceded()
    {
        $result = ['key' => 'val'];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($result);
        $this->assertEquals($this->manager->updateEmployee(1, $this->employee), $result);
    }

    public function testDeleteEmployeeSucceded()
    {
        $result = [];
        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($result);
        $this->assertEquals($this->manager->deleteEmployee(1), $result);
    }
}
