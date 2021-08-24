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

use stdClass;

defined('MOODLE_INTERNAL') || die();

class glicko_rating_system extends capquiz_rating_system {

    private $record;
	private $multiplier = 173.7178;

    public function configure(\stdClass $configuration) {
        if ($configuration->deviation) {
            $this->deviation = $configuration->deviation;
        }
        if ($configuration->volatility) {
            $this->volatility = $configuration->volatility;
        }
		if ($configuration->tau) {
            $this->tau = $configuration->tau;
        }
    }

    public function configuration() {
        $config = new \stdClass;
        $config->deviation = $this->deviation;
        $config->volatility = $this->volatility;
        $config->tau = $this->tau;
        return $config;
    }

    public function default_configuration() {
        $config = new \stdClass;
        $config->deviation = 350; //default rd
        $config->volatility = 0.06; //sigma
		$config->tau = 0.75;		
        return $config;
    }
		
    public function update_user_rating(capquiz_user $user, capquiz_question $question, float $score) {

		$user->id = $user->id();
		$user->sigma = $this->volatility;
        $user->newrating = ($user->rating() - 1500)/$this->multiplier;
		$user->deviation = $this->deviation;
		$user->phi = $user->deviation/$this->multiplier;
		$user->questionid = $question->id();
		
		$questionratings = new stdClass();
		$questionratings = $this->question_ratings_by_question($question->id());
		if (count($questionratings) == 0) {
		// was wenn zero?
		}		
		$v = 0;
		$delta = 0;		
		foreach ($questionratings as $question) {
			$q_rd = 350/$this->multiplier;	// auf 350 fix?	
			$q_rating = ($question->rating-1500)/$this->multiplier;			
			$E = $this->E($user->newrating, $q_rating, $q_rd);
			$g = $this->g($q_rd);			
			$v += ( $g * $g * $E * ( 1 - $E ) );
			$delta += $g * ( $score - $E );
		}
		$user->v = 1.0 / $v;
		$user->delta_sum = $delta;
		$user->delta = $v * $delta;
		
        $user->set_rating($this->glicko($user, true));		
    }
	

    public function question_victory_ratings(capquiz_question $winner, capquiz_question $loser) {	 
		$loser->sigma = $this->volatility;
        $loser->newrating = ($loser->rating() - 1500)/$this->multiplier;
		$loser->deviation = $this->deviation;
		$loser->phi = $this->deviation/$this->multiplier;
		print_object($loser); die();
		$userratingsloser = new stdClass();
		$userratingsloser = $this->user_ratings_by_question($loser->id());

		if (count($userratingsloser) == 0) {
			$userratingloser->rating = $loser->newrating;
		}
		$v_loser = 0;
		$delta_loser = 0;		
		foreach ($userratingsloser as $userrating) {
			$user_rd = 350/$this->multiplier;	// auf 350 fix?	
			$user_rating = ($userrating->rating-1500)/$this->multiplier;			
			$E = $this->E($loser->newrating, $user_rating, $user_rd);
			$g = $this->g($user_rd);			
			$v_loser += ( $g * $g * $E * ( 1 - $E ) );
			$delta_loser += $g * ( 0 - $E );
		}
		$loser->v = 1.0 / $v_loser;
		$loser->delta_sum = $delta_loser;
		$loser->delta = $v_loser * $delta_loser;
		$loser->set_rating($this->glicko($loser, false));

		$winner->sigma = $this->volatility;
        $winner->newrating = ($winner->rating() - 1500)/$this->multiplier;
		$winner->deviation = $this->deviation;
		$winner->phi = $this->deviation/$this->multiplier;		
		$userratingswinner = new stdClass();
		$userratingswinner = $this->user_ratings_by_question($winner->id());
		if (count($userratingswinner) == 0) {
			$userratingswinner->rating = $winner->newrating;
		}			
		$v_winner = 0;
		$delta_winner = 0;		
		foreach ($userratingswinner as $userrating) {
			$user_rd = 350/$this->multiplier;	// auf 350 fix?	
			$user_rating = ($userrating->rating-1500)/$this->multiplier;			
			$E = $this->E($winner->newrating, $user_rating, $user_rd);
			$g = $this->g($user_rd);			
			$v_winner += ( $g * $g * $E * ( 1 - $E ) );
			$delta_winner += $g * ( 1 - $E );
		}
		$winner->v = 1.0 / $v_winner;
		$winner->delta_sum = $delta_winner;
		$winner->delta = $v_winner * $delta_winner;
        $winner->set_rating($this->glicko($winner, false)); 
 }
	
