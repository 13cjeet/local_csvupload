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

namespace local_csvupload;

/**
 * Unit test for csvupload
 *
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */

/**
 * class file for unit test
 *
 * @author    Parthajeet Chakraborty
 * @copyright  2023 <13cjeet@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_csvupload
 */
class csvupload_test extends \advanced_testcase {

    /**
     * Data provider for test_convert_duration_to_seconds.
     *
     * @return array the test cases.
     */
    public function test_adding() {
        global $CFG, $DB;
        require_once($CFG->libdir.'/csvlib.class.php');

        $this->resetAfterTest(true);

        $filepath = $CFG->dirroot.'/local/csvupload/example.csv';
        $linescount = count(file($filepath));

        $iid = \csv_import_reader::get_new_iid('csvupload');
        $cir = new \csv_import_reader($iid, 'csvupload');

        $content = fopen($filepath, 'r');
        $readcount = $cir->load_csv_content(fread($content, filesize($filepath)), 'UTF-8', 'comma');
        $csvloaderror = $cir->get_error();

        if (empty($csvloaderror)) {

            $cir->init();
            $adhoctask = new \local_csvupload\task\local_csvupload();
            for ($numlines = 0; $numlines <= $linescount - 1; $numlines++) {
                $lines = $cir->next();
                if ($lines) {
                    if (filter_var($lines[2], FILTER_VALIDATE_EMAIL)) {
                        $adhoctask->set_custom_data($lines);
                        $time = time() + (2 * 60);
                        $adhoctask->set_next_run_time($time);
                        \core\task\manager::queue_adhoc_task($adhoctask);
                        $record = new \stdClass();
                        $record->firstname = $lines[0];
                        $record->lastname = $lines[1];
                        $record->email = $lines[2];
                        $record->timecreated = $time;
                        $insert = $DB->insert_record('csvupload_stats', $record);
                        $this->assertEquals(true, $insert);
                    }
                }
            }
        }
    }

}
