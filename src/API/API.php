<?php
/*
 *  WordPress Contact Form 7 Creamailer extension
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

use \SourOatMilk\CF7CMStandalone\API\APIBase as Base;
use \SourOatMilk\CF7CMStandalone\Utility\UtilityBase as Utility;

class API extends Base {
    /**
     * makeHeaders
     * Creates headers
     *
     * @param string $url
     * @param int $timestamp
     * @param string $json
     * @return array
     */
    public function makeHeaders($url, $timestamp, $json) {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json; charset=UTF-8'
        ];
        $hash = sha1($url . $json . $timestamp . $this->getOption('shared_key'));
        return array_merge($headers, [
            'X-Access-Token' => $this->getOption('key'),
            'X-Request-Signature' => $hash,
            'X-request-Timestamp' => $timestamp,
        ]);
    }

    /**
     * request
     * Create request
     *
     * @param string $method
     * @param string $resource
     * @param array $data
     * @return array
     */
    public function request($method, $resource, $data = []) {
        $res = ltrim($resource, '/');
        $purl = parse_url($res);
        $query = isset($purl['query']) ? "?$purl[query]" : '';
        $url = $this->getOption('url') . "/$purl[path]$query";
        $json = empty($data) ? null : json_encode($data);
        $timestamp = time();
        $headers = $this->makeHeaders($url, $timestamp, $json);
        $timeout = 10;

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
}