	private function summation($data, $score) {
		
		
		return $data;
	}
	
	private function glicko($data, $realplayer) {
		$tau = $this->tau;
		$sigma = $data->sigma;
		$rating = $data->newrating;
		$deviation = $data->deviation;
		$phi = $data->phi;
		$v = $data->v;
		$delta_sum = $data->delta_sum;
		$delta = $data->delta;
				
		$a = log( $sigma * $sigma );
		$x_prev = $a;
		$x = $x_prev;
		$tausq = $tau * $tau;
		$phisq = $phi * $phi;
		$deltasq = $delta * $delta;
		do {
			$exp_xp = exp( $x_prev );
			$d = $phi * $phi + $v + $exp_xp;
			$deltadsq = $deltasq / ($d * $d);
			$h1 = -( $x_prev - $a ) / ( $tausq ) - ( 0.5 * $exp_xp / $d ) + ( 0.5 * $exp_xp * $deltadsq );
			$h2 = ( -1.0 / $tausq ) - ( ( 0.5 * $exp_xp ) * ( $phisq + $v ) / ( $d * $d ) ) + ( 0.5 * $deltasq * $exp_xp * ( $phisq + $v - $exp_xp ) / ( $d * $d * $d ) );
			$tmp_x = $x;
			$x = $x_prev - ( $h1 / $h2 );
			$x_prev = $tmp_x;
		} while (abs($x - $x_prev) > 0.1);
		$sigma_p = exp( $x / 2 );
		$phi_star = sqrt( $phisq + ( $sigma_p * $sigma_p ) );
		$phi_p = 1.0 / ( sqrt( ( 1.0 / ( $phi_star * $phi_star ) ) + ( 1.0 / $v ) ) );
		$mu_p = $rating + $phi_p * $phi_p * $delta_sum;
				
		$data->newrating = ($mu_p * $this->multiplier)+1500;
		$data->newrd = $this->multiplier * $phi_p;
		$data->newvolatility = $mu_p;
		if ($realplayer == true){
		$this->insert_user_rating_entry_glicko($data->id, $data->newrating, $data->newrd, $data->newvolatility, $data->sigma, $data->questionid);
		}
		
		return $data->newrating;
	}
	
	private function g($phi) {
		return 1.0 / ( sqrt( 1.0 + ( 3.0 * $phi * $phi) / M_PI * M_PI ) );
	}

	private function E($mi_a, $mi_b, $phi_b) {
		return 1.0 / ( 1.0 + exp( -($this->g($phi_b))*( $mi_a - $mi_b ) ) );
	}
	
	private function insert_user_rating_entry_glicko(int $userid, float $rating, float $deviation, float $volatility, float $sigma, int $questionid) {
        global $DB, $USER;

        $record = new stdClass();
        $record->capquiz_user_id = $userid;
        $record->rating = $rating;
        $record->deviation = $deviation;
        $record->volatility = $volatility;
		$record->sigma = $sigma;
		$record->question_id = $questionid;
        $record->timecreated = time();
        try {
            $ratingid = $DB->insert_record('capquiz_user_rating_glicko', $record);
            $record->id = $ratingid;
            return $record;
        } catch (dml_exception $e) {
            return null;
        }
    }
	
    private function question_ratings_by_question($questionid) {
        global $DB;
        $sql = "SELECT *
                  FROM {capquiz_question_rating}
                 WHERE capquiz_question_id = :question_id";
        $record = $DB->get_records_sql($sql, ['question_id' => $questionid]);

        return $record;
    }

    private function user_ratings_by_question($questionid) {
        global $DB;
        $sql = "SELECT CONCAT(cur.timecreated,'_',ca.time_answered), cur.rating
                  FROM {capquiz_user_rating} cur
				  LEFT JOIN {capquiz_attempt} ca ON ca.user_id = cur.capquiz_user_id
				  JOIN {capquiz_question} cq ON cq.id = ca.question_id
                 WHERE cq.id = :question_id";
        $record = $DB->get_records_sql($sql, ['question_id' => $questionid]);

        return $record;
    }	
}
