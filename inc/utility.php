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
 * Contains an aggregate collection of miscellaneous utility functions, these
 * are functions for things such as breaking a tie or calculating the the
 * next date that is a day of the week.
 */


/**
 * A function which detemines the winner of an election in the case of a tie by using
 * a Pseudo Random Number Generator (PRNG) MOD <# of tied nominees> where 0 is first
 * nominee 1 is second nominee and so on for each nominee tied for the position
 * @package utility
 * 
 * @param array $nominees An array of the nominees tied for the position
 * @return The winner by random selection using a PRNG
 */
function tie_breaker($nominees)
{
	/* Get the winner using a PRNG mod <# nominees> */
	$rounds = rand(0, 100);
	for ($i = 0; $i < $rounds; $i++)
	{
		mt_srand(rand());
		mt_rand();
	}

	$idx_winner = mt_rand() % count($nominees);
	 
	return $nominees[$idx_winner];
}


/**
 * A function which determines the next available date that falls on a weekday when
 * executed. For example if the current date was a Friday then the next available
 * date would be the Monday.
 *
 * TODO Fix this to handle scenarios where the next day of the week occurs on the next month
 * 
 * @package utility
 * 
 * @param DateTime $date The date you want to determine the next weekday for, formatted as 'Y-m-d-H-i'
 * @return string The next available date that falls on a weekday in the format of year-month-day
 * (ie. 2012-09-15) for September 15, 2012.
 */
function get_next_weekday($date)
{
	$cur_date = DateTime::createFromFormat('Y-m-d', $date);
	
	$cur_day_of_week = $date->format('l');
	$next_day_of_week = $date;

	if (strcasecmp($cur_day_of_week, 'Friday') === 0)
	{
		$next_day_of_week->add(new DateInterval('P3D'));
	}
	elseif (strcasecmp($cur_day_of_week, 'Saturday') === 0)
	{
		$next_day_of_week->add(new DateInterval('P2D'));
	}
	else
	{
		$next_day_of_week->add(new DateInterval('P1D'));
	}

	return $next_day_of_week->format('Y-m-d');
}
?>