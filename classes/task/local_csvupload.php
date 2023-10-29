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
 * Adhoc task class for csvupload
 *
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */

namespace local_csvupload\task;

use core\task\adhoc_task;

/**
 * Adhoc task class for csvupload
 *
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */
class local_csvupload extends adhoc_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'local_csvupload');
    }

    /**
     * Adhoc task email que.
     */
    public function execute() {
        global $CFG, $DB;
        $data = $this->get_custom_data();
        mtrace('------------- Email sending start ---------------------');
        if ($data) {
            $checkrecord = $DB->get_record('csvupload_stats', ['email' => $data[2], 'timesent' => null]);
            if ($checkrecord) {
                $supportuser = \core_user::get_support_user();
                $messagetext = $messagehtml = 'Test message';
                $checkrecord->username = $data[2];
                email_to_user($checkrecord, $supportuser, 'Test subject', $messagetext, $messagehtml, '', '', true);
                $checkrecord->timesent = time();
                $DB->update_record('csvupload_stats', $checkrecord);
            }
        }
        mtrace('------------- Email sending end ---------------------');
    }

}
