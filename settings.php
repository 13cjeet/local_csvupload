<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * settings page of plugin
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    global $ADMIN;

    $settings = new admin_settingpage(
        'local_csvupload_settings',
        get_string('csvupload_form', 'local_csvupload'),
        'moodle/site:config'
    );


    $settings->add(new admin_setting_configcheckbox(
        'csvupload_send_random_email',
        get_string('csvupload_send_random_email', 'local_csvupload'),
        get_string('csvupload_send_random_email_desc', 'local_csvupload'),
        0
    ));

    $ADMIN->add('localplugins', $settings);

}


$capabilities = [
    'local/csvupload:upload',
];

$context = context_system::instance();
$hasaccess = has_all_capabilities($capabilities, $context);

// Add this admin page only if the user has all of the required capabilities.
if ($hasaccess) {
    $ADMIN->add('accounts', new admin_externalpage('local_csvupload',
    get_string('pluginname', 'local_csvupload'),
    new moodle_url('/local/csvupload/index.php'), $capabilities));
}
