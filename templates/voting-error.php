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
 * Default content to display when a voting error has occurred, this may be due
 * to the fact that they do not have an account, provided invalid voting information
 * (such as voting for an invalid nominee), tried to vote a second time, or tried to
 * vote when either the nomination/election period is closed.
 */
?>
<div class="alert alert-error">
  <h1>Voting Error!</h1>
  <br/>
  <p> 
      There was an error while trying to process your voting submission, please try
	  logging out of the site, exiting the page, and then logging back in again. If
	  that does not resolve the issue then please contact the Computer Science
	  Club.
  </p>
  <p><a href="http://cs-club.ca" class="btn btn-primary btn-large">Contact Us »</a></p>
</div>