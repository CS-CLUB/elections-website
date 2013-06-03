#!/usr/bin/php -q
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
require_once '../inc/db_interface.php';
require_once '../inc/utility.php';
require '../inc/election_auth.php';
require '../inc/election_date.php';


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

if ($runmode['help'] == true)
{
	echo 'Usage: '.$argv[0].' [runmode]' . "\n";
	echo 'Available runmodes:' . "\n";
	foreach ($runmode as $runmod=>$val)
	{
		echo ' --'.$runmod . "\n";
	}
	die();
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
	if (($initd_location = System_Daemon::writeAutoRun()) === false)
	{
		System_Daemon::notice('unable to write init.d script');
	}
	else
	{
		System_Daemon::info('sucessfully written startup script: %s',$initd_location);
	}
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
 *	1. At the start of a new election period initiate a new election by creating 
 *     new election tables and initiate the nomination period
 *
 *	2. At the end of the nomination period close the nomination period and tally 
 *	   the votes to determine the candidates
 *
 *	3. On the first day after end of the nomination period that lands on a weekday open
 *	   the election period for a full 24-hours (12:00am - 11:59pm)
 *
 *	4. At the end of the 24 hour election period (11:59pm) close the election 
 *	   and determine the winners of the election	 	
 */

while (true)
{
	/* The start of the nomination period */
	if (strcmp(date('m-d-H-i'), $nomination_start_date) === 0)
	{
		/* Create the tables for the start of a new election year */
		new_election_tables($mysqli_elections);
		
		System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . " Initiated a new election and created " . 
				"the tables for the start of the " . date('Y') . " election");
	}
	
	/* End of the nomination period */
	if (strcmp(date('m-d-H-i'), $nomination_end_date) === 0)
	{
		/* Close the nomination period and tally the votes to determine the candidates 
		 * and then store the results in the DB 
		 */
		determine_winners($mysqli_elections, "nomination");
		
		System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . " Closed the nomination period " .
				"and recorded the candidates in the database for the " . date('Y') . " election");
	}
	
	/* First weekday after the nomination period ends start election period for 24-hours (12:00am - 11:59pm) */
	if (strcmp(date('Y-m-d-H-i'), $election_start_date) === 0)
	{
		/* Initiate the final election, populate the table with the candidates and incumbents */
		pop_election_table($mysqli_elections);
		
		System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . " Initiated the election period, " .
				"with the candidates and incumbents for the " . date('Y') . " election");
	}
	
	/* End of the 24 hour election period */
	if (strcmp(date('Y-m-d-H-i'), $election_end_date) === 0)
	{
		/* Close the election period and tally the votes to determine the winners
		 * of the election and then store the results in the DB
		 */
		determine_winners($mysqli_elections, "election");
		
		System_Daemon::log(System_Daemon::LOG_INFO, date('Y-m-d H:i:s') . " Closed the election period " .
				"and recorded the election winners in the database for the " . date('Y') . " election");
	}
	
	System_Daemon::iterate(60);
}


System_Daemon::stop();
?>