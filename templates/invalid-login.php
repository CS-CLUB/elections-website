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
 * Default content to display when a user has invalid login, this may be due
 * to the fact that they do not have an account, used the wrong password/username,
 * or have not been a member for at least one semester.
 */
?>
<div class="alert alert-error">
	<h1>Invalid Login Information!</h1>
	<br/>
  <p> 
  		The login information you provided is invalid! This may be due to the fact that you
  		are not a registered club member, used the wrong password/username or that you have
  		not been a member for <strong>at least one semester</strong>.
  </p>
  <p>
  		If you are currently not a club member and would like more information about the Computer
  		Science Club then please click below to visit the Computer Science Club website. If you
  		are a member who has been registered for at least one semester and for some reason cannot
  		login then please visit our <a target="_blank" href="http://uoitcsc.dyndns.org/contact">Contact Page</a>
  		and contact us about your issue.
  </p>
  <p><a href="http://uoitcsc.dyndns.org" class="btn btn-primary btn-large">Learn more »</a></p>
</div>