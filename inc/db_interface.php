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


/**
 * Contains a collection of the functions that directly interact with the database
 * to provide a convenient database abstraction layer, in the future support could
 * be added to support other databases. At the moment the implementations are
 * specific to MySQL (5.1 is the version tested) and prepared statements are
 * used for all queries to provide a layer of protection against SQL injection.
 * 
 * TODO More work perhaps to add OOP features for scoping instead of just an
 * aggregation of many functions, a hierarchy of classes would be nice
 */


/**
 * A function which creates the appropriate tables for the start of a new election year
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 */
function new_election_tables($mysqli_elections)
{
	/* The members table */
	$members_tbl = 'members_';

	/* The positions tables for the nominations and elections */
	$positions_tbls = array('positions_nom_', 'positions_elect_');

	/* The voters table to record voters and winners table to record winners */
	$voters_winners_tbls = array('voters_nom_', 'voters_elect_', 'winners_nom_', 'winners_elect_');

	/* The current election year */
	$election_year = date('Y');

	/* Create the members table for the current election year */
	if ($stmt = $mysqli_elections->prepare("CREATE TABLE ". $members_tbl . $election_year .
												" (
													access_account SMALLINT UNSIGNED NOT NULL,
													first_name VARCHAR(32),
													last_name VARCHAR(32),
													PRIMARY KEY (access_account)
											 	  )"))
	{
		/* execute query */
		$stmt->execute();

		/* close statement */
		$stmt->close();
	}

	/* Create the positions tables for the current election year */
	foreach ($positions_tbls as $position_tbl)
	{
		if ($stmt = $mysqli_elections->prepare("CREATE TABLE ". $position_tbl . $election_year .
													" (
														reference SMALLINT UNSIGNED NOT NULL REFERENCES ".
															$members_tbl . $election_year . "(access_account),
														votes SMALLINT UNSIGNED,
														position VARCHAR(32)
													  )"))
		{
			/* execute query */
			$stmt->execute();

			/* close statement */
			$stmt->close();
		}
	}

	/* Create the voters and winners tables for the current election year */
	foreach ($voters_winners_tbls as $voters_winners_tbl)
	{
		if ($stmt = $mysqli_elections->prepare("CREATE TABLE ". $voters_winners_tbl . $election_year .
													" (
														reference SMALLINT UNSIGNED NOT NULL REFERENCES ".
															$members_tbl . $election_year . "(access_account),
														position VARCHAR(32)
													  )"))
		{
			/* execute query */
			$stmt->execute();

			/* close statement */
			$stmt->close();
		}
	}
}


/**
 * A function which determines if the election tables for the current year already exist
 * the purpose of this function is to determine if the daemon has already created the tables
 * for the current election year.
 * 
 * TODO CLEAN THIS METHOD UP, I DON'T LIKE THE IMPLEMENTATION OF ALL THE "IF STATEMENTS"
 * 
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB 
 */
function election_tables_exist($mysqli_elections)
{
	/* Election tables that exist for the current year */
	$tables_cur_year = array();
	
	/* The current election year */
	$election_year = date('Y');
	
	/* Get the election tables for the current year */
	if ($stmt = $mysqli_elections->prepare("SHOW TABLES LIKE '%".$election_year."'"))
	{
		/* bind parameters for markers */
		//$stmt->bind_param('s', "%".$election_year);
	
		/* execute query */
		$stmt->execute();
	
		/* bind result variables */
		$stmt->bind_result($table);
	
		while ($stmt->fetch())
		{
			$tables_cur_year[] = $table;
		}
		
		/* close statement */
		$stmt->close();
	}
	
	/* Verify that all of the tables for the current election year exist */
	if (count($tables_cur_year) === 7)
	{
		if (in_array('members_' . $election_year, $tables_cur_year)
			&& in_array('positions_nom_' . $election_year, $tables_cur_year)
			&& in_array('positions_elect_' . $election_year, $tables_cur_year)
			&& in_array('voters_nom_' . $election_year, $tables_cur_year)
			&& in_array('voters_elect_' . $election_year, $tables_cur_year)
			&& in_array('winners_nom_' . $election_year, $tables_cur_year)
			&& in_array('winners_elect_' . $election_year, $tables_cur_year))
		{
			return TRUE;
		}
	}
	
	return FALSE;
}


/**
 * A function which populates the election table with the candidates and
 * incumbents for the final phase of the election.
 * 
 * TODO Clean this method up, already have function to get candidates/incumbents
 * TODO Handle the case where there is no candidate for a position
 * TODO Handle the case where the inumbent is no longer attending UOIT
 * 
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB 
 */
function pop_election_table($mysqli_elections)
{
	/* An array mapping the positions to the candidate access_account */
	$candidates = array('President'         => 0,
						'Vice President'    => 0,
						'Coordinator'       => 0,
						'Treasurer'         => 0
	);
	
	/* An array mapping the positions to the incumbent access_account */
	$incumbents = array('President'         => 0,
						'Vice President'    => 0,
						'Coordinator'       => 0,
						'Treasurer'         => 0
	);
	
	$previous_year = (date('Y') - 1);
	$current_year = date('Y');
	
	/* The name of the table containing the current years nominees */
	$candidates_table = 'winners_nom_' . $current_year ;
	
	/* The name of the table containing the previous years winners */
	$incumbents_table = 'winners_elect_' . $previous_year ;
	
	/* The name of the table containing the current participates in the elections */
	$positions_elect_table = 'positions_elect_' . $current_year ;
	
	/* Get the candidate for each position */
	foreach ($candidates as $position => $access_account)
	{
		/* Get the candidate for the current position from the database */
		if ($stmt = $mysqli_elections->prepare("SELECT reference 
                                                    FROM " . $candidates_table .
                                                            " WHERE position LIKE ?"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('s', $position);
	
			/* execute query */
			$stmt->execute();
	
			/* bind result variables */
			$stmt->bind_result($access_account);
	
			$stmt->fetch();
	
			$candidates[$position] = $access_account;
	
			/* close statement */
			$stmt->close();
		}
	}
	
	
	/* Get the incumbents for each position */
	foreach ($incumbents as $position => $access_account)
	{
		/* Get the candidate for the current position from the database */
		if ($stmt = $mysqli_elections->prepare("SELECT reference
                                                    FROM " . $incumbents_table . 
                                                            " WHERE position LIKE ?"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('s', $position);
	
			/* execute query */
			$stmt->execute();
	
			/* bind result variables */
			$stmt->bind_result($access_account);
	
			$stmt->fetch();
	
			$incumbents[$position] = $access_account;
	
			/* close statement */
			$stmt->close();
		}
	}
	
	/* Populate the election positions table with the candidates and incumbents */
	foreach ($candidates as $position => $access_account)
	{
		if ($stmt = $mysqli_elections->prepare("INSERT INTO " . $positions_elect_table .
														" VALUES (?, 0, ?)"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('is', $access_account, $position);
				
			/* execute query */
			$stmt->execute();
				
			/* close statement */
			$stmt->close();
		}
	}
	foreach ($incumbents as $position => $access_account)
	{
		if ($stmt = $mysqli_elections->prepare("INSERT INTO " . $positions_elect_table .
														" VALUES (?, 0, ?)"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('is', $access_account, $position);
	
			/* execute query */
			$stmt->execute();
	
			/* close statement */
			$stmt->close();
		}
	}
}


/**
 * A Function which determines if a user already exists in the elections members table
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param int $access_Account The unique (primary key) access account number of the user
 * @return boolean True if the user already exists in the members table
 */
function is_member($mysqli_accounts, $mysqli_elections, $username)
{
	$access_account = 0;
	$is_member = FALSE;
	
	$current_year = date('Y');

	/* Get the unique access account for the user if the user exists */
	if ($stmt = $mysqli_accounts->prepare("SELECT access_account 
                                                FROM ucsc_members 
                                                    WHERE username LIKE ?"))
	{
		/* bind parameters for markers */
		$stmt->bind_param('s', $username);

		/* execute query */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($access_account);

		/* fetch value */
		$stmt->fetch();

		/* close statement */
		$stmt->close();
	}	
	 
	if ($stmt = $mysqli_elections->prepare("SELECT EXISTS(SELECT 1
			                                                FROM members_" . $current_year . 
			                                                    " WHERE access_account=?)"))
				{
		/* bind parameters for markers */
		$stmt->bind_param('i', $access_account);

		/* execute query */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($is_member);

		/* fetch value */
		$stmt->fetch();

		/* close statement */
		$stmt->close();
	}

	return $is_member;
}


/**
 * A function which adds a person who has logged into the elections website
 * to the members table if they do not already exist in the table. This is
 * the default operation each time a person logs into the website as there is
 * no other feasible way to determine who can be an eligible nominee.
 *
 * @param mysqli $mysqli_accounts The mysqli connection object for the ucsc accounts DB
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param string $username The username of the person to add to the members table
 * @return array An array containing the users unique access account (primary key) and name
 */
function add_member($mysqli_accounts, $mysqli_elections, $username)
{

	$member = array('access_account' => 0,
					'first_name' => '',
					'last_name' => '');
	$user_match = '';

	$current_year = date('Y');
	
	/* Get the users information from the database if it exists */
	if ($stmt = $mysqli_accounts->prepare("SELECT username
                                                FROM ucsc_members
                                                    WHERE username LIKE ?"))
	{
		/* bind parameters for markers */
		$stmt->bind_param('s', $username);

		/* execute query */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($user_match);

		/* fetch value */
		$stmt->fetch();

		/* close statement */
		$stmt->close();
	}

	/* If username found, get primary key (access account), and name of the user */
	if (strcasecmp($username, $user_match) === 0)
	{
		if ($stmt = $mysqli_accounts->prepare("SELECT access_account, first_name, last_name
                                                    FROM ucsc_members
                                                        WHERE username LIKE ?"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('s', $username);

			/* execute query */
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($member['access_account'], $member['first_name'], $member['last_name']);

			/* fetch value */
			$stmt->fetch();

			/* close statement */
			$stmt->close();
		}

		/* Add the user to the members table of the elections database if they do not exist */
		if (! is_member($mysqli_accounts, $mysqli_elections, $username))
		{
			if ($stmt = $mysqli_elections->prepare("INSERT INTO members_" .$current_year. " VALUES (?, ?, ?)"))
			{
				/* bind parameters for markers */
				$stmt->bind_param('iss', $member['access_account'], $member['first_name'], $member['last_name']);

				/* execute query */
				$stmt->execute();

				/* close statement */
				$stmt->close();
			}
		}
	}

	return $member;
}


/**
 * A function which returns the access account number and full name of a user who
 * exists in the elections database members table. Use this method if you have verified
 * that the member exists in the database using the is_member() method. 
 * 
 * @param mysqli $mysqli_accounts The mysqli connection object for the ucsc accounts DB
 * @param string $username The username of the member
 */
function get_member($mysqli_accounts, $username)
{
	$member = array('access_account' => 0,
					'first_name' => '',
					'last_name' => '');
	
	$user_match = '';

	/* Get the users information from the database if it exists */
	if ($stmt = $mysqli_accounts->prepare("SELECT username
                                                FROM ucsc_members
                                                    WHERE username LIKE ?"))
	{
		/* bind parameters for markers */
		$stmt->bind_param('s', $username);

		/* execute query */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($user_match);

		/* fetch value */
		$stmt->fetch();

		/* close statement */
		$stmt->close();
	}

	/* If username found, get primary key (access account), and name of the user */
	if (strcasecmp($username, $user_match) === 0)
	{
		if ($stmt = $mysqli_accounts->prepare("SELECT access_account, first_name, last_name
                                                    FROM ucsc_members
                                                        WHERE username LIKE ?"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('s', $username);

			/* execute query */
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($member['access_account'], $member['first_name'], $member['last_name']);

			/* fetch value */
			$stmt->fetch();

			/* close statement */
			$stmt->close();
		}
	}

	return $member;
}


/**
 * A function which gets the nominees for each position from the database.
 * By default the only person you can nominate is yourself or others who have
 * nominated themselves, this avoids the situation of someone being nominated
 * for a position they don't want to fulfill.
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @return A multi-dimensional array containing the positions and nominees for each position
 */
function get_nominees($mysqli_elections)
{
	/* An array mapping the nominated position to the nominees */
	$nominees = array(  'President'         => array(),
						'Vice President'    => array(),
						'Coordinator'       => array(),
						'Treasurer'         => array()
	);
	
	$current_year = date('Y');
	
	$members_table = "members_" . $current_year;
	$positions_nom_table = "positions_nom_" . $current_year;

	/* Get the nominees for each position */
	foreach ($nominees as $position => $nominee)
	{
		/* Get the nominees for the current position from the database */
		if ($stmt = $mysqli_elections->prepare("SELECT first_name, last_name
                                                    FROM ".$members_table." m INNER JOIN " . $positions_nom_table.
                                                         " p ON p.reference = m.access_account
                                                            WHERE p.position LIKE ?"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('s', $position);

			/* execute query */
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($first_name, $last_name);

			/* fetch each nominee for the position */
			while ($stmt->fetch())
			{
				/* TODO This is not valid if two people have the exact same name! */
				$nominees[$position][] = $first_name . ' ' . $last_name;
			}

			/* close statement */
			$stmt->close();
		}
	}

	return $nominees;
}


/**
 * A function which gets the candidates who are the individuals who won in the
 * nomination period.
 * TODO: Add support to handle the case where the incumbents are no longer attending UOIT
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @return A multi-dimensional array containing the positions and candidates for each position
 */
function get_candidates($mysqli_elections)
{
	/* An array mapping the positions to the candidate */
	$candidates = array('President'         => '',
						'Vice President'    => '',
						'Coordinator'       => '',
						'Treasurer'         => ''
	);

	$current_year = date('Y');
	
	$members_table = "members_" . $current_year;
	$winners_nom_table = "winners_nom_" . $current_year;
	
	/* Get the candidate for each position */
	foreach ($candidates as $position => $candidate)
	{
		/* Get the candidate for the current position from the database */
		if ($stmt = $mysqli_elections->prepare("SELECT first_name, last_name
                                                    FROM ".$members_table." m INNER JOIN ".$winners_nom_table.
                                                        " w ON w.reference = m.access_account
                                                            WHERE w.position LIKE ?"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('s', $position);

			/* execute query */
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($first_name, $last_name);

			$stmt->fetch();

			$candidates[$position][] = $first_name . ' ' . $last_name;

			/* close statement */
			$stmt->close();
		}
	}

	return $candidates;
}


/**
 * A function which gets the incumbents who are the individuals who won in the
 * election from the previous year.
 * TODO: Add support to handle the case where the incumbents are no longer attending UOIT
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @return A multi-dimensional array containing the positions and incumbents for each position
 */
function get_incumbents($mysqli_elections)
{
	/* An array mapping the positions to the incumbent */
	$incumbents = array('President'         => '',
						'Vice President'    => '',
						'Coordinator'       => '',
						'Treasurer'         => ''
	);

	$previous_year = (date('Y')-1);
	$current_year = date('Y');
	
	$members_table = "members_" . $current_year;
	
	/* The name of the table containing the previous years winnners */
	$incumbents_table = 'winners_elect_' . $previous_year;

	/* Get the incumbent for each position */
	foreach ($incumbents as $position => $incumbent)
	{
		/* Get the incumbent for the current position from the database */
		if ($stmt = $mysqli_elections->prepare("SELECT first_name, last_name
                                                    FROM ". $members_table." m INNER JOIN " . $incumbents_table . " w " .
														"ON w.reference = m.access_account
                                                            WHERE w.position LIKE ?"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('s', $position);

			/* execute query */
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($first_name, $last_name);

			$stmt->fetch();

			$incumbents[$position][] = $first_name . ' ' . $last_name;

			/* close statement */
			$stmt->close();
		}
	}

	return $incumbents;
}

/**
 * TODO test and make sure it actually works
 * TODO add a column for the user name in the members table
 * Used to check if a given candidate (with their first, last and user name) is a candidate
 * capable to be elected for the given position.
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param string $nominee the full name of the nominee
 * @param string $user_name the user name of a potential candidate
 * @param string $position the position that the candidate might hold
 * @return boolean $is_candidate TRUE if the candidate is found in the database with the given position
 */
function is_candidate($mysqli_elections, $nominee, $position)
{
	$is_candidate = FALSE;

	$current_year = date('Y');
	
	$members_table = "members_" . $current_year;
	$positions_elect_table = "positions_elect_" . $current_year;
		
	if ($stmt = $mysqli_elections->prepare("SELECT EXISTS(
                                                    SELECT 1 FROM ".$members_table." m INNER JOIN ".$positions_elect_table.
														" p ON p.reference = m.access_account WHERE 
															p.position LIKE ? AND CONCAT
                                                                (
                                                                    m.first_name,
                                                                    ' ',
                                                                    m.last_name
                                                                )
                                                                LIKE ?
														 )"))
	{
		/* bind parameters for markers */
		$stmt->bind_param('ss', $position, $nominee);

		/* execute query */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($is_candidate);

		/* fetch value */
		$stmt->fetch();

		/* close statement */
		$stmt->close();
	}
	return $is_candidate;
}

/**
 * TODO test and make sure it actually works
 * Used to check if a given nominee (with their first, last and user name) is a nominee
 * capable to be elected for the given position.
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param string $nominee the full name of the nominee
 * @param string $user_name the user name of a potential nominee
 * @param string $position the position that the nominee might hold
 * @return boolean $is_nominee TRUE if the nominee is found in the database with the given position
 */
function is_nominee($mysqli_elections, $nominee, $position)
{
	$is_nominee = FALSE;
	
	$current_year = date('Y');
	
	$members_table = "members_" . $current_year;
	$positions_nom_table = "positions_nom_" . $current_year;

	if ($stmt = $mysqli_elections->prepare("SELECT EXISTS(
													SELECT 1 FROM ".$members_table." m INNER JOIN ".$positions_nom_table.
														" p ON p.reference = m.access_account WHERE
															p.position LIKE ? AND CONCAT
	                                                                (
	                                                                    m.first_name,
	                                                                    ' ',
	                                                                    m.last_name
	                                                                )
	                                                                LIKE ?
															 )"))
	{
		/* bind parameters for markers */
		$stmt->bind_param('ss', $position, $nominee);

		/* execute query */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($is_nominee);

		/* fetch value */
		$stmt->fetch();

		/* close statement */
		$stmt->close();
	}
	return $is_nominee;
}

/**
 * A function which verifies if a user has already been nominated for a position
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param int $access_account The unique (primary key) access account number of the user
 * @param string $position The position that the user is submitting a vote for
 * @return boolean True if the user has already been nominated for the position
 */
function is_nominated($mysqli_elections, $access_account, $position)
{
	$is_nominated = TRUE;

	$current_year = date('Y');
	
	$positions_nom_table = "positions_nom_" . $current_year;
	
	/* Verify that the user has not already been nominated for that position */
	if ($stmt = $mysqli_elections->prepare("SELECT EXISTS(
                                                    SELECT 1 FROM ". $positions_nom_table.
														"WHERE reference=? AND position LIKE ?)"))
	{
		/* bind parameters for markers */
		$stmt->bind_param('is', $access_account, $position);

		/* execute query */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($is_nominated);

		/* fetch value */
		$stmt->fetch();

		/* close statement */
		$stmt->close();
	}

	return $is_nominated;
}

function has_voted($mysqli_elections, $access_account, $vote_type)
{
	/* Array of the positions */
	$positions = array( 'President',
						'Vice President',
						'Coordinator',
						'Treasurer' );
	
	$has_voted = TRUE;
	$voting_record_tbl = '';
	$current_year = date('Y');
	
	/* Set the table to check their voting record based on the type of vote being cast */
	if (strcasecmp($vote_type, 'nomination') === 0)
	{
		$voting_record_tbl = 'voters_nom_' . $current_year;
	}
	elseif (strcasecmp($vote_type, 'election') === 0)
	{
		$voting_record_tbl = 'voters_elect_' . $current_year;
	}
	
	/* Verify that the user has not already voted for that position */
	if ($stmt = $mysqli_elections->prepare("SELECT EXISTS(
														SELECT 1 FROM " . $voting_record_tbl .
														" WHERE reference=?)"))
	{
		/* bind parameters for markers */
		$stmt->bind_param('i', $access_account);
	
		/* execute query */
		$stmt->execute();
	
		/* bind result variables */
		$stmt->bind_result($has_voted);
	
		/* fetch value */
		$stmt->fetch();
	
		/* close statement */
		$stmt->close();
	}
	
	return $has_voted;
}


/**
 * A function which verifies if a user has already voted for a position
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param int $access_account The unique (primary key) access account number of the user
 * who is currently logged in
 * @param string $position The position that the user is submitting a vote for
 * @param string $vote_type The type of vote they are casting, a "nomination" or
 * "election" vote
 * @return boolean True if the user has already voted for the position
 */
function has_voted_position($mysqli_elections, $access_account, $position, $vote_type)
{
	$has_voted_position = TRUE;
	$voting_record_tbl = '';
	$current_year = date('Y');
	
	/* Set the table to check their voting record based on the type of vote being cast */
	if (strcasecmp($vote_type, 'nomination') === 0)
	{
		$voting_record_tbl = 'voters_nom_' . $current_year;
	}
	elseif (strcasecmp($vote_type, 'election') === 0)
	{
		$voting_record_tbl = 'voters_elect_' . $current_year;
	}

	 
	/* Verify that the user has not already voted for that position */
	if ($stmt = $mysqli_elections->prepare("SELECT EXISTS(
                                                    SELECT 1 FROM " . $voting_record_tbl .
														" WHERE reference=? AND position LIKE ?)"))
	{
		/* bind parameters for markers */
		$stmt->bind_param('is', $access_account, $position);

		/* execute query */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($has_voted_position);

		/* fetch value */
		$stmt->fetch();

		/* close statement */
		$stmt->close();
	}

	return $has_voted_position;
}


/**
 * A function which records the positions that a user has voted for, this is
 * a precaution to prevent double voting even in the event of users trying
 * to hack/subvert the system.
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param int $access_account The unique (primary key) access account number of the user
 * who is currently logged in
 * @param string $vote_type The type of vote they are casting, a "nomination" or
 * "election" vote
 * @param $position The position that the user has voted for
 */
function record_user_vote($mysqli_elections, $access_account, $position, $vote_type)
{
	$record_vote_tbl = '';
	$current_year = date('Y');

	/* Set the table to check their voting record based on the type of vote being cast */
	if (strcasecmp($vote_type, 'nomination') === 0)
	{
		$record_vote_tbl = 'voters_nom_' . $current_year;
	}
	elseif (strcasecmp($vote_type, 'election') === 0)
	{
		$record_vote_tbl = 'voters_elect_' . $current_year;
	}

	if ($stmt = $mysqli_elections->prepare("INSERT INTO " . $record_vote_tbl .
			" VALUES (?, ?)"))
	{
		/* bind parameters for markers */
		$stmt->bind_param('is', $access_account, $position);

		/* execute query */
		$stmt->execute();

		/* close statement */
		$stmt->close();
	}
}


/**
 * A function which handles a list of positions that a user is nominating themselves
 * for and adds them to the list of nominees so that other users can vote for them.
 * This is a precaution so that other users cannot nominate someone for a position
 * they do not want to be in.
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param int $access_account The unique (primary key) access account number of the user
 * who is currently logged in and nominating themself
 * @param $positions An array of the positions the user is nominating themselves for
 */
function nominate_self($mysqli_elections, $access_account, $positions)
{
	$current_year = date('Y');
	$positions_nom_table = "positions_nom_" . $current_year;
	
	foreach ($positions as $position)
	{
		/* Determine that the user has not already been nominated for the position */
		if (!is_nominated($mysqli_elections, $access_account, $position))
		{
			if ($stmt = $mysqli_elections->prepare("INSERT INTO ".$positions_nom_table.
														"VALUES (?, 0, ?)"))
			{
				/* bind parameters for markers */
				$stmt->bind_param('is', $access_account, $position);
					
				/* execute query */
				$stmt->execute();
					
				/* close statement */
				$stmt->close();
			}
		}
	}
}


/**
 * A function which takes a list of the positions and nominee that the member
 * voted for and adds a vote to that nominee if the user has not already cast
 * a vote for that position.
 *
 * TODO Perhaps add some kind of AJAX support or page rendering support to
 * remove positions they have already voted for. This covers the case where
 * a user votes for maybe only one position and then returns to vote again.
 *
 * TODO Perhaps add some kind of support where a user can change their vote,
 * although most electoral systems don't have this...
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param int $access_account The unique (primary key) access account number of the user
 * who is currently logged in
 * @param $positions An array of the positions and the full name of the nominee they voted for
 */
function nomination_vote($mysqli_elections, $access_account, $positions)
{
	$current_year = date('Y');
	$members_table = "members_" . $current_year;
	$positions_nom_table = "positions_nom_" . $current_year;
	
	/* Get the nominee they voted for, for each position */
	foreach ($positions as $position => $nominee)
	{
		/* Verify that the user has not already voted for that position */
		if (! has_voted_position($mysqli_elections, $access_account, $position, 'nomination'))
		{
			/* Increment by 1 the votes for the nominee of the position they voted for */
			if ($stmt = $mysqli_elections->prepare("UPDATE ".$positions_nom_table." p INNER JOIN ".$members_table.
                                                        " m ON p.reference = m.access_account
                                                            SET p.votes = p.votes + 1
                                                                WHERE p.position LIKE ? AND
                                                                CONCAT
                                                                (
                                                                    m.first_name,
                                                                    ' ',
                                                                    m.last_name
                                                                )
                                                                LIKE ?"))
			{
				/* bind parameters for markers */
				$stmt->bind_param('ss', $position, $nominee);

				/* execute query */
				$stmt->execute();

				/* close statement */
				$stmt->close();

				/* Record the position that the user voted for, precaution against double voting */
				record_user_vote($mysqli_elections, $access_account, $position, 'nomination');
			}
		}
	}
}


/**
 * A function for the final election voting, which takes a list of the positions
 * and nominee that the member voted for and adds a vote to that nominee if the
 * user has not already cast a vote for that position.
 *
 * TODO Add support to handle initially populating the positions_elect table using
 * a PHP daemon (System_Daemon) instead of EACH TIME the function is called
 * with the candidates and incumbents with a starting value of 0 votes
 *
 * TODO This method along with the nomination_vote method can probably just
 * be combined into one method "record_user_vote"
 *
 * TODO Perhaps add some kind of AJAX support or page rendering support to
 * remove positions they have already voted for. This covers the case where
 * a user votes for maybe only one position and then returns to vote again.
 *
 * TODO Perhaps add some kind of support where a user can change their vote,
 * although most electoral systems don't have this...
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param int $access_account The unique (primary key) access account number of the user
 * who is currently logged in
 * @param $positions An array of the positions and the full name of the nominee they voted for
 */
function election_vote($mysqli_elections, $access_account, $positions)
{
	$current_year = date('Y');
	$members_table = "members_" . $current_year;
	$positions_elect_table = "positions_elect_" . $current_year;
	
	/* Get the nominee they voted for, for each position */
	foreach ($positions as $position => $nominee)
	{
		/* Verify that the user has not already voted for that position */
		if (! has_voted_position($mysqli_elections, $access_account, $position, 'election'))
		{
			/* Increment by 1 the votes for the nominee of the position they voted for */
			if ($stmt = $mysqli_elections->prepare("UPDATE ".$positions_elect_table." p INNER JOIN "
                                                        .$members_table." m ON p.reference = m.access_account
                                                            SET p.votes = p.votes + 1
                                                                WHERE p.position LIKE ? AND
                                                                CONCAT
                                                                (
                                                                    m.first_name,
                                                                    ' ',
                                                                    m.last_name
                                                                )
                                                                LIKE ?"))
			{
				/* bind parameters for markers */
				$stmt->bind_param('ss', $position, $nominee);

				/* execute query */
				$stmt->execute();

				/* close statement */
				$stmt->close();

				/* Record the position that the user voted for, precaution against double voting */
				record_user_vote($mysqli_elections, $access_account, $position, 'election');
			}
		}
	}
}


/**
 * A function which records the winners of a nomination/election in the database
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param array $positions An array of the positions mapping to the access_account that won in the position
 * of the winning candidate for the position
 * @param string $vote_type The type of vote they are casting, a "nomination" or
 * "election" vote
 */
function record_winners($mysqli_elections, $positions, $vote_type)
{
	$winners_tbl = '';
	$current_year = date('Y');
	
	/* Set the table to check their voting record based on the type of vote being cast */
	if (strcasecmp($vote_type, 'nomination') === 0)
	{
		$winners_tbl = 'winners_nom_' . $current_year;
	}
	elseif (strcasecmp($vote_type, 'election') === 0)
	{
		$winners_tbl = 'winners_elect_' . $current_year;
	}

	foreach($positions as $position => $access_account)
	{
		if ($stmt = $mysqli_elections->prepare("INSERT INTO "
													. $winners_tbl .
													" VALUES (?, ?)"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('is', $access_account, $position);

			/* execute query */
			$stmt->execute();

			/* close statement */
			$stmt->close();
		}
	}
}


/**
 * A function which determines the winners of the election by comparing the number
 * of votes that the candidate and incumbent each have.
 *
 * In the event of a tie between the candidate and incumbent for a position then
 * the winning person is selected by a Pseudo Random Number Generator (PRNG) MOD 2
 * where 0 is candidate and 1 is the incumbent
 *
 * TODO This is a method, along with the determine_candidate method that should be
 * executed by the system_daemon
 *
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @param string $election_type The type of election to determine the winners for,
 * a "nomination" or "election"
 */
function determine_winners($mysqli_elections, $election_type)
{
	/* Array of the positions */
	$positions = array( 'President',
						'Vice President',
						'Coordinator',
						'Treasurer' );

	$positions_tbl = '';	
	$current_year = date('Y');
	$members_table = "members_" . $current_year;
	
	/* Set the table to tally the votes from to determine the winner */
	if (strcasecmp($election_type, 'nomination') === 0)
	{
		$positions_tbl = 'positions_nom_' . $current_year;
	}
	elseif (strcasecmp($election_type, 'election') === 0)
	{
		$positions_tbl = 'positions_elect_' . $current_year;
	}

	/* Array of winning nominees by access_account and # of votes, re-initialized for each position,
	 * there should only be one potential winner but may have several in the event of a tie, in which
	* case there will need to be tie-breaker
	*/
	$nominees = array();

	/* Array of winners of the election by position and the access account of the winner */
	$winners = array();

	/* For each position tally the votes and record the winner */
	foreach($positions as $position)
	{
		/* Re-initialize the winning nominees list */
		$nominees = array();

		if ($stmt = $mysqli_elections->prepare("SELECT access_account
        											FROM ".$members_table." m INNER JOIN " .$positions_tbl. 
        												" p ON p.reference = m.access_account
        													WHERE p.position LIKE ?
        														AND p.votes = (
        																		SELECT MAX(votes) FROM " .$positions_tbl. 
        																			" WHERE position LIKE ?
        																		)"))
		{
			/* bind parameters for markers */
			$stmt->bind_param('ss', $position, $position);

			/* execute query */
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($access_account);

			/* The first result is the winner (most votes), but there could
			 * also be others who are tied for first, every nominee in first
			* place is added to the winning nominees list
			*/
			while ($stmt->fetch())
			{
				$nominees[] = $access_account;
			}

			/* If there is a tie for the position determine the winner using
			 * a tie-breaker which is a PRNG MOD <# winning nominees>
			*/
			if (count($nominees) > 1)
			{
				$winners[$position] = tie_breaker($nominees);
			}
			else
			{
				$winners[$position] = $access_account;
			}

			/* close statement */
			$stmt->close();
		}
	}

	/* Finally, record the winners in election DB */
	record_winners($mysqli_elections, $winners, $election_type);
}


/**
 * A function which gets the top three nominees for the candidate positions, the function
 * returns an array of the positions and the first, second, and third place nominees
 * as well as the number of votes they received.
 *
 * TODO Finish the get_top_three_candidates method and add support to also get the
 * top three winners of the election, this can be done in one method
 * 
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @return A multi-dimensional array of the candidate positions and the first, second,
 * and third place nominees and the number of votes they received.
 */
function get_top_three_candidates($mysqli_elections)
{
	/* An array mapping the nominated position to the nominees */
	$candidates = array('President'         => array(),
						'Vice President'    => array(),
						'Coordinator'       => array(),
						'Treasurer'         => array()
	);
}
?>