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
 * Report table class
 *
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Report table sql extended
 *
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */
class csv_report extends table_sql {

    /**
     * Column controls
     *
     * @param stdClass $data
     * @return string
     */
    public function col_timecreated($data) {
        $date = ($data->timecreated) ? date('d-m-Y H:i:s', $data->timecreated) : '';
        return $date;
    }

    /**
     * Column controls
     *
     * @param stdClass $data
     * @return string
     */
    public function col_timesent($data) {
        $date = ($data->timesent) ? date('d-m-Y H:i:s', $data->timesent) : '';
        return $date;
    }
}
