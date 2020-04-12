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
namespace SourOatMilk\CF7CMStandalone\Views;

use \SourOatMilk\CF7CMStandalone\Views\IndexBase as Base;
use \SourOatMilk\CF7CMStandalone\Utility\UtilityBase as Utility;

class Index extends Base {
    public static function form($api_data, $options) {
        $prefix = $options['input'];
        $api_inputs = [
            [
                'label'     => 'Access Token',
                'name'      => 'key',
                'value'     => $api_data['key'],
                'help'      => 'Your access token (asiakastunnus)',
                'required'  => true,
                'condition' => !empty($api_data['key']),
                'prefix'    => $prefix,
            ],
            [
                'label'     => 'Shared Secred',
                'name'      => 'shared_key',
                'value'     => $api_data['shared_key'],
                'help'      => 'Your shared secred (yhteinen tunniste)',
                'required'  => true,
                'type'      => 'password',
                'condition' => !empty($api_data['shared_key']),
                'prefix'    => $prefix,
            ],
        ];

        $api_list_select_options = [
            '&nbsp;' => '',
        ];

        foreach ((array)$api_data['lists'] as $list) {
            $api_list_select_options[$list->name] = $list->id;
        }

        $api_list_select = [
            'label'             => 'List',
            'name'              => 'list_id',
            'required'          => true,
            'condition'         => !empty($api_data['list_id']),
            'select_options'    => $api_list_select_options,
            'selected'          => $api_data['list_id'],
            'help'              => 'Select list for subscribers',
            'prefix'            => $prefix,
        ];

        $select_options = [
            '&nbsp;' => '',
        ];

        foreach (Utility::getCF7TagNames() as $tag) {
            $v = $tag;
            $select_options["[$v]"] = $v;
        }

        $selects = [
            [
                'label'             => 'Email',
                'name'              => 'email',
                'required'          => true,
                'condition'         => !empty($api_data['email']),
                'select_options'    => $select_options,
                'selected'          => $api_data['email'],
                'help'              => 'Email tag',
                'prefix'            => $prefix,
            ],
            [
                'label'             => 'Opt-in',
                'name'              => 'opt-in',
                'required'          => true,
                'condition'         => !empty($api_data['opt-in']),
                'select_options'    => $select_options,
                'selected'          => $api_data['opt-in'],
                'help'              => 'Opt-in tag',
                'prefix'            => $prefix,
            ],
        ];

        $optional_selects = [
            'name' ,
            'company',
            'address',
            'city',
            'zip_code',
            'country',
            'phone',
            'customer_number',
        ];

        foreach ($optional_selects as $item) {
            $label = ucfirst(str_replace('_', ' ', $item));
            $selects[] = [
                'label'             => $label,
                'name'              => $item,
                'select_options'    => $select_options,
                'selected'          => $api_data[$item],
                'help'              => "$label tag",
                'prefix'            => $prefix,
            ];
        }

        $status = $api_data['status'];
        $success = isset($status['success']) && $status['success'];
        $status_text = $success ? 'Connected' : 'No connection';
        $status_class = $success ? 'info' : 'danger';
        $is_hidden = !$success ? 'is-hidden' : '';

        ?>
        <div class="form">
            <fieldset>
                <div class='level'>
                    <div class='level-left'>
                        <div class='level-item'>
                            <h3>API settings</h3>
                        </div>
                    </div>
                    <div class='level-right'>
                        <div class='level-item'>
                            <small class="tag <?php echo 'is-'.$status_class; ?>">
                                <?php echo $status_text; ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php foreach ($api_inputs as $input) : ?>
                    <?php echo Index::inputField($input); ?>
                <?php endforeach; ?>
                <div class="<?php echo $is_hidden; ?>">
                    <?php echo Index::selectField($api_list_select); ?>
                </div>
            </fieldset>

            <hr class="<?php echo $is_hidden; ?>">

            <fieldset class="<?php echo $is_hidden; ?>">
                <h3>Data</h3>
                <p>Available tags:</p>
                <div class="tags">
                    <?php foreach (Utility::getCF7TagNames() as $tag) : ?>
                        <span class="tag is-black">
                            <tt>[<?php echo $tag; ?>]</tt>
                        </span>
                    <?php endforeach; ?>
                </div>

                <div class='level'>
                <?php foreach ($selects as $select) : ?>
                    <div class='level-item'>
                    <?php echo Index::selectField($select); ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </fieldset>
        </div>
        <?php
    }

    public static function render($api_data, $options) {
        ?>
        <div>
            <div class='level extension-header'>
                <div class='level-left'>
                    <div class='level-item'>
                        <img src="<?php echo \CF7CMSTANDALONE_CF7CM_URL . 'assets/images/logo.png'; ?>" />
                    </div>
                </div>
                <div class='level-right'>
                    <div class='level-item'>
                        <sup>Version <?php echo \CF7CMSTANDALONE_CF7CM_VERSION; ?></sup>
                    </div>
                </div>
            </div>
            <hr>
            <?php echo Index::form($api_data, $options); ?>
            <hr>
            <h3>Need help?</h3>
            <p>See the official <a 
                href="https://tuki.creamailer.fi/hc/fi/articles/360022745512"
                target="_blank">help article</a> for more information.</p>
        </div>
        <?php
    }
}
