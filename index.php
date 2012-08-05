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
require_once 'inc/utility.php';

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

include 'templates/header.php';
//include 'templates/election-open.php';
//include 'templates/election-closed.php';
//include 'templates/nomination-open.php';
//include 'templates/nomination-closed.php';
include 'templates/first-login.php';

session_start();
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

/* close connection */
$mysqli_accounts->close();
$mysqli_elections->close();

include 'templates/footer.php';
exit();
?>