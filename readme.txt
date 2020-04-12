=== Contact Form 7 Creamailer Extension ===
Contributors: souroatmilk
Tags: contact form 7, extension, creamailer, newsletter
Requires at least: 5.0
Tested up to: 5.0.3
Stable tag: 0.1.2
Requires PHP: 7.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Integrates Creamailer for Contact Form 7.

== Description ==

[Creamailer](https://www.creamailer.fi/) extension for [Contact Form 7](https://wordpress.org/plugins/contact-form-7/). This plugin adds new subscriber to Creamailer list on form submit if they have opted in for newsletter subscription.

== Installation ==

0. Unzip files to the `/wp-content/plugins/cf7cm` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
2. In Contact Form 7, click on `Creamailer` tab and fill required fields.

== Frequently Asked Questions ==

= All fields are filled but subscriber was not added =

Subscriber will only be added if all required fields are filled and user has
opted in. Opt-in should be a checkbox.

== Screenshots ==

0. screenshot-1.png

== Changelog ==

= 0.1.2 =
Updated logo.
Added connection test.
Added more fields.
Added automatic list fetching.
Added help section.
Added Finnish help texts for API inputs.
Added basic validation.
Renamed API input labels.
Changed Shared Secred to password type.
Data section will be if there is no API connection.
Uninstalling will remove all plugin data.

= 0.1.1 =
Bug fixing, opt-in was leaking.

= 0.1 =
Initial relase.
