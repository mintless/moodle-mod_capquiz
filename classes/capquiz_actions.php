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
 * @package     mod_capquiz
 * @author      Aleksander Skrede <aleksander.l.skrede@ntnu.no>
 * @copyright   2018 NTNU
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_capquiz;

defined('MOODLE_INTERNAL') || die();

class capquiz_actions {

    public static $parameter = 'action';
    public static $redirect = 'redirect';
    public static $attemptanswered = 'answered';
    public static $attemptreviewed = 'reviewed';
    public static $setquestionlist = 'set-question-list';
    public static $setquestionrating = "set-question-rating";
    public static $setdefaultqrating = "set-default-question-rating";
    public static $addquestion = 'add-question';
    public static $publishquestionlist = 'publish-question-list';
    public static $removequestion = 'remove-question';
    public static $createqlisttemplate = 'create-question-list-template';

}
