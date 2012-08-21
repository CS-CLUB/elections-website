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

require_once 'utility.php';

/* September 1, 12:00 am */
$nomination_start_date = '09-01-00-00';

/* September 14th (11:59pm) */
$nomination_end_date = '09-14-23-59';

/* Election start day, first weekday after September 14, at 12:00am */
$election_start_date =  get_next_weekday($nomination_end_date) . '-00-00';

/* End of the first week day (11:59pm) after September 14th */
$election_end_date = $election_start_date->format('Y-m-d') . '-23-59';
