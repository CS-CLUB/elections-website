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
 * Contains an aggregate collection of miscellaneous utility functions, these
 * are functions for things such as breaking a tie or calculating the the
 * next date that is a day of the week.
 */


/**
 * A function which detemines the winner of an election in the case of a tie by using
 * a Pseudo Random Number Generator (PRNG) MOD <# of tied nominees> where 0 is first
 * nominee 1 is second nominee and so on for each nominee tied for the position
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
 * @return string The next available date that falls on a weekday in the format of month-day
 * (ie. 09-15) for September 15.
 */
function get_next_weekday()
{
	$cur_day_of_week = date('l');
	$next_day_of_week = date('m') . '-';

	if (strcasecmp($cur_day_of_week, 'Friday') === 0)
	{
		$next_day_of_week .= (date('d') + 3);
	}
	elseif (strcasecmp($cur_day_of_week, 'Saturday') === 0)
	{
		$next_day_of_week .= (date('d') + 2);
	}
	else
	{
		$next_day_of_week .= (date('d') + 1);
	}

	return $next_day_of_week;
}
?>