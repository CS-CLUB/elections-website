<?php
/*
 * CS-CLUB Elections Website
 *
 * Copyright (C) 2013 Jonathan Gillett, Joseph Heron, Computer Science Club at DC and UOIT
 * All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/*
 * The final election voting form, displays a form with the list of candidates and
 * incumbents for each position.
 * 
 * 
 * DEPENDENCIES
 * ------------
 * 
 * This template depends on the arrays $candidates and incumbents containing the positions
 * and the nominee for each position
 * 
 * An array mapping the positions to the incumbent
 * $candidates = array( 'President'       => '',
 *      				'Vice President'  => '',
 *      				'Coordinator'     => '',
 *      				'Treasurer'       => ''
 *       			  );
 *  
 * An array mapping the positions to the incumbent
 * $incumbents = array( 'President'       => '',
 *      				'Vice President'  => '',
 *      				'Coordinator'     => '',
 *      				'Treasurer'       => ''
 *      			  );
 */
?>
<section id="election-form">
  <div class="page-header">
  <h1>Election Period Voting</h1>
  </div>
  <div class="row">
  <div class="span8">
    <form class="well form-horizontal" action="index.php" method="post" accept-charset="UTF-8">
    <fieldset>
      <div class="control-group">
        <label for="president_elect" class="control-label">President</label>
        <div class="controls">
          <select id="president_elect" name="president_elect" class="input-xlarge">
          <option></option>
          <?php 
            echo '<option>' . $candidates['President'][0] . ' (Candidate)' . '</option>';
            echo '<option>' . $incumbents['President'][0] . ' (Incumbent)' . '</option>';
          ?>
          </select>
        </div>
        </div>
        <div class="control-group">
        <label for="vicepresident_elect" class="control-label">Vice President</label>
        <div class="controls">
          <select id="vicepresident_elect" name="vicepresident_elect" class="input-xlarge">
          <option></option>
          <?php 
            echo '<option>' . $candidates['Vice President'][0] . ' (Candidate)' . '</option>';
            echo '<option>' . $incumbents['Vice President'][0] . ' (Incumbent)' . '</option>';
          ?>
          </select>
        </div>
        </div>
        <div class="control-group">
        <label for="coordinator_elect" class="control-label">Coordinator</label>
        <div class="controls">
          <select id="coordinator_elect" name="coordinator_elect" class="input-xlarge">
          <option></option>
          <?php 
            echo '<option>' . $candidates['Coordinator'][0] . ' (Candidate)' . '</option>';
            echo '<option>' . $incumbents['Coordinator'][0] . ' (Incumbent)' . '</option>';
          ?>
          </select>
        </div>
        </div>
          <div class="control-group">
        <label for="treasurer_elect" class="control-label">Treasurer</label>
        <div class="controls">
          <select id="treasurer_elect" name="treasurer_elect" class="input-xlarge">
          <option></option>
          <?php 
            echo '<option>' . $candidates['Treasurer'][0] . ' (Candidate)' . '</option>';
            echo '<option>' . $incumbents['Treasurer'][0] . ' (Incumbent)' . '</option>';
          ?>
          </select>
        </div>
        </div>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit" name="election_vote" value="Submit Vote">Submit Vote</button>
        </div>
    </fieldset>
    </form>
  </div>
  </div>
</section>