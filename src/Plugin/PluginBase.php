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
namespace SourOatMilk\CF7CMStandalone\Plugin;

use \SourOatMilk\CF7CMStandalone\API\APIBase as API;
use \SourOatMilk\CF7CMStandalone\Views\IndexBase as Index;
use \SourOatMilk\CF7CMStandalone\Utility\UtilityBase as Utility;

/**
 * TODO: Make clear separation between admin and frontend.
 */
class PluginBase {
    /**
     * @var class $api Instance of API
     */
    public $api;

    /**
     * @var array $options Plugin options
     */
    private $api_options = [
        'url' => '',
        'version' => '',
        'key' => '',
        'shared_key' => '',
    ];

    /**
     * @var array $inputs
     */
    public $data_options = [
        'email' => '',
        'opt-in' => '',
    ];

    /**
     * @var array $options Plugin options
     */
    public $options = [
        'id' => 'extension-base',
        'tab_title' => 'Extension',
        'short_name' => 'eb',
        'input' => '',
        'option' => '',
        'cf7' => ''
    ];

    /**
     * @var array $required_data Data that must be set
     */
    public $required_data = [];

    public function __construct($options, $api_options, $required = []) {
        $opts = array_merge($this->options, $options);
        $this->api_options = array_merge($this->api_options, $api_options);
        $this->required_data = array_merge($this->required_data, $required);
        $sn = $opts['short_name'];
        $o = array_merge($opts, [
            'input' => "cf7-$sn",
            'option' => 'cf7_' . $sn . '_',
            'cf7' => "cf7$sn",
        ]);
        $this->options = $o;
        $this->initHooks();
    }

    /**
     * initHooks
     * Initialize hooks
     *
     * @return boolean
     */
    public function initHooks() {
        $prefix = 'wpcf7_';
        $add = [
            'filter' => [
                $prefix . 'editor_panels' => 'attach'
            ],
            'action' => [
                $prefix . 'after_save' => 'save',
                $prefix . 'before_send_mail' => 'send',
                'admin_init' => 'initAdmin',
            ]
        ];

        foreach ($add as $z => $arr) {
            foreach ($arr as $x => $y) {
                $fn = "\\add_$z";
                $fn($x, [$this, $y]);
            }
        }

        return true;
    }

    /**
     * initAdmin
     * Initialize admin
     *
     * @return boolean
     */
    public function initAdmin() {
        \wp_register_style('cf7eb-style', \CF7CMSTANDALONE_CF7EB_URL . 'assets/main.css');
        \wp_enqueue_style('cf7eb-style');

        return true;
    }

    /**
     * attach
     * Attach extension to CF7
     *
     * @return array
     */
    public function attach($panels) {
        $opts = $this->options;
        $new_page = [
            "$opts[id]" => [
                'title' => __($opts['tab_title'], 'contact-form-7'),
                'callback' => function ($args) {
                    $this->activate($args);
                }
            ]
        ];
        return array_merge($panels, $new_page);
    }

    /**
     * activate
     * Activates extension and renders settings page
     *
     * @param array $args CF7 arguments passed
     * @return html
     */
    public function activate($args) {
        $this->setup($args);
        if (CF7CMSTANDALONE_CF7EB_DEV) {
            echo '<p>
                <span class="tag is-warning">
                    WARNING: Dev mode is on, debug messages will be shown!
                </span>
            </p>';
        }
        return $this->display();
    }

    /**
     * newAPI
     * Creates new API instance. This is silly, but required for
     * submission to actually work.
     *
     * @return boolean
     */
    public function newAPI($options) {
        $this->api = new API($options);
        return true;
    }

    /**
     * setup
     * Perfect description goes here
     *
     * @param array $args CF7 argument array
     * @return boolean
     */
    public function setup($args) {
        $options = $this->getOptions($args);
        $this->newAPI($options['api_options']);

        return true;
    }

    /**
     * display
     * Renders the settings view
     *
     * @return string
     */
    public function display() {
        return Index::render($this->getAdminOptions(), $this->options);
    }

    /**
     * save
     * Save settings
     */
    public function save($args) {
        $opt = $this->options['option'];
        $inp = $this->options['input'];
        if (!empty($_POST)) {
            \update_option($opt . $args->id(), $_POST[$inp]);
        }
    }

    /**
     * send
     * Send data to API
     *
     * @return array
     */
    public function send($args) {
        echo Utility::debug([$this->options['id'] => 'send']);
        $options = \get_option($this->options['option'] . $args->id());
        $this->data_options = array_merge($this->data_options, $options);

        if (!$this->areRequiredSet()) {
            echo Utility::debug('REQUIREMENTS ARE NOT MET');
            return [];
        }

        $response = [];
        $data = $this->prepareRequest();

        if (empty($data)) {
            echo Utility::debug($data);
            return $response;
        }

        $response = $this->sendRequest($args, $data);

        return $response;
    }

    /**
     * requiredAreSet
     */
    public function areRequiredSet() {
        return empty(array_filter($this->required_data, function ($v) {
            return empty($this->data_options[$v]);
        }));
    }

    /**
     * prepareRequest
     * Preparation for request. This does nothing by itself, extension must
     * implement this for its needs.
     *
     * return array
     */
    public function prepareRequest() {
        /**
         * Example:
         *
         * $posted_data = $this->getPostedData();
         * $data_for_api = $this->data_options['data'];
         * $data = [
         *     'data_for_api' => $this->getPostedOption($data_for_api, $posted_data);
         * ];
         * return $data;
         */
        return [];
    }

    /**
     * sendRequest
     * Send API request to save input data. This does nothing by itself,
     * it has to be implemented by extension.
     *
     * @param array $data Data for API
     * @return array
     */
    public function sendRequest($args, $data) {
        $options = $this->getOptions($args);
        $this->newAPI($options['api_options']);

        /**
         * Example:
         * return $this->api->post('foobar', $data);
         */

        return $data;
    }

    /**
     * setOption
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function setOption($key, $value) {
        foreach (['api_options', 'data_options'] as $opt) {
            if (isset($this->$opt[$key])) {
                $this->$opt[$key] = $value;
            }
        }

        return true;
    }

    /**
     * getOptions
     *
     * @param array $args
     * @return array
     */
    public function getOptions($args = []) {
        if (!empty($args)) {
            $api_data = array_merge($this->api_options, $this->data_options);
            $options = \get_option($this->options['option'] . $args->id(), $api_data);
            foreach ($options as $key => $value) {
                $this->setOption($key, $value);
            }
        }

        return [
            'api_options' => $this->api_options,
            'data_options' => $this->data_options
        ];
    }

    /**
     * getPostedData
     *
     * @return array
     */
    public function getPostedData() {
        $submission = \WPCF7_Submission::get_instance();
        return $submission->get_posted_data();
    }

    /**
     * getPostedOption
     * Get value of posted option
     *
     * @param string $option Name of option
     * @param array $posted Posted data
     * @return array|string
     */
    public function getPostedOption($option, $posted) {
        $has_option = isset($posted[$option]);
        return $has_option ? $posted[$option] : [];
    }

    /**
     * getAdminOptions
     * Get API and data options for admin page
     *
     * @return array
     */
    public function getAdminOptions() {
        return array_merge($this->api_options, $this->data_options);
    }
}
