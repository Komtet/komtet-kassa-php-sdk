<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk;

use phpDocumentor\Reflection\Types\Null_;

class Employee
{
    /**
     * @var EmployeeType
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $pos_id;

    /**
     * @var string
     */
    private $inn;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $paymentAddress;
    
    /**
     * @var bool
     */
    private $isManager;

    /**
     * @var bool
     */
    private $isCanAssignOrder;

    /**
     * @var bool
     */
    private $isAppFastBasket;

    /**
     * @param EmployeeType $type Employee type
     * @param string $name Full name 
     * @param string $login Login
     * @param string $password Password
     * @param string $pos_id POS ID in KOMTET Kassa
     * @param string $inn Tax system
     * @param string $phone Phone
     * @param string $email Email
     *
     * @return Employee
     */
    public function __construct($type, $name, $login, $password, $pos_id,
                                $inn = null, $phone = null, $email=null)
    {
        $this->type = $type;
        $this->name = $name;

        $this->login = $login;
        $this->password = $password;

        $this->pos_id = $pos_id;
        
        if ($inn) {
            $this->inn = $inn;
        }

        if ($phone) {
            $this->phone = $phone;
        }
        
        if ($email) {
            $this->email = $email;
        }

    }

    /**
     * @param string $paymentAddress Settlement address
     *
     */
    public function setPaymentAddress($paymentAddress)
    {
        $this->paymentAddress = $paymentAddress;
    }

    /**
     * @param bool $isManager
     * @param bool $isCanAssignOrder
     * @param bool isAppFastBasket
     *
     */
    public function setAccessSettings($isManager=null, $isCanAssignOrder=null, $isAppFastBasket=null)
    {   
        if ($isManager) {
            $this->isManager = $isManager;
        }

        if ($isCanAssignOrder) {
            $this->isCanAssignOrder = $isCanAssignOrder;
        }
        
        if ($isAppFastBasket) {
            $this->isAppFastBasket = $isAppFastBasket;
        }
        
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'type' => $this->type,
            'name' => $this->name,
            'login' => $this->login,
            'password' => $this->password,
            'pos_id' => $this->pos_id
        ];

        if ($this->inn !==null) {
            $result['inn'] = $this->inn;
        }

        if ($this->phone !==null) {
            $result['phone'] = $this->phone;
        }

        if ($this->email !==null) {
            $result['email'] = $this->email;
        }

        if ($this->paymentAddress !==null) {
            $result['payment_address'] = $this->paymentAddress;
        }

        if ($this->isManager !==null) {
            $result['is_manager'] = $this->isManager;
        }

        if ($this->isCanAssignOrder !==null) {
            $result['is_can_assign_order'] = $this->isCanAssignOrder;
        }

        if ($this->isAppFastBasket !==null) {
            $result['is_app_fast_basket'] = $this->isAppFastBasket;
        }

        return $result;
    }
}
