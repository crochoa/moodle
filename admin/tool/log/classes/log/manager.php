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
 * Log store manager.
 *
 * @package    tool_log
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\log;

defined('MOODLE_INTERNAL') || die();

class manager implements \core\log\manager {
    /** @var \core\log\reader[] $readers */
    protected $readers;

    /** @var \tool_log\log\writer[] $readers */
    protected $writers;

    /** @var \tool_log\log\store[] $readers */
    protected $stores;

    public function store(\core\event\base $event) {
        $this->init();
        foreach ($this->writers as $plugin => $writer) {
            try {
                $writer->store($event, $this);
            } catch (\Exception $e) {
                debugging('Exception detected when logging event '.$event->eventname.' in '.$plugin.': '.$e->getMessage(), DEBUG_NORMAL, $e->getTrace());
            }
        }
    }

    public function get_readers(\context $context) {
        $this->init();
        $return = array();
        foreach ($this->readers as $plugin => $reader) {
            if ($reader->can_access($context)) {
                $return[$plugin] = $reader;
            }
        }
        return $return;
    }

    protected function init() {
        if (isset($this->stores)) {
            return;
        }
        $this->stores = array();
        $this->readers = array();
        $this->writers = array();

        $plugins = get_config('tool_log', 'enabled_stores');
        if (empty($plugins)) {
            return;
        }

        $plugins = explode(',', $plugins);
        foreach ($plugins as $plugin) {
            $classname = "\\$plugin\\log\\store";
            if (class_exists($classname)) {
                $store = new $classname();
                $this->stores[$plugin] = $store;
                if ($store instanceof \tool_log\log\writer) {
                    $this->writers[$plugin] = $store;
                }
                if ($store instanceof \core\log\reader) {
                    $this->readers[$plugin] = $store;
                }
            }
        }
    }

    public static function get_store_plugins() {
        return \core_component::get_plugin_list_with_class('logstore', 'log\store');
    }

    public function dispose() {
        if ($this->stores) {
            foreach($this->stores as $store) {
                $store->dispose();
            }
        }
        $this->stores = array();
        $this->readers = array();
        $this->writers = array();
    }
}
