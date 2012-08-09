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
require_once 'inc/election_auth.php';
require_once 'inc/election.php';
require_once 'inc/utility.php';
require_once 'inc/validate.php';
require_once 'inc/verify.php';


session_start();

$error_msg = array();
$passphrase = "";
$first_name = "";
$last_name = "";
$student_id = "";
$email = "";
$username = "";
$password = "";
$access_account = 0;
//$join_date = date("Y-m-d");
//$last_access = date("Y-m-d");
$active = 1;

/* The test positions I am voting for */
$positions = array( 'President' => 'Bob Cajun',
                    'Vice President' => 'Bob Cajun',
                    'Coordinator' => 'Bob Cajun',
                    'Treasurer' => 'Bob Cajun');

/* The positions that I am voting for in the election */
$election_vote = array( 'President' => 'Bob Cajun',
                    	'Vice President' => 'Bob Cajun',
                    	'Coordinator' => 'Bob Cajun',
                    	'Treasurer' => 'Bob Cajun');

$nominate_myself = array('President', 'Coordinator');

/* Connect to the databases */
$mysqli_accounts = new mysqli("localhost", $db_user, $db_pass, $db_acc_name);
$mysqli_elections = new mysqli("localhost", $db_user, $db_pass, $db_elec_name);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/**
 * Handle which page to display based on the current events and whether or not the
 * user has a login cookie set, there are the following conditions.
 * 
 * TODO I DO NOT LIKE THIS IMPLEMENTATION, PERHAPS CLEANUP THE MULTILINE IF STATEMENTS INTO A CLEANER SET OF METHODS
 * 
 * 1. User is not logged in, display one of the templates if the nomination/election
 * 	  period is open/closed with info about the Computer Science Club
 * 
 * 2. User is not logged in and has submitted their login information
 * 		
 * 	  	a) If the login information is valid 
 * 
 * 			i)	If they are already a member and have logged in for the election website
 * 				before then sign them in
 *
 * 			ii) If it is the first time they have logged in display the first time login
 * 				page with election information and terms and conditions
 * 
 * 		b) If the login information is invalid dislpay the invalid login page
 *
 * 3. User is logged in and has clicked the Sign Out button
 *
 * 4. User has logged in for the first time and submitted the terms and conditions form
 * 		
 * 		a) If the user accepted the terms and conditions they are allowed to login and vote
 * 
 * 		b) If the user did not accept the terms and conditions they are redirected to default site
 * 
 * 5. User has a valid login cookie set / has logged into the site with valid account
 * 
 * 		a) If the user has not already voted display the template for nomination/election 
 * 			 period voting 
 * 
 * 		b) If the user has already voted then dislplay the thank you for voting page with
 * 			 election results/information if applicable
 * 
 * 6. Display the footer template at the bottom of the page regardless
 */

/* 
 * 1. User is not logged in, display one of the templates if the nomination/election
 * 	  period is open/closed with info about the Computer Science Club
 */
