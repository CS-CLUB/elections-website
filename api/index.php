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
require_once '../inc/db_interface.php';
require '../inc/election_auth.php';
require_once '../inc/election.php';
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();


/*
 * The REST web service api which provides access to statistical and other
* useful data, which can be retrieved from the database using a GET operation
*/
$app = new \Slim\Slim();

/* Gets the results of a nomination or election period */
$app->get('/results/:type/:position', 'getResults');
$app->get('/results/:type', 'getResultsAll');

$app->run();


/** 
 * Get the results of a nomination or election period. Use this function
 * to get the vote breakdown after the nomination/election for the 
 * specified position only.
 * 
 * Displays the results as a JSON encoded array of the positions and the 
 * names of the individuals and the number of votes they received.
 * 
 * @package api
 *
 * @param string $type The type of election period ("nomination" or "election")
 * @param string $position The position to get the results for (President, Vice President,
 * Coordinator, Treasurer). The default if no argument is provided is to get all positions.
 */
function getResults($type, $position)
{
	global $db_user, $db_pass, $db_elec_name;

	/* Connect to the database */
	$mysqli_elections = new mysqli("localhost", $db_user, $db_pass, $db_elec_name);

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	/* Only get the results if the election period is closed */
	if (is_election_closed($mysqli_elections))
	{
		/* Encode the results as JSON */
		echo json_encode(get_results($mysqli_elections, $type, $position));
	}
}

/** 
 * Get the results of a nomination or election period. Use this function
 * to get the vote breakdown after the nomination/election for ALL positions.
 *
 * Displays the results as a JSON encoded array of the positions and the
 * names of the individuals and the number of votes they received.
 *
 * @package api
 *
 * @param string $type The type of election period ("nomination" or "election")
 */
function getResultsAll($type)
{
	global $db_user, $db_pass, $db_elec_name;

	/* Connect to the database */
	$mysqli_elections = new mysqli("localhost", $db_user, $db_pass, $db_elec_name);

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	/* Only get the results after the election period is closed */
	if (is_election_closed($mysqli_elections))
	{
		/* Encode the results as JSON */
		echo json_encode(get_results($mysqli_elections, $type));
	}
}

?>