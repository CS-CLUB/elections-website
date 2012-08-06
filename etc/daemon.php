#!/usr/bin/php -q
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
 * The elections website daemon, this daemon runs in the background and creates
 * the appropriate tables at the start of each election period (September 1st).
 * On September 14 it closes the nomination period and determines the winners, then
 * on the first school day afte September 14 the election is held, at which point at
 * the end of the day (11:59pm) the election is closed and the results of the
 * election are determined.
 * 
 * Why is this a daemon and not part of the main website? It makes more sense for
 * this to operate independent of user interactions with the website.
 */
error_reporting(E_ALL);
require_once 'System/Daemon.php';
require_once 'db_interface.php';
require_once 'election_auth.php';
require_once 'utility.php';


/* Arguments supported by Daemon, currently only option is to create init scripts */
$runmode = array(
		'help' => false,
		'write-initd' => false
);

/* Scan command line attributes for allowed arguments */
foreach ($argv as $k=>$arg)
{
	if (substr($arg, 0, 2) == '--' && isset($runmode[substr($arg, 2)]))
	{
		$runmode[substr($arg, 2)] = true;
	}
}

/* Specify the options for the daemon */
$options = array(
		'appName' => 'election_daemon',
		'appDir' => dirname(__FILE__),
		'appDescription' => 'Elections Daemon, manages the elections',
		'authorName' => 'GNU USER',
		'authorEmail' => 'gsoc.student@gmail.com',
		'sysMaxExecutionTime' => '0',
		'sysMaxInputTime' => '0',
		'sysMemoryLimit' => '32M'
);

System_Daemon::setOptions($options);


/* Write start/stop scripts to manage the Daemon if option specified by the user */
if ($runmode['write-initd'])
{
	System_Daemon::writeAutoRun();
}


/* Spawn the Daemon */
System_Daemon::start();
System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . "Daemon: " . 
	System_Daemon::getOption("appName"). " spawned! Daemon logs will be written to: " 
	. System_Daemon::getOption("logLocation"));



/* Connect to the databases */
$mysqli_accounts = new mysqli("localhost", $db_user, $db_pass, $db_acc_name);
$mysqli_elections = new mysqli("localhost", $db_user, $db_pass, $db_elec_name);

/* check connection */
if (mysqli_connect_errno()) {
	System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . " Failed to connect to the database: " . 
		mysqli_connect_error());
	exit();
}


/** 
 * Run in the background infinitely and do the following election operations
 * 
 *	1. At the start of a new year on September 1st (12:00 am) intiate a new
 *     election by creating new election tables and initiate the nomination period
 *
 *	2. At the end of the nomination period September 14th (11:59pm) close the
 *	   nomination period and tally the votes to determine the candidates
 *
 *	3. On the first day after September 14th that lands on a weekday open
 *	   the election period for a full 24-hours (12:00am - 11:59pm)
 *
 *	4. At the end of the 24 hour election period (11:59pm) close the election 
 *	   and determine the winners of the election	 	
 */
$next_weekday = '';

while (true)
{
	/* September 1, 12:00 am */
	if (strcmp(date('m-d-H-i'), '09-01-00-00') === 0)
	{
		/* Create the tables for the start of a new election year */
		new_election_tables($mysqli_elections);
		
		System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . " Initiated a new election and created " . 
				"the tables for the start of the " . date('Y') . " election");
		
		/* Instead of constantly iterating, can sleep for the next 14 days */
		System_Daemon::iterate(60*60*24*14);
	}
	
	/* September 14th (11:59pm) */
	if (strcmp(date('m-d-H-i'), '09-14-23-59') === 0)
	{
		/* Close the nomination period and tally the votes to determine the candidates 
		 * and then store the results in the DB 
		 */
		determine_winners($mysqli_elections, "nomination");
		
		System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . " Closed the nomination period " .
				"and recorded the candidates in the database for the " . date('Y') . " election");
		
		/* Determine the next week day after September 14th to host the election */
		$next_weekday = get_next_weekday(DateTime::createFromFormat('Y-m-d-H-i', date('Y-m-d-H-i')));
		
		System_Daemon::iterate(60);
	}
	
	
	/* First weekday after September 14 start election period for 24-hours (12:00am - 11:59pm) */
	if (strcmp(date('Y-m-d-H-i'), $next_weekday . '-00-00') === 0)
	{
		/* Initiate the final election, populate the table with the candidates and incumbents */
		pop_election_table($mysqli_elections);
		
		System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . " Initiated the election period, " .
				"with the candidates and incumbents for the " . date('Y') . " election");
		
		/* Sleep until the end of the 24 hour election period */
		System_Daemon::iterate((60*60*24) - 120);
		
	}
	
	/* End of the 24 hour election period on the first week day after September 14 */
	if (strcmp(date('Y-m-d-H-i'), $next_weekday . '-23-59') === 0)
	{
		/* Close the election period and tally the votes to determine the winners
		 * of the election and then store the results in the DB
		 */
		determine_winners($mysqli_elections, "election");
		
		System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . " Closed the election period " .
				"and recorded the election winners in the database for the " . date('Y') . " election");
		
		/* Instead of constantly iterating, can sleep until the next election */
		System_Daemon::iterate(60*60*24*345);
	}
	
	System_Daemon::iterate(60);
}


System_Daemon::stop();
?>