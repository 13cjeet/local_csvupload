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
 * Main landing page of plugin
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/local/csvupload/csv_form.php');

core_php_time_limit::raise(60 * 60);
raise_memory_limit(MEMORY_HUGE);

admin_externalpage_setup('local_csvupload');

$returnurl = new moodle_url('/local/csvupload/index.php');
$reportnurl = new moodle_url('/local/csvupload/report.php');


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_csvupload'));

$mform1 = new local_uploadcsv_form1();

if ($formdata = $mform1->get_data()) {
    $iid = csv_import_reader::get_new_iid('csvupload');
    $cir = new csv_import_reader($iid, 'csvupload');

    $validcols = ['firstname', 'lastname', 'email'];

    $content = $mform1->get_file_content('userfile');
    $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
    $csvloaderror = $cir->get_error();

    // If there are no import errors then proceed.
    if (empty($csvloaderror)) {

        // Get header (field names).
        $filecolumns = $cir->get_columns();

        if (count($filecolumns) != 3) {
            throw new moodle_exception('csvloaderror', 'error', $returnurl,
                'CSV contains more column', 'CSV contains more column');
        }

        $table = new html_table();
        $table->attributes['class'] = 'boxaligncenter';

        $heading = $body = $allrow = [];

        foreach ($filecolumns as $key => $val) {
            if (!in_array($val, $validcols) || $key != array_search($val, $validcols)) {
                throw new moodle_exception('csvloaderror', 'error', $returnurl,
                    $val.' column does not match', 'check column name and order');
            }
            $heading[] = ucfirst($val);
        }

        $table->head = $heading;
        $cir->init();
        for ($numlines = 0; $numlines <= $formdata->previewrows; $numlines++) {
            $lines = $cir->next();
            if ($lines) {
                $body[] = $lines;
            }
        }
        $table->data = $body;
        echo html_writer::table($table);

        $cir->init();
        for ($allnumlines = 0; $allnumlines <= $readcount - 1; $allnumlines++) {
            $alllines = $cir->next();
            if ($alllines) {
                $allrow[] = $alllines;
            }
        }

        $mform2 = new local_uploadcsv_form2('submit.php', ['data' => ['iid' => $iid, 'processdata' => json_encode($allrow),
            'filecolumns' => json_encode($filecolumns)]]);

        if ($formdata = $mform2->is_cancelled()) {
            $cir->cleanup(true);
            redirect($returnurl);
        } else {
            $mform2->display();
        }

    } else {
        throw new moodle_exception('csvloaderror', 'error', $returnurl,
            $csvloaderror, $csvloaderror);
    }

} else {
    $mform1->display();
}

echo "<hr>";
echo $OUTPUT->single_button($reportnurl, 'View Report');
echo $OUTPUT->footer();

