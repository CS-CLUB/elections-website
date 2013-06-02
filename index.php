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

require_once 'inc/db_interface.php';
require 'inc/election_auth.php';
require 'inc/election_date.php';
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
 *    and the post data for NOMINATION voting is SET
 *
 *		a)	If the nomination vote post data is valid and it is the nomination period
 *
 *			-> Record their nomination vote
 *
 *		b)	Elseif (check if they are nominating themselves)
 *
 *			-> Record their nominate_self vote (does not use up their vote!)
 *
 * 6. User has a valid login cookie set / has logged into the site with valid account
 * 	  and the post data for ELECTION voting is SET
 *
 *		a)	If the election vote post data is valid and it is the election period
 *
 *			-> Record their election vote
 *
 * 7. User has a valid login cookie set / has logged into the site with valid account
 *    and there is NO nomination/election post data
 *
 * 		a) If the user has not already voted display the template for nomination/election
 * 			 period voting
 *
 * 		b) If the user has already voted then dislplay the thank you for voting page with
 * 			 election results/information if applicable
 *
 * 8. Display the footer template at the bottom of the page regardless
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
	$login_username = $_SESSION['login_username'];
	session_unset();

	/* a) If the user accepted the terms and conditions they are allowed to login and vote */
	if ($_POST['accept_rules'] == 1)
	{
		add_member($mysqli_accounts, $mysqli_elections, $login_username);
		set_session_data($mysqli_accounts, $login_username, $SESSION_KEY);

		if ($login_remember == 1)
		{
			set_login_cookie();
		}
	}

	/* Refresh the page */
	header('Location: '.$_SERVER['REQUEST_URI']);
}
/* 5. User has a valid login cookie set / has logged into the site with valid account
 *    and the post data for NOMINATION voting is SET
 */
