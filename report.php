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
 * View report file
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require("$CFG->dirroot/local/csvupload/classes/csv_report.php");


admin_externalpage_setup('local_csvupload');

$returnurl = new moodle_url('/local/csvupload/index.php');
$download = optional_param('download', '', PARAM_ALPHA);


$table = new csv_report('uniqueid');
$table->is_downloading($download, 'test', 'testing123');

if (!$table->is_downloading()) {
    $PAGE->set_title('CSV upload report');
    $PAGE->set_heading('CSV upload report');
    $PAGE->navbar->add('CSV upload report', new moodle_url('/local/csvupload/report.php'));
    echo $OUTPUT->header();
}
$table->set_sql('*', "{csvupload_stats}", '1=1');

$table->define_baseurl(new moodle_url('/local/csvupload/report.php'));

$table->out(10, true);

if (!$table->is_downloading()) {
    echo "<hr>";
    echo $OUTPUT->single_button($returnurl, 'Got to Upload CSV');
    echo $OUTPUT->footer();
}
