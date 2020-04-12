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
namespace SourOatMilk\CF7CMStandalone\Plugin;

use \SourOatMilk\CF7CMStandalone\API\API;
use \SourOatMilk\CF7CMStandalone\Views\Index;

use \SourOatMilk\CF7CMStandalone\Plugin\PluginBase as Base;
use \SourOatMilk\CF7CMStandalone\Utility\UtilityBase as Utility;

class Plugin extends Base {
    public $data_options = [
        'opt-in' => '',
        'email' => '',
        'name' => '',
        'company' => '',
        'address' => '',
        'city' => '',
        'zip_code' => '',
        'country' => '',
        'phone' => '',
        'customer_number' => ''
    ];

    public function __construct() {
        $options = [
            'id' => 'creamailer-extension',
            'tab_title' => 'Creamailer',
            'short_name' => 'cm',
        ];
        $api_options = [
            'url' => 'https://api.cmfile.net/v1/api',
            'key' => '',
            'shared_key' => '',
            'list_id' => '',
            'lists' => [],
            'status' => [],
            'required' => [
                'key',
                'shared_key',
            ]
        ];
        $required_data = [
            'email',
            'opt-in'
        ];
        parent::__construct($options, $api_options, $required_data);
    }

    /**
     * send
     * Get parent::send return value and print it out
     *
     * @return string
     */
    public function send($args) {
        $response = parent::send($args);
        echo Utility::debug($response);
    }

    /**
     * prepareRequest
     * Prepares the request
     *
     * @return array
     */
    public function prepareRequest() {
        $posted_data = $this->getPostedData();
        $opt_in = $this->data_options['opt-in'];
        $list_id = $this->data_options['list_id'];
        $data_items = array_filter($this->data_options, function ($k) {
            return !in_array($k, ['opt-in', 'key', 'shared_key', 'list_id']);
        }, ARRAY_FILTER_USE_KEY);
        $is_opt_in = isset($opt_in) && strlen($opt_in) > 0;
        $posted_opt_in = $this->getPostedOption($opt_in, $posted_data);
        $has_opt_in = !empty($posted_opt_in[0]);
        $data = [];

        echo Utility::debug($posted_data);
        echo Utility::debug($posted_opt_in);

        /**
         * Opt-in is always required, no exceptions! Also list ID must exist.
         */
        if (!$is_opt_in || !$has_opt_in || empty($list_id)) {
            echo Utility::debug([
                'list_id' => $list_id,
                'is_opt_in' => $is_opt_in,
                'has_opt_in' => $has_opt_in
            ]);
            return $data;
        }

        foreach ($data_items as $k => $v) {
            $value = $this->getPostedOption($v, $posted_data);
            if (!empty($value)) {
                $data[$k] = $value;
            }
        }

        echo Utility::debug($data);

        return $data;
    }

    /**
     * sendRequest
     * Sends the request
     *
     * @param array $args
     * @param array $data
     * @return array
     */
    public function sendRequest($args, $data) {
        $options = $this->getOptions($args);
        $this->newAPI($options['api_options']);
        $query = [];
        $list_id = $this->api->getOption('list_id');

        // echo Utility::debug($this->api->get("lists/subscribers/$list_id.json?status=active"));

        $exists = $this->api->get("subscribers/$list_id.json?email=$data[email]");
        $has_error = isset($exists['error']);
        if ($has_error && $exists['message'] == 'Subscriber not found') {
            echo Utility::debug($exists);
        }
        $method = !$has_error ? 'put' : 'post';
        return $this->api->$method("subscribers/$list_id", $data);
    }

    /**
     * display
     * Displays settings
     *
     * @return string
     */
    public function display() {
        $admin_options = $this->getAdminOptions();
        $status = $this->api->get('connection_test');
        $admin_options['status'] = $status;
        echo Utility::debug($status);
        echo Utility::debug($admin_options);
        if (isset($status['success']) && $status['success']) {
            $admin_options['lists'] = $this->getLists();
        }
        return Index::render($admin_options, $this->options);
    }

    /**
     * getLists
     * Get Creamailer lists
     *
     * @return array
     */
    public function getLists() {
        $res = $this->api->get('lists');
        $has_results = isset($res['Results']) && !empty($res['Results']);
        $lists = $has_results ? $res['Results'] : [];
        return $lists;
    }

    /**
     * newAPI
     * Create new API instance
     *
     * @param array $options
     * @return bool
     */
    public function newAPI($options = null) {
        $options = $options ? $options : $this->getOptions()['api_options'];
        $this->api = new API($options);
        return true;
    }
}
