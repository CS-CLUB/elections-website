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

require_once 'utility.php';

date_default_timezone_set('America/Toronto');

/* Start of nomination period, usually Sept. 1, 12:00 am */
$nomination_start_date = '09-01-00-00';

/* The end of the nomination period, usually Sept. 14, 11:59pm */
$nomination_end_date = '09-14-23-59';

/* Election start day, first weekday after nomination end date at 12:00am */
$election_start_date =  get_next_weekday(DateTime::createFromFormat('Y-m-d-H-i', date('Y').'-'.$nomination_end_date)) . '-00-00';

/* Election ends on the same day as the election start at 11:59pm */
$election_end_date = DateTime::createFromFormat('Y-m-d-H-i', $election_start_date)->format('Y-m-d') . '-23-59';

?>