elseif ((verify_login_cookie($mysqli_accounts, $SESSION_KEY)
		|| verify_login_session($mysqli_accounts, $_SESSION['login'], $SESSION_KEY))
		&& isset($_POST['nomination_vote']))
{

	if (!isset($_POST['president_nom']))
	{
		$_POST['president_nom'] = 'None';
	}

	if (!isset($_POST['vicepresident_nom']))
	{
		$_POST['vicepresident_nom'] = 'None';
	}

	if (!isset($_POST['coordinator_nom']))
	{
		$_POST['coordinator_nom'] = 'None';
	}

	if (!isset($_POST['treasurer_nom']))
	{
		$_POST['treasurer_nom'] = 'None';
	}
	/* An array mapping the positions to the nominee */
	$positions = array(	'President'         => $_POST['president_nom'],
						'Vice President'    => $_POST['vicepresident_nom'],
						'Coordinator'       => $_POST['coordinator_nom'],
						'Treasurer'         => $_POST['treasurer_nom'],
					  );

	/* An array containing the positions a person has nominated themselves for */
	$positions_self = array();

	/* If nomination vote post data valid and its nomination period, record nomination vote */
	if (validate_nomination_vote($mysqli_elections, $positions)
		&& is_nomination($mysqli_elections))
	{
		/* Get each position the user has nominated themselves for */
		foreach ($positions as $position => $nominee)
		{
			if (validate_nominate_self($nominee))
			{
				$positions_self[] = $position;
			}
		}

		/* Nominate user for each position the user nominated themselves for, now others can vote for them */
		if (count($positions_self) > 0)
		{
			nominate_self($mysqli_elections, $_SESSION['access_account'], $positions_self);
		}

		/* Record the nominees and the position the nominees are in that the user voted for */
		nomination_vote($mysqli_elections, $_SESSION['access_account'], $positions);

		/* Refresh the page */
		header('Location: '.$_SERVER['REQUEST_URI']);
	}
	/* Voting error, user either posted invalid data, or tried to vote when election is closed */
	else
	{
		include 'templates/header-member.php';
		include 'templates/voting-error.php';

	}
}
/* 6. User has a valid login cookie set / has logged into the site with valid account
 * 	  and the post data for ELECTION voting is SET
*/
elseif ((verify_login_cookie($mysqli_accounts, $SESSION_KEY)
		|| verify_login_session($mysqli_accounts, $_SESSION['login'], $SESSION_KEY))
		&& isset($_POST['election_vote']))
{
	/* An array mapping the positions to the election nominee */
	$positions = array(	'President'         => '',
						'Vice President'    => '',
						'Coordinator'       => '',
						'Treasurer'         => ''
					   );
	/* An array mapping the position to the POST data name */
	$positions_post = array('President'			=> 'president_elect',
							'Vice President'	=> 'vicepresident_elect',
							'Coordinator'       => 'coordinator_elect',
							'Treasurer'         => 'treasurer_elect'
						   );

	/* TODO CLEAN THIS UP -- I DO NOT LIKE THIS!!!
	 * For each position's post data select just the name of the nominee from the position, ignoring the
	 * parentheses indicating "(candidate)" or "(incumbent)"
	 */
	foreach ($positions_post as $position => $post_name)
	{
		if (preg_match('/(^(([A-Za-z]+)|\s{1}[A-Za-z]+)+)\s*?(\(Candidate\)|\(Incumbent\))$/', $_POST[$post_name], $matches))
		{
			// The first regex group, $matches[1] is the nominee's name
			$positions[$position] = $matches[1];
		}
	}

	/* If election vote post data valid and its election period, record election vote */
	if (validate_election_vote($mysqli_elections, $positions)
		&& is_election($mysqli_elections))
	{
		/* Record the final election nominees and the position they are in that the user voted for */
		election_vote($mysqli_elections, $_SESSION['access_account'], $positions);

		/* Refresh the page */
		header('Location: '.$_SERVER['REQUEST_URI']);
	}
	/* Voting error, user either posted invalid data, or tried to vote when election is closed */
	else
	{
		include 'templates/header-member.php';
		include 'templates/voting-error.php';
	}
}
/* 7. User has a valid login cookie set / has logged into the site with valid account */
elseif (verify_login_cookie($mysqli_accounts, $SESSION_KEY)
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

	/* a) If the user has not already voted display the template for nomination/election period voting */
	if (!has_voted_all_positions($mysqli_elections, $_SESSION['access_account'], "nomination")
	//(!has_voted($mysqli_elections, $_SESSION['access_account'], "nomination")
		&& is_nomination($mysqli_elections))
	{
		/* Get the nominees needed to populate the nomination voting form */
		$nominees = get_nominees($mysqli_elections);
		/* Get the incumbents to ensure an incumbent cannot nominate themself */
		$incumbents = get_incumbents($mysqli_elections);
		include 'templates/nomination-form.php';
	}
	elseif (!has_voted($mysqli_elections, $_SESSION['access_account'], "election")
			&& is_election($mysqli_elections))
	{
		/* Get the candidates and incumbents needed to populate the election voting form*/
		$candidates = get_candidates($mysqli_elections);
		$incumbents = get_incumbents($mysqli_elections);
		include 'templates/election-form.php';
	}
	/*	b) 	If the user has already voted then display the thank you for voting page with
	 * 		election results/information if applicable
	 */
	elseif (has_voted($mysqli_elections, $_SESSION['access_account'], "nomination")
			|| has_voted($mysqli_elections, $_SESSION['access_account'], "election"))
	{
		include 'templates/already-voted.php';
	}
	else
	{
		/* "Nomination closed" template if its after the nomination period and before election opens */
		if (is_nomination_closed($mysqli_elections))
		{
			include 'templates/nomination-closed.php';
		}
		/* "Election closed" template, it is AFTER the next weekday after Sept. 14th */
		else
		{
			include 'templates/election-closed.php';
		}
	}
}


/*if(isset($_SESSION['username']))
{
    $username = $_SESSION['username'];
}
else {
//	exit();
}

$username = 'gnu_user';
*/

/* Get the member information for the user logged in */
//$member = add_member($mysqli_accounts, $mysqli_elections, $username);

//$member_info = get_member($mysqli_accounts, $username);

/* Nominate myself for president */
//nominate_self($mysqli_elections, $member['access_account'], $nominate_myself);

//$nominees = get_nominees($mysqli_elections);

/* Vote for the candidates in the nomination period */
//nomination_vote($mysqli_elections, $member['access_account'], $positions);

/* Determine the winning candidates and store them in the DB */
//determine_winners($mysqli_elections, "nomination");

/* Get the candidates and incumbents */
//$candidates = get_candidates($mysqli_elections);
//$incumbents = get_incumbents($mysqli_elections);

/* Vote for the individuals in the final election */
//election_vote($mysqli_elections, $member['access_account'], $election_vote);

/* Finally, determine the winners of the election */
//determine_winners($mysqli_elections, "election");

/* Display the member's info */
/*
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
*/

/* Display the nominees */
//echo '<p>nominees:</p>';
//var_dump($nominees);

/* Display the candidates and incumbents */
//echo '<p>Candidates and incumbents:</p>';
//var_dump($candidates);
//var_dump($incumbents);

/* Test for elections_tables_exist */
/*$temp = FALSE;
$temp = election_tables_exist($mysqli_elections);
if ($temp == TRUE)
{
	echo '<p style="font-size:150%;">Is the DB there?!: '. $temp.'<br/></p>';
}*/

/* close connection */
$mysqli_accounts->close();
$mysqli_elections->close();


include 'templates/footer.php';
exit();
?>
