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
 * Default content to display when the election period is closed, provides information
 * about the Computer Science Club and links to register/website.
 *
 * DEPENDENCIES
 * ------------
 * 
 * This template depends on the election_date.php file having been sourced in order
 * to access the global variables for the election/nomination start and end dates.
 *
 */
?>
<div class="hero-unit">
	<h1>Election Period Closed</h1>
	<br/>
  <p> 	
  		Thank you for visiting the Computer Science Club election website, but the election
  		period for the year is currently closed. The election nomination period for candidates
  		starts at the beginning of each fall semester on <strong>
      <?php 
        echo DateTime::createFromFormat('m-d-H-i', $nomination_start_date)->format('F j'); 
      ?>
      </strong> with the final election period for the executive positions taking place on the first weekday after 
  		<strong>
      <?php 
        echo DateTime::createFromFormat('m-d-H-i', $nomination_end_date)->format('F j'); 
      ?>
      </strong> from <strong>12:00am - 11:59pm</strong>.
  </p>
  <p>
  		Election participation is only open to registered club members. If you would like to find out more about
  		the Computer Science Club and how you can become a member, please click below to find out more.
  </p>
  <p>
  		As well, don't forget to click below to view the election results and become familiar with your 
  		club executives.
  </p>
  <p><a href="http://cs-club.ca" class="btn btn-primary btn-large">Learn more »</a>
  	 <a href="results.php" class="btn btn-primary btn-large">View Results</a></p>
  
</div>