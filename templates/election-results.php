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
 * Displays the results of the election, including a table with the positions
 * and the name of the elected individual as well as various charts and statistics.
 * 
 * DEPENDENCIES
 * ------------
 * 
 * This template depends on the arrays $winners containing the positions and the name
 * of the elected individual for that position
 * 
 * An array mapping the positions to an array for the winner containing the first name,
 * last name, and username
 * $winners = array('President'         => array('first_name' => '', 
 * 												 'last_name' => '', 
 * 												 'username' => ''),
 *					'Vice President'    => array('first_name' => '', 
 * 												 'last_name' => '', 
 * 												 'username' => ''),
 *					'Coordinator'       => array('first_name' => '', 
 * 												 'last_name' => '', 
 * 												 'username' => ''),
 *					'Treasurer'         => array('first_name' => '', 
 * 												 'last_name' => '', 
 * 												 'username' => ''),
 *				   );
 */
?>
<div class="hero-unit">
  <div class="page-header">
	<h1>Election Results</h1>
  </div>
  <h2>Elected Executives</h2>
  <div class="row">
    <div class="span8">  
      <table class="table">
      <thead>
        <tr>
        <th>Position</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
        </tr>
      </thead>
      <tbody>
        <?php
            foreach ($winners as $position => $winner)
            {
              echo '<tr>';
              echo '<td>' . $position . '</td>';
              echo '<td>' . $winner['first_name'] . '</td>';
              echo '<td>' . $winner['last_name'] . '</td>';
              echo '<td>' . $winner['username'] . '</td>';
              echo '</tr>';
            }
          ?>
      </tbody>
      </table>
    </div>
  </div>
  <h2>Vote Breakdown</h2>
  <div class="row">
    <div class="span6 chart">
   	  <div id="pie_president"></div>
    </div>
    <div class="span6 chart">
      <div id="pie_vicepresident"></div>
    </div>
  </div>
  <div class="row">
    <div class="span6 chart">
   	  <div id="pie_coordinator"></div>
    </div>
    <div class="span6 chart">
      <div id="pie_treasurer"></div>
    </div>
  </div>
</div>