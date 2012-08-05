<?php
/*
 *  UOIT/DC Computer Science Club Elections Website
 *  Copyright (C) 2012 UOIT/DC Computer Science Club
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/* 
 * Displays a welcome message for users when it is their first time logging into
 * the elections website as well as a disclaimer that they can only use the elections
 * website if they are currently attending UOIT as this is the only plausible way to
 * determine if someone is still attending UOIT.
 */
?>
<div class="hero-unit">
	<h1>Welcome!</h1>
	<br />
  <p> Thank you for logging into the Computer Science Club election website, since this is your
  		first time visiting the site this year we are going to go over some election information as
  		well as guidelines and rules.
  </p>
	<br />  
  <h2>Election Information</h2>
  <p> 
  		The election nomination period for candidates starts at the beginning of each fall semester on
  		<strong>September 1</strong>. During the nomination period you are able to nominate yourself
  		for the executive positions you wish to run for, as well as vote for other nominees to run
  		as candidates in the election. The nomination period is open until <strong>September 14</strong>
  		after which point the votes will be tallied and the final election will take place.
  </p>
  <p>
  		After the nomination period, the final election period for the executive positions takes place 
  		on the first day after <strong>September 14</strong> that lands on a weekday. The election period
  		on that day is open for a full 24-hours from 12:00am - 11:59pm. So unlike the nomination period it
  		is important that you remember to vote on the day of the final election.
  </p>
	<br />
  <h2>Election Guidelines and Rules</h2>
  <p>
  		Now that the election details have been covered the next important topic are the guidelines, while
  		there are not many guidelines and rules it is important that you adhere to the following. 
  </p>
  <ol>
  	<li>
  		<p>	You may only participate in the election if you are currently attending UOIT/DC, while you may have been
  				an active member in the past when you were attending UOIT/DC, unfortunately you must be a student who is 
  				currently enrolled at UOIT/DC in order to vote.
  		</p>
  </li>
  <li>
  	<p>	Only nominate yourself for a position that you intend to fulfill, do not nominate yourself for a position unless
  			you are committed to the responsibilities of that position.
  	</p>
  </li>
  <li>
  	<p>	Only club members who have been registered for at least one semester may vote, this is to give members a buffer
  			to become familiar with the club before voting in the election.
  	</p>
  </ol>
  <br />
<form action="index.php" method="post" accept-charset="UTF-8">
		<label class="checkbox" for="accept_rules">
			<input type="checkbox" value="1" id="accept_rules" name="accept_rules" />
			 <p><strong>I have read and agree to the rules and conditions</strong></p>
	  </label>
		<input id="rules_confirm" class="btn btn-primary btn-large" type="submit" name="rules_confirm" value="Submit" />
  </form>
</div>