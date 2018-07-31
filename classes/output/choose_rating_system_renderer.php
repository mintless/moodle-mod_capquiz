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
use mod_capquiz\capquiz_urls;
use mod_capquiz\form\view\choose_matchmaking_strategy_form;
use mod_capquiz\form\view\choose_rating_system_form;

defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');

class choose_rating_system_renderer {

    private $url;
    private $capquiz;
    private $renderer;

    public function __construct(capquiz $capquiz, renderer $renderer) {
        $this->capquiz = $capquiz;
        $this->renderer = $renderer;
        $this->url = capquiz_urls::view_rating_system_configuration_url();
    }

    public function set_redirect_url(\moodle_url $url) {
        $this->url = $url;
    }

    public function render() {
        global $PAGE;
        $url = $PAGE->url;
        $form = new choose_rating_system_form($this->capquiz, $url);
        if ($form_data = $form->get_data()) {
            $loader = $this->capquiz->rating_system_loader();
            $registry = $this->capquiz->rating_system_registry();
            $loader->set_rating_system($registry->rating_systems()[$form_data->rating_system]);
            redirect($this->url);
        }

        return $this->renderer->render_from_template('capquiz/choose_rating_system', [
            'form' => $form->render()
        ]);
    }
}
