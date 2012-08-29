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
require_once 'inc/db_interface.php';
require 'inc/election_auth.php';
require 'inc/election_date.php';
require_once 'inc/election.php';
require_once 'inc/utility.php';
require_once 'inc/validate.php';
require_once 'inc/verify.php';

session_start();

/* Connect to the databases */
$mysqli_accounts = new mysqli("localhost", $db_user, $db_pass, $db_acc_name);
$mysqli_elections = new mysqli("localhost", $db_user, $db_pass, $db_elec_name);

/* check connection */
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

/* User has a valid login cookie set / has logged into the site with valid account */
if (verify_login_cookie($mysqli_accounts, $SESSION_KEY)
	|| verify_login_session($mysqli_accounts, $_SESSION['login'], $SESSION_KEY))
{

	/* FIX, forgot to account for when user has login cookie set but there is no session
	 * data, have to retrieve username from cookie and then set the session data
	*/
	if (verify_login_cookie($mysqli_accounts, $SESSION_KEY))
	{
		/* Get the login cookie data */
		$login_cookie = htmlspecialchars($_COOKIE['login']);

		/* Get the username from login cookie data and set session info */
		$username = username_from_session($mysqli_accounts, $login_cookie, $SESSION_KEY);
		set_session_data($mysqli_accounts, $username, $SESSION_KEY);
	}

	include 'templates/header-member.php'; 
}
else
{
	include 'templates/header.php';
}

/* Display the election results */
$winners = get_winners($mysqli_elections);
include 'templates/election-results.php';

/* close connection */
$mysqli_accounts->close();
$mysqli_elections->close();


include 'templates/footer.php';
exit();
?>