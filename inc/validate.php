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


function validate_mysqli_connect($mysqli_connection)
{
	if ($mysqli_connection->connect_errno)
	{
		return FALSE;
	}
	return TRUE;
}

/*
 * Check that the user has entered valid data, do not attempt
 * to login to the account otherwise
 *
 * Validate the username and password before verifying if they are correct
 * @param string $username the username post data.
 * @return boolean TRUE if the input for the username is valid
 */
function validate_username($username)
{
	if (preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $username)
			&& strlen($username) < 32)
	{
		return TRUE;
	}
	return FALSE;
}


/* Validate the password 
 * @param string $password the password post data
 * @return boolean TRUE if the input for the password is valid
 */
function validate_password($password)
{
	if (preg_match('/^[a-zA-Z0-9\!\$\%\^\&\*\(\)\_\?]{6,31}$/', $password))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

/*
 * 
 * @param string $name the name to be validated if there is only letters and spaces
 * @return boolean TRUE if there is only letters and spaces
 */
function validate_nom_entry($name)
{
	if (preg_match('/^(([A-Za-z]+)|\s{1}[A-Za-z]+)+$/', $name)
			&& strlen($name) < 32)
	{
		return TRUE;
	}
	return FALSE;
}

/**
 * TODO test and make sure it actually works
 * Used to check if a given candidate (with their first, last and user name) is a candidate 
 * capable to be elected for the given position.
 * 
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param string $first_name the first name of a potential candidate
 * @param string $last_name the last name of a potential candidate  
 * @param string $user_name the user name of a potential candidate
 * @param string $position the position that the candidate might hold
 * @return boolean $is_candidate TRUE if the candidate is found in the database with the given position
 */
function validate_candidate($mysqli_elections, $first_name, $last_name, $user_name, $position)
{
	if (validate_nom_entry($first_name) && validate_nom_entry($last_name)
			&& validate_username($username))
	{
		/* Check if there is a candidate for the given position */
		return is_candidate($mysqli_elections, $first_name, $last_name, $user_name, $position);
	}
	return FALSE;
}

/**
 * TODO test and make sure it actually works
 * Used to check if a given nominee (with their first, last and user name) is a nominee
 * capable to be elected for the given position. First checks if the given information is valid.
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param string $first_name the first name of a potential nominee
 * @param string $last_name the last name of a potential nominee
 * @param string $user_name the user name of a potential nominee
 * @param string $position the position that the nominee might hold
 * @return boolean $is_candidate TRUE if the nominee is found in the database with the given position
 */
function validate_nominee($mysqli_elections, $first_name, $last_name, $user_name, $position)
{
	if (validate_nom_entry($first_name) && validate_nom_entry($last_name)
			&& validate_username($username))
	{
		//Check if there is a nominee for the given position
		return is_nominee($mysqli_elections, $first_name, $last_name, $user_name, $position);
	}
	return FALSE;
}

/**
 * TODO test and make sure it actually works
 * Used to check if a given nominee (with their first, last and user name) is a
 * user nominating themselves for a position during the nomination period.
 * 
 * @param string $nominee the full name of the nominee
 * @return boolean TRUE if the user is nominating themselves
 */
function validate_nominate_self($nominee)
{
	if (strcmp($nominee, $_SESSION['first_name'].' '.$_SESSION['last_name']) === 0)
	{
		return TRUE;
	}
	
	return FALSE;
}

/**
 * TODO test and make sure it actually works
 * Used to check if a given nominee (with their first, last and user name) is a nominee
 * capable to be elected for the given position. First checks if the given information is valid.
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param $positions An array of the positions and the full name of the nominee they voted for
 * @return 
 */
function validate_nomination_vote($mysqli_elections, $positions)
{
	//$has_voded = TRUE;
	foreach ($positions as $position => $nominee)
	{
		if (validate_nom_entry($nominee))
				//&& validate_username($username))
		{
			if (has_voted_position($mysqli_elections, $_SESSION['access_account'], $position, "nomination")
					|| (!is_nominee($mysqli_elections, $nominee, $position)
						&& !validate_nominate_self($nominee)))
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	return TRUE;
}

/**
 * TODO test and make sure it actually works
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param $positions An array of the positions and the full name of the nominee they voted for
 * @return
 */
function validate_election_vote($mysqli_elections, $positions)
{
	foreach ($positions as $position => $nominee)
	{
		if (validate_nom_entry($nominee))
			//&& validate_username($username))
		{
			if (has_voted_position($mysqli_elections, $_SESSION['access_account'], $position, "election")
					|| !is_candidate($mysqli_elections, $nominee, $position))
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	return TRUE;
}