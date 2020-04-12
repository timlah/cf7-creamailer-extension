<?php
/*
 *  WordPress plugin for Contact Form 7 extensions
 *  Copyright (C) 2019 SourOatMilk (email: souroatmilk@protonmail.com)

 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace SourOatMilk\CF7CMStandalone\API;

use \SourOatMilk\CF7CMStandalone\Utility\UtilityBase as Utility;

class APIBase {
    /**
     * @var array $options API options
     */
    protected $options = [
        'url' => '',
        'key' => '',
        'required' => ['url', 'key']
    ];

    public function __construct($options) {
        $this->options = array_merge($this->options, $options);
        $this->options['url'] = rtrim($this->options['url'], '/');
    }

    /**
     * get
     * Get request
     *
     * @param string $resource
     * @param array $data
     * @return array
     */
    public function get($resource, $data = []) {
        return $this->beginRequest('GET', $resource, $data);
    }

    /**
     * post
     * Post request
     *
     * @param string $resource
     * @param array $data
     * @return array
     */
    public function post($resource, $data = []) {
        return $this->beginRequest('POST', $resource, $data);
    }

    /**
     * put
     * Put request
     *
     * @param string $resource
     * @param array $data
     * @return array
     */
    public function put($resource, $data = []) {
        return $this->beginRequest('PUT', $resource, $data);
    }

    /**
     * patch
     * Patch request
     *
     * @param string $resource
     * @param array $data
     * @return array
     */
    public function patch($resource, $data = []) {
        return $this->beginRequest('PATCH', $resource, $data);
    }

    /**
     * delete
     * Delete request
     *
     * @param string $resource
     * @return array
     */
    public function delete($resource) {
        return $this->beginRequest('DELETE', $resource);
    }

    /**
     * isSetupCorrect
     * Check if required keys are present
     *
     * @return boolean
     */
    public function isSetupCorrect() {
        return empty(array_filter($this->options['required'], function ($v) {
            return empty($this->options[$v]);
        }));
    }

    /**
     * getOption
     *
     * @return string
     */
    public function getOption($option) {
        return $this->options[$option];
    }

    /**
     * makeHeaders
     * Creates headers for request
     * TODO: As it is now, is tied too much on how Creamailer works, refactor.
     *
     * @param string $url
     * @param date $timestamp
     * @param array $json
     * @return array
     */
    public function makeHeaders($url, $timestamp, $json) {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json; charset=UTF-8'
        ];
        return $headers;
    }

    /**
     * beginRequest
     *
     * @param string $method
     * @param string $resource
     * @param array $data
     * @return array
     */
    public function beginRequest($method, $resource, $data = []) {
        if (!$this->isSetupCorrect()) {
            return [];
        }

        $request = $this->request($method, $resource, $data);

        return $this->execRequest(...$request);
    }

    /**
     * request
     * Send API request
     *
     * @param string $method
     * @param string $resource
     * @param array $data
     * @return array
     */
    public function request($method, $resource, $data = []) {
        $res = ltrim($resource, '/');
        $purl = parse_url($res);
        $params = isset($purl['query']) ? "?$purl[query]" : '';
        $url = $this->getOption('url') . "/$purl[path]$params";
        $json = empty($data) ? null : json_encode($data);

        $args = [
            'url' => $url,
            'method' => $method,
            'headers' => $headers,
            'timeout' => $timeout,
        ];

        if (!empty($data)) {
            $args['body'] = $json;
        }

        return [$url, $args];
    }

    /**
     * execRequest
     * Executes request
     *
     * @param string $url
     * @param array $args
     * @return array
     */
    public function execRequest($url, $args) {
        $response = wp_remote_request($url, $args);
        $result = $this->parseResponse($response);

        return (array)$result;
    }

    /**
     * parseResponse
     * Parse API response
     *
     * @param array $response
     * @return array
     */
    public function parseResponse($response) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        if (!is_null($data)) {
            return $data;
        }

        $code = (int)wp_remote_retrieve_response_code($response);
        $message = wp_remote_retrieve_response_message($response);

        if ($code !== 200) {
            $message = "Failure: <em>$code $message</em>.";
        }

        return ['result' => $message];
    }
}
