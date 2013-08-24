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
 * Legacy log reader.
 *
 * @package    logstore_legacy
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_legacy\log;

defined('MOODLE_INTERNAL') || die();

class store implements \tool_log\log\store, \core\log\reader {
    public function __construct() {
    }

    public function get_name() {
        return get_string('pluginname', 'logstore_legacy');
    }

    public function get_description() {
        return get_string('pluginname_desc', 'logstore_legacy');
    }

    public function can_access(\context $context) {
        return true;
    }

    public function get_events($selectwhere, array $params, $order, $limitfrom, $limitnum) {
        // TODO
    }

    public function get_events_count($selectwhere, array $params) {
        // TODO
    }

    public function dispose() {
    }
}