if (verify_login_cookie($mysqli_accounts, $SESSION_KEY) === false
	&& (!isset($_SESSION['login'])
	|| verify_login_session($mysqli_accounts, $_SESSION['login'], $SESSION_KEY) === false)
	&& !isset($_POST['login_username'])
	&& !isset($_POST['login_password'])
	&& !isset($_POST['accept_rules']))
{
	include 'templates/header.php';
	
	/* "Nomination open" template if its between Sept. 1, 12am to Sept 14th 11:59pm */
	if (is_nomination($mysqli_elections))
	{
		include 'templates/nomination-open.php';
	}
	/* "Nomination closed" template if its after the nomination period and before election opens */
	elseif (is_nomination_closed($mysqli_elections))
	{
		include 'templates/nomination-closed.php';
	}
	/* "Election open" template, it is the next weekday after Sept. 14th */
	elseif (is_election($mysqli_elections))
	{
		include 'templates/election-open.php';
	}
	else
	{
		include 'templates/election-closed.php';
	}
}
/* 2. User is not logged in and has submitted their login information */
elseif (verify_login_cookie($mysqli_accounts, $SESSION_KEY) === false
		&& (!isset($_SESSION['login'])
		|| verify_login_session($mysqli_accounts, $_SESSION['login'], $SESSION_KEY) === false)
		&& isset($_POST['login_username'])
		&& isset($_POST['login_password']))
{
	/* a) If the login information is valid and they entered the correct username/password  */
	if (validate_username($_POST['login_username']) && validate_password($_POST['login_password'])
		&& verify_login($mysqli_accounts, $_POST['login_username'] , $_POST['login_password'], $AES_KEY))
	{
		/* i)	If they are already a member and have logged in to the election website
		 * 		before then sign them in
		 */
		if (is_member($mysqli_accounts, $mysqli_elections, $_POST['login_username']))
		{
			set_session_data($mysqli_accounts, $_POST['login_username'], $SESSION_KEY);
			
			if ($_POST['login_remember'] == 1)
			{
				set_login_cookie();
			}
			
			/* Refresh the page */
			header('Location: '.$_SERVER['REQUEST_URI']);
		}
		/* ii) If it is the first time they have logged in display the first time login
		 * 	   page with election information and terms and conditions
		 */
		else
		{
			/* Store the login username and password they posted in the session, so they can
			 * be allowed to vote in the election if they accept the terms and conditions
			 */
			$_SESSION['login_username'] = $_POST['login_username'];
			$_SESSION['login_password'] = $_POST['login_password'];
			$_SESSION['login_remember'] = $_POST['login_remember'];
			
			include 'templates/header.php';
			include 'templates/first-login.php';
		}
	}
	/* b) The login information is invalid dislpay the invalid login page */
	else
	{
		include 'templates/header.php';
		include 'templates/invalid-login.php';
	}
}
/* 3. User is logged in and has clicked the Sign Out button */
elseif ((verify_login_cookie($mysqli_accounts, $SESSION_KEY)
		|| verify_login_session($mysqli_accounts, $_SESSION['login'], $SESSION_KEY))
		&& isset($_POST['signout']))
{
	session_unset();
	
	/* Overwrite the login cookie as NULL if it exists */
	if (isset($_COOKIE['login']))
	{
		setcookie('login', NULL, time()+1);
	}
	
	/* Refresh the page */
	header('Location: '.$_SERVER['REQUEST_URI']);
}
/* 4. User has logged in for the first time and submitted the terms and conditions form */
elseif (isset($_SESSION['login_username']) && isset($_SESSION['login_password'])
		&& isset($_POST['accept_rules']))
{
	$login_remember = $_SESSION['login_remember'];
	session_unset();
	
	/* a) If the user accepted the terms and conditions they are allowed to login and vote */
	if ($_POST['accept_rules'] == 1)
	{
		add_member($mysqli_accounts, $mysqli_elections, $_SESSION['login_username']);
		set_session_data($mysqli_accounts, $_SESSION['login_username'], $SESSION_KEY);
			
		if ($login_remember == 1)
		{
			set_login_cookie();
		}
	}
	
	/* Refresh the page */
	header('Location: '.$_SERVER['REQUEST_URI']);
}
/* 5. User has a valid login cookie set / has logged into the site with valid account */
elseif (verify_login_cookie($mysqli_accounts, $SESSION_KEY)
	|| verify_login_session($mysqli_accounts, $_SESSION['login'], $SESSION_KEY))
{
	include 'templates/header-member.php';
	
	/* Get the nominees needed to populate the nomination voting form */
	$nominees = get_nominees($mysqli_elections);
	include 'templates/nomination-form.php';
}

//include 'templates/election-open.php';
//include 'templates/election-closed.php';


//include 'templates/first-login.php';

if(isset($_SESSION['username']))
{
    $username = $_SESSION['username'];
}
else {
//	exit();
}

$username = 'gnu_user';


/* Get the member information for the user logged in */
$member = add_member($mysqli_accounts, $mysqli_elections, $username);

$member_info = get_member($mysqli_accounts, $username);

/* Nominate myself for president */
nominate_self($mysqli_elections, $member['access_account'], $nominate_myself);

$nominees = get_nominees($mysqli_elections);

/* Vote for the candidates in the nomination period */
nomination_vote($mysqli_elections, $member['access_account'], $positions);

/* Determine the winning candidates and store them in the DB */
determine_winners($mysqli_elections, "nomination");

/* Get the candidates and incumbents */
$candidates = get_candidates($mysqli_elections);
$incumbents = get_incumbents($mysqli_elections);

/* Vote for the individuals in the final election */
election_vote($mysqli_elections, $member['access_account'], $election_vote);

/* Finally, determine the winners of the election */
determine_winners($mysqli_elections, "election");

/* Display the member's info */
if (! empty($member_info))
{
    echo '<p style="font-size:150%;">The following is the member info for: '. $username . '<br/></p>';
    echo '<p style="font-size:125%">';

    foreach ($member as $value)
    {
        echo $value . "<br/>";
    }
    echo "</p>";

}

/* Display the nominees */
var_dump($nominees);

/* Display the candidates and incumbents */
echo '<p>Candidates and incumbents:</p>';
var_dump($candidates);
var_dump($incumbents);

/* Test for elections_tables_exist */
$temp = FALSE;
$temp = election_tables_exist($mysqli_elections);
if ($temp == TRUE)
{
	echo '<p style="font-size:150%;">Is the DB there?!: '. $temp.'<br/></p>';
}

/* close connection */
$mysqli_accounts->close();
$mysqli_elections->close();

include 'templates/footer.php';
exit();
?>