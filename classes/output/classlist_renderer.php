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

namespace mod_capquiz\output;

use mod_capquiz\capquiz;
use mod_capquiz\capquiz_user;
use mod_capquiz\capquiz_badge;

defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');
require_once($CFG->dirroot . '/question/editlib.php');

/**
 * @package     mod_capquiz
 * @author      Aleksander Skrede <aleksander.l.skrede@ntnu.no>
 * @copyright   2018 NTNU
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class classlist_renderer {

    private $capquiz;
    private $renderer;

    public function __construct(capquiz $capquiz, renderer $renderer) {
        $this->capquiz = $capquiz;
        $this->renderer = $renderer;
    }

    public function render() {
        $users = capquiz_user::list_users($this->capquiz);
        $rows = [];
        $badge_registry = new capquiz_badge($this->capquiz->course_module_id(), $this->capquiz->id());
        for ($i = 0; $i < count($users); $i++) {
            $user = $users[$i];
            $rows[] = [
                'index' => $i + 1,
                'student_id' => $user->id(),
                'username' => $user->username(),
                'firstname' => $user->first_name(),
                'lastname' => $user->last_name(),
                'rating' => $user->rating(),
                'stars' => $badge_registry->number_of_stars($user)
            ];
        }
        $leaderboard = $this->renderer->render_from_template('capquiz/classlist', [
            'users' => $rows
        ]);
        return $leaderboard;
    }
}
