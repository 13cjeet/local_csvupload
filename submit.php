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
 * CSV submission mail queue report
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */

use local_csvupload\task\local_csvupload;

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot.'/local/csvupload/csv_form.php');

$iid = required_param('iid', PARAM_INT);

core_php_time_limit::raise(60 * 60);
raise_memory_limit(MEMORY_HUGE);

admin_externalpage_setup('local_csvupload');

$returnurl = new moodle_url('/local/csvupload/index.php');

$table = new flexible_table('test-flexible-table');
$table->define_baseurl($returnurl);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_csvupload'));

global $DB;

$insert = 0;
$mform2 = new local_uploadcsv_form2();
if ($formdata = $mform2->get_data()) {

    $table->define_columns(array_merge(json_decode($formdata->filecolumns), ['queued']));
    $cols = array_map('ucfirst', json_decode($formdata->filecolumns));
    $table->define_headers(array_merge($cols, ['Is Queued']));
    $table->setup();

    $adhoctask = new local_csvupload();
    foreach (json_decode($formdata->processdata) as $key => $val) {
        $email = 'Yes';
        if (!filter_var($val[2], FILTER_VALIDATE_EMAIL)) {
            $email = "No";
        } else {
            $adhoctask->set_custom_data($val);
            $time = time() + (2 * 60);
            $adhoctask->set_next_run_time($time);
            \core\task\manager::queue_adhoc_task($adhoctask);

            $record = new stdClass();
            $record->firstname = $val[0];
            $record->lastname = $val[1];
            $record->email = $val[2];
            $record->timecreated = $time;
            $insert = $DB->insert_record('csvupload_stats', $record);
        }
        $table->add_data(array_merge($val, [$email]));
    }
}

if ($insert) {
    $table->finish_html();
    echo $OUTPUT->single_button($returnurl, 'Return');
} else {
    \core\notification::error('Email not queued!');
}

echo $OUTPUT->footer();
