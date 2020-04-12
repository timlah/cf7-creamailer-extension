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
namespace SourOatMilk\CF7CMStandalone\Utility;

class UtilityBase {
    /**
     * debug
     * Use dumpJSON to print return $arg if development mode is on
     *
     * @param string|array $arg
     * @return string|boolean
     */
    public static function debug($arg) {
        return CF7CMSTANDALONE_CF7EB_DEV ? UtilityBase::dumpJSON($arg) : false;
    }


    /**
     * dumpJSON
     * Pretty prints any string or array
     *
     * @param string|array $arg
     * @return string
     */
    public static function dumpJSON($arg) {
        return '<pre>' . json_encode($arg, JSON_PRETTY_PRINT) . '</pre>';
    }

    /**
     * getCF7Tags
     * Get all CF7 tags
     *
     * @return array
     */
    public static function getCF7Tags() {
        $has_manager = class_exists('WPCF7_FormTagsManager');
        $manager = $has_manager ? '\\WPCF7_FormTagsManager' : '\\WPCF7_ShortcodeManager';
        return $manager::get_instance()->get_scanned_tags();
    }

    /**
     * getCF7TagNames
     * Get filtered tag names from CF7
     *
     * @return array
     */
    public static function getCF7TagNames() {
        $tags = UtilityBase::getCF7Tags();
        return array_filter(array_map(function ($v) {
            return $v['name'];
        }, $tags), function ($v) {
            return $v !== '';
        });
    }

    /**
     * htmlParams
     * Make HTML parameters from associative array
     *
     * @param array $arr Array of parameters
     * @return array
     */
    public static function htmlParams($arr) {
        $result = [];
        foreach ($arr as $k => $v) {
            $result[] = "$k='$v'";
        }
        return $result;
    }
}
