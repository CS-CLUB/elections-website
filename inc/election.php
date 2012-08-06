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
 * Contains a collection of the functions that relate to handling the election
 * such as determining the current election event status, handling election voting,
 * and generating the statistics/results of the election.
 */
require_once 'utility.php';


/**
 * A function which determines if it is currently the nomination period of the election
 * 
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @return boolean True if it is currently the nomination period of the election
 */
function is_nomination($mysqli_elections)
{
	$cur_date = DateTime::createFromFormat('m-d-H-i', date('m-d-H-i'));
	
	/* September 1, 12:00 am */
	$nomination_start = DateTime::createFromFormat('m-d-H-i', '09-01-00-00');
	
	/* September 14th (11:59pm) */
	$nomination_end = DateTime::createFromFormat('m-d-H-i', '09-14-23-59');
	
	/* 
	 * If the current date is within the nomination period, and the election tables exist
	 * then it is the nomination period
	 */
	if ($cur_date > $nomination_start && $cur_date < $nomination_end 
		&& election_tables_exist($mysqli_elections))
	{
		return true;
	}
	
	return false;
}


/**
 * A function which determines if the nomination period of the election is closed,
 * some years this many not apply as the election period will open within 1 minute 
 * of the nomination period closing!
 * 
 * @param mysqli $mysqli_elections The mysqli connection object for the ucsc elections DB
 * @return boolean True if the nomination period of the election is closed
*/
function is_nomination_closed($mysqli_elections)
{
	$cur_date = DateTime::createFromFormat('Y-m-d-H-i', date('Y-m-d-H-i'));
	
	/* September 14th (11:59pm) */
	$nomination_end = DateTime::createFromFormat('Y-m-d-H-i', date('Y') . '-09-14-23-59'); 
	
	/* Election start day, first weekday after September 14 at 11:59pm */
	$election_start = DateTime::createFromFormat('Y-m-d-H-i', get_next_weekday($nomination_end) . '-00-00');

	/*
	 * If the current date is after the nomination period, before the election period,
	 * and the election tables exist then the nomination period is closed
	*/
	if ($cur_date > $nomination_end && $cur_date < $election_start
		&& election_tables_exist($mysqli_elections))
	{
		return true;
	}
	
	return false;
}


?>