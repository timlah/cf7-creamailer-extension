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
namespace SourOatMilk\CF7CMStandalone\Views;

use \SourOatMilk\CF7CMStandalone\Utility\UtilityBase as Utility;

class IndexBase {
    /**
     * field
     * Field item HTML
     *
     * @param array $_options Options
     * @return string
     */
    public static function field($_options) {
        $options = array_merge([
            'for' => 'default',
            'help' => '',
            'input' => '',
            'label' => 'Default',
            'required' => false,
            'condition' => false,
            'addons' => ''
        ], $_options);
        $required = $options['required'];
        $condition = $options['condition'];

        $is_required = $options['required'] ? 'is-required' : '';
        $is_danger = $options['required'] && !$options['condition'] ? 'is-danger' : '';
        $has_addons = !empty($options['addons']);
        $addons = $has_addons ? "<div class='control'>$options[addons]</div>" : '';
        $addons_class = $has_addons ? 'has-addons has-addons-right' : '';
        return '<div class="field '. join(' ', [$is_required, $is_danger, $addons_class]) . '">
                <label class="label" for=' . $options['for'] . '>' . $options['label'] .  '</label>
                <div class="control">' . $options['input'] . '</div>
                ' . $addons . '
                <p class="help">' . $options['help'] . '</p>
            </div>';
    }

    /**
     * inputField
     * Creates an input element with label wrapped in 'field' div
     *
     * @param array $_options Options
     * @return string
     */
    public static function inputField($_options) {
        $options = array_merge([
            'help' => '',
            'label' => 'label',
            'name' => 'default',
            'prefix' => 'default',
            'required' => false,
            'condition' => false,
            'type' => 'text',
            'value' => 'default',
            'addons' => '',
        ], $_options);
        $params = [
            'type' => $options['type'],
            'id' => "$options[prefix]-$options[name]",
            'name' => $options['prefix'] . "[$options[name]]",
            'value' => $options['value'],
        ];
        $params_str = implode(' ', Utility::htmlParams($params));
        $input = "<input class='input' $params_str />";

        $out = [
            'for' => $params['id'],
            'help' => $options['help'],
            'input' => $input,
            'label' => $options['label'],
            'prefix' => $options['prefix'],
            'required' => $options['required'],
            'condition' => $options['condition'],
            'addons' => $options['addons'],
        ];

        return IndexBase::field($out);
    }

    /**
     * selectField
     * Creates a select elemeent with options
     *
     * @param array $_options Options
     * @return string
     */
    public static function selectField($_options) {
        $options = array_merge([
            'help' => '',
            'label' => 'label',
            'name' => 'default',
            'prefix' => 'default',
            'required' => false,
            'condition' => false,
            'selected' => '',
            'select_options' => [],
        ], $_options);
        $params = [
            'id' => "$options[prefix]-$options[name]",
            'name' => $options['prefix'] . "[$options[name]]",
        ];
        $params_str = implode(' ', Utility::htmlParams($params));

        $options_html = '';
        foreach ($options['select_options'] as $key => $value) {
            $is_selected = $value == $options['selected'] ? 'selected' : '';
            $options_html .= "<option $is_selected value='$value'>$key</option>";
        }

        $input = '<div class="select">
                <select ' . $params_str . '>' . $options_html . '</select>
            </div>';

        $out = [
            'for' => $params['id'],
            'help' => $options['help'],
            'input' => $input,
            'label' => $options['label'],
            'prefix' => $options['prefix'],
            'required' => $options['required'],
            'condition' => $options['condition'],
        ];
        return IndexBase::field($out);
    }

    /**
     * form
     * Create form element
     *
     * @param array $options Options from plugin
     * @return html
     */
    public static function form($api_data, $options) {
        $prefix = $options['input'];
        $api_inputs = [
            [
                'help' => 'Example: <code>https://www.example.com/api/</code>',
                'label' => 'API URL',
                'name' => 'url',
                'prefix' => $prefix,
                'required' => true,
                'condition' => !empty($api_data['url']),
                'value' => $api_data['url'],
            ],
            [
                'help' => 'Your API Key',
                'label' => 'API Key',
                'name' => 'key',
                'prefix' => $prefix,
                'required' => true,
                'condition' => !empty($api_data['key']),
                'value' => $api_data['key'],
            ],
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
                'help'              => 'Email tag',
                'label'             => 'Email',
                'name'              => 'email',
                'prefix'            => $prefix,
                'required'          => true,
                'condition'         => !empty($api_data['email']),
                'select_options'    => $select_options,
                'selected'          => $api_data['email'],
            ],
            [
                'help'              => 'Opt-in tag',
                'label'             => 'Opt-in',
                'name'              => 'opt-in',
                'prefix'            => $prefix,
                'required'          => true,
                'condition'         => !empty($api_data['opt-in']),
                'select_options'    => $select_options,
                'selected'          => $api_data['opt-in'],
            ],
        ];
        ?>
            <div class="form">
                <fieldset>
                    <h3>API settings</h3>
                    <?php foreach ($api_inputs as $input) : ?>
                        <?php echo IndexBase::inputField($input); ?>
                    <?php endforeach; ?>
                </fieldset>

                <hr>

                <fieldset>
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
                        <?php echo IndexBase::selectField($select); ?>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </fieldset>
            </div>
        <?php
    }

    /**
     * render
     * Renders view
     *
     * @param array $options Options from plugin
     * @return html
     */
    public static function render($api_data, $options) {
        ?>
        <div>
            <div class='level extension-header'>
                <div class='level-left'>
                    <div class='level-item'>
                        <h2>CF7EB</h2>
                    </div>
                    <div class='level-item'>
                        <small>This is an example, create your own!</small>
                    </div>
                </div>
                <div class='level-right'>
                    <div class='level-item'>
                        <sup>Version <?php echo CF7CMSTANDALONE_CF7EB_VERSION; ?></sup>
                    </div>
                </div>
            </div>
            <hr>
            <?php echo IndexBase::form($api_data, $options); ?>
        </div>
        <?php
    }
}
