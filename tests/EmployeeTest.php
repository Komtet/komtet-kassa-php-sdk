<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk;

use Komtet\KassaSdk\Employee;
use Komtet\KassaSdk\EmployeeType;

class EmployeeTest extends \PHPUnit_Framework_TestCase
{
    public function testEmployee()
    {
        $employee = new Employee(
            EmployeeType::CASHIER,
            'Full Name',
            'test_login',
            'test_password',
            'POS_KEY'
        );

        $this->assertEquals($employee->asArray()['type'], EmployeeType::CASHIER);
        $this->assertEquals($employee->asArray()['name'], 'Full Name');
        $this->assertEquals($employee->asArray()['login'], 'test_login');
        $this->assertEquals($employee->asArray()['password'], 'test_password');
        $this->assertEquals($employee->asArray()['pos_id'], 'POS_KEY');

        $employee->setPaymentAddress('payment address');
        $this->assertEquals($employee->asArray()['payment_address'], 'payment address');

        $employee->setAccessSettings(true, true, true);
        $this->assertEquals($employee->asArray()['is_manager'], true);
        $this->assertEquals($employee->asArray()['is_can_assign_order'], true);
        $this->assertEquals($employee->asArray()['is_app_fast_basket'], true);
    }
}
