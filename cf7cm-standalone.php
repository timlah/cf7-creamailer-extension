<?php
/*
 * Plugin name: Contact From 7 Creamailer Extension
 * Description: A Contact Form 7 extension for Creamailer.
 * Author: SourOatMilk
 * Author URI: https://souroatmilk.xyz
 * Text Domain: contact-form-7
 * Version: 0.1.2
 */
/*
 *  WordPress Contact Form 7 Creamailer extension
 *  Copyright (C) 2019 SourOatMilk (email: souroatmilk@protonmail.com)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

define('CF7CMSTANDALONE_CF7EB_URL', \plugin_dir_url(__FILE__));
define('CF7CMSTANDALONE_CF7EB_VERSION', '0.1.2');
define('CF7CMSTANDALONE_CF7EB_DEV', getenv('DEV'));
define('CF7CMSTANDALONE_CF7CM_VERSION', '0.1.2');
define('CF7CMSTANDALONE_CF7CM_BASE_VERSION', '0.1.1');
define('CF7CMSTANDALONE_CF7CM_URL', \plugin_dir_url(__FILE__));

new SourOatMilk\CF7CMStandalone\Plugin\Plugin();