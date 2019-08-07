<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komtet\KassaSdk;

use Komtet\KassaSdk\Exception\ClientException;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

class Client
{
    /**
     * @var string
     */
    private $host = 'https://kassa.komtet.ru';

    /**
     * @var string
     */
    private $partner = null;

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
     * @var array A list of headers to be masked in logs
     */
    private $maskedHeaders = ['Authorization', 'X-HMAC-Signature'];

    /**
     * @param string $key Shop ID
     * @param string $secret Secret key
     * @param LoggerInterface $logger PSR Logger
     *
     * @return Client
     */
    public function __construct($key, $secret, LoggerInterface $logger = null)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->logger = $logger;
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
     * @param string $value
     *
     * @return Client
     */
    public function setPartner($value)
    {
        $this->partner = $value;

        return $this;
    }

    /**
     * @param string $path
     * @param mixed $data
     *
     * @return mixed
     */
    public function sendRequest($path, $data = null, $method = null)
    {
        if (is_array($data)) {
            $system_php_serialize_precision = ini_get('serialize_precision');
            $system_php_precision = ini_get('precision');
            ini_set('precision', 17);
            ini_set('serialize_precision', -1);

            $data = json_encode($data);

            if ($system_php_precision != False) {
                ini_set('precision', $system_php_precision);
            }

            if ($system_php_serialize_precision != False) {
                ini_set('serialize_precision', $system_php_serialize_precision);
            }

        } elseif ($data) {
            throw new InvalidArgumentException('Unexpected type of $data, excepts array or null');
        }

        if (!$method) {
            $method = $data !== null ? 'POST' : 'GET';
        }

        if (class_exists('Psr\Log\LogLevel')) {
            $log_level_debug = LogLevel::DEBUG;
            $log_level_warning = LogLevel::WARNING;
        }
        else {
            $log_level_debug = 'debug';
            $log_level_warning = 'warning';
        }

        $url = sprintf('%s/%s', $this->host, $path);
        $signature = hash_hmac('md5', $method . $url . ($data ? $data : ''), $this->secret);

        $headers = [
            'Accept: application/json',
            sprintf('Authorization: %s', $this->key),
            sprintf('X-HMAC-Signature: %s', $signature)
        ];
        if (!empty($this->partner)) {
            $headers[] = sprintf('X-Partner-ID: %s', $this->partner);
        }
        if (in_array($method, array('POST', 'PUT'))) {
            $headers[] = 'Content-Type: application/json';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (in_array($method, array('POST', 'PUT'))) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($ch);

        $this->log($log_level_debug, 'request: url={url} headers={headers} data={data}', [
            'url' => $url,
            'headers' => $this->maskHeaders($headers),
            'data' => $data
        ]);

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
            $this->log($log_level_warning, 'error: {error} {response}', [
                'error' => $error,
                'response' => $response
            ]);
            throw new ClientException($error, self::getClientExceptionCodeByResponse($response));
        }

        $this->log($log_level_debug, 'response: {response}', ['response' => $response]);

        return json_decode($response, true);
    }

    private function log($level, $message, $context)
    {
        if ($this->logger !== null) {
            $message = sprintf('KOMTET Kassa %s', $message);
            $this->logger->log($level, $message, $context);
        }
    }

    private function maskHeaders($headers)
    {
        return array_map(
            function($header) {
                $parts = explode(':', $header);
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                if (in_array($key, $this->maskedHeaders)) {
                    $value =  str_repeat('*', strlen($value) - 2) . substr($value, -2);
                }
                return [$key, $value];
            },
            $headers
        );
    }

    private static function getClientExceptionCodeByResponse($response) {
        if (preg_match('~external_id: Чек с внешним идентификатором [0-9]+ уже существует~uim', $response)) {
            return ClientException::EXTERNAL_ID_EXISTS;
        }
        return 0;
    }
}
