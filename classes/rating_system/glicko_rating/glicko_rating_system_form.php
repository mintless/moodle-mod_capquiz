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

namespace mod_capquiz;

use mod_capquiz\capquiz;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * @package     mod_capquiz
 * @author      Aleksander Skrede <aleksander.l.skrede@ntnu.no>
 * @copyright   2018 NTNU
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class glicko_rating_system_form extends \moodleform {

    /** @var \stdClass $configuration */
    private $configuration;

    public function __construct(\stdClass $configuration, \moodle_url $url) {
        $this->configuration = $configuration;
        parent::__construct($url);
    }

    public function definition() /*: void*/ {
        $form = $this->_form;

        $form->addElement('text', 'deviation', get_string('deviation', 'capquiz'));
        $form->setType('deviation', PARAM_INT);
        $form->addRule('deviation', get_string('deviation_specified_rule', 'capquiz'), 'required', null, 'client');
        $form->addRule('deviation', get_string('deviation_numeric_rule', 'capquiz'), 'numeric', null, 'client');
        $form->setDefault('deviation', $this->configuration->deviation);
        $form->addHelpButton('deviation', 'deviation', 'capquiz');

        $form->addElement('text', 'volatility', get_string('volatility', 'capquiz'));
        $form->setType('volatility', PARAM_INT);
        $form->addRule('volatility', get_string('volatility_specified_rule', 'capquiz'), 'required', null, 'client');
        $form->addRule('volatility', get_string('volatility_numeric_rule', 'capquiz'), 'numeric', null, 'client');
        $form->setDefault('volatility', $this->configuration->volatility);
        $form->addHelpButton('volatility', 'volatility', 'capquiz');
		
		$form->addElement('text', 'tau', get_string('tau', 'capquiz'));
        $form->setType('tau', PARAM_INT);
        $form->addRule('tau', get_string('tau_specified_rule', 'capquiz'), 'required', null, 'client');
        $form->addRule('tau', get_string('tau_numeric_rule', 'capquiz'), 'numeric', null, 'client');
        $form->setDefault('tau', $this->configuration->tau);
        $form->addHelpButton('tau', 'tau', 'capquiz');

        $this->add_action_buttons(false);
    }
}