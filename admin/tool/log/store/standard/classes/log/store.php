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
 * Standard log reader/writer.
 *
 * @package    log_standard
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_standard\log;

defined('MOODLE_INTERNAL') || die();

class store implements \tool_log\log\store, \tool_log\log\writer, \core\log\sql_reader {
    public function __construct() {
    }

    public function store(\core\event\base $event, \tool_log\log\manager $manager) {
        global $DB;

        $data = $event->get_data();
        $data['origin'] = getremoteaddr();
        $data['realuser'] = session_is_loggedinas() ? $_SESSION['USER']->realuser : null;

        // TODO: hack base instead
        $data['edulevel'] = $data['level'];
        unset($data['level']);

        $DB->insert_record('logstore_standard_log', $data);
    }

    public function get_name() {
        return get_string('pluginname', 'logstore_standard');
    }

    public function get_description() {
        return get_string('pluginname_desc', 'logstore_standard');
    }

    public function can_access(\context $context) {
        // TODO: where is context???
        return true;
    }

    public function get_events($selectwhere, array $params, $order, $limitfrom, $limitnum) {
        global $DB;

        $events = array();
        $records = $DB->get_records_select('logstore_standard_log', $selectwhere, $params, 'timecreated ASC, id ASC', '*', $limitfrom, $limitnum);

        foreach($records as $data) {
            $extra = array('origin'=>$data->origin, 'realuser'=>$data->realuse);
            $id = $data['id'];
            unset($data['origin']);
            unset($data['realuser']);
            unset($data['id']);

            // TODO: hack base instead
            $data['level'] = $data['edulevel'];
            unset($data['edulevel']);

            $events[$id] = \core\event\base::restore($data, $extra);
        }

        return $events;
    }

    public function get_events_count($selectwhere, array $params) {
        global $DB;
        return $DB->count_records_select('logstore_standard_log', $selectwhere, $params);
    }

    public function get_log_table() {
        return 'logstore_standard_log';
    }

    public function dispose() {
    }
}
