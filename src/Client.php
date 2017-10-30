<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk;

use Komtet\KassaSdk\Exception\ClientException;
use Psr\Log\LoggerInterface;

class Client
{
    
    const LOG_LEVEL = 0;
    
    /**
     * @var string
     */
    private $host = 'https://kassa.komtet.ru';

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;
    
    /** 
     * @var LoggerInterface 
     */
    private $logger;

    /**
     * @param string $key Shop ID
     * @param string $secret Secret key
     *
     * @return Client
     */
    public function __construct($key, $secret, LoggerInterface $logger = null)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @param string $value
     *
     * @return Client
     */
    public function setHost($value)
    {
        $this->host = $value;

        return $this;
    }

    /**
     * @param string $path
     * @param mixed $data
     *
     * @return mixed
     */
    public function sendRequest($path, $data = null)
    {
        if ($data === null) {
            $method = 'GET';
        } elseif (is_array($data)) {
            $method = 'POST';
            $data = json_encode($data);
        } else {
            throw new InvalidArgumentException('Unexpected type of $data, excepts array or null');
        }

        $url = sprintf('%s/%s', $this->host, $path);
        $signature = hash_hmac('md5', $method . $url . ($data ? $data : ''), $this->secret);

        $headers = [
            'Accept: application/json',
            sprintf('Authorization: %s', $this->key),
            sprintf('X-HMAC-Signature: %s', $signature)
        ];
        if ($method == 'POST') {
            $headers[] = 'Content-Type: application/json';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($ch);
        
        if($this->logger){
            $this->logger->log(self::LOG_LEVEL, 'Requested url '.$url.' params '. print_r($data, true).' headers '.print_r($headers, true));
            $this->logger->log(self::LOG_LEVEL, 'Response '.$response);
        }
        
        $error = null;
        if ($response === false) {
            $error = curl_error($ch);
        } else {
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($status !== 200) {
                $error = sprintf('Unexpected status (%s)', $status);
            }
        }
        curl_close($ch);
        if ($error !== null) {
            throw new ClientException($error);
        }
        return json_decode($response, true);
    }
}
