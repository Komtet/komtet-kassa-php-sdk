<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\Employee;
use Komtet\KassaSdk\v2\EmployeeType;
use PHPUnit\Framework\TestCase;

class EmployeeTest extends TestCase
{
    public function testEmployee()
    {
        $employee = new Employee(
            EmployeeType::COURIER,
            'Full Name1',
            'test_login2',
            'test_password',
            'POS_KEY'
        );

        $this->assertEquals($employee->asArray()['type'], EmployeeType::COURIER);
        $this->assertEquals($employee->asArray()['name'], 'Full Name1');
        $this->assertEquals($employee->asArray()['login'], 'test_login2');
        $this->assertEquals($employee->asArray()['password'], 'test_password');
        $this->assertEquals($employee->asArray()['pos_id'], 'POS_KEY');

        $employee->setPaymentAddress('payment address');
        $this->assertEquals($employee->asArray()['payment_address'], 'payment address');

        $employee->setAccessSettings(true, false, true);
        $this->assertEquals($employee->asArray()['is_manager'], true);
        $this->assertEquals($employee->asArray()['is_can_assign_order'], false);
        $this->assertEquals($employee->asArray()['is_app_fast_basket'], true);
    }
}
