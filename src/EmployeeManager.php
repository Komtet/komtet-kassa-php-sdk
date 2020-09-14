<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk;

class EmployeeManager
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }


    /**
     * Feed back the information about employees
     *
     * @param EmployeeType $type type of employees
     * @param string $start Launch the employees input from "start"
     * @param string $limit Bound the employees input to the "limit" elements
     *
     * @return mixed
     */
    public function getEmployees($start = '0', $limit = '10', $type = null)
    {
        $path = sprintf('api/shop/v1/employees?start=%s&limit=%s', $start, $limit);
        if ($type) {
            $path .= sprintf('&type=%s', $type);
        }
        return $this->client->sendRequest($path);
    }

    /**
     * Employee creation
     *
     * @param Employee $employee Employee
     *
     * @return mixed
     */
    public function createEmployee($employee)
    {
        $path = sprintf('api/shop/v1/employees');
        return $this->client->sendRequest($path, $employee->asArray());
    }


    /**
     * Employee update
     *
     * @param int $oid Employee ID
     * @param Employee $employee Employee
     *
     * @return mixed
     */
    public function updateEmployee($eid, $employee)
    {
        $path = sprintf('api/shop/v1/employees/%s', $eid);
        return $this->client->sendRequest($path, $employee->asArray(), 'PUT');
    }


    /**
     * Viewing employee information
     *
     * @param int $eid Employee ID
     *
     * @return mixed
     */
    public function getEmployeeInfo($eid)
    {
        $path = sprintf('api/shop/v1/employees/%s', $eid);
        return $this->client->sendRequest($path);
    }


    /**
     * Delete employee
     *
     * @param int $eid Employee ID
     *
     * @return mixed
     */
    public function deleteEmployee($eid)
    {
        $path = sprintf('api/shop/v1/employees/%s', $eid);
        return $this->client->sendRequest($path, null, 'DELETE');
    }
}
