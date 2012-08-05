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
 * The following is a configuration file containing all of the election website
 * authentication information such as the database access and database names
 * as well as the AES encrypt/decrypt keys.
 * 
 * Basically, anything you wouldn't want everyone else to view and have access
 * to goes in this file, as it is not part of the open source code that is
 * publicly accessible.
 */


/* Database access */
$db_user = '';
$db_pass = '';
$db_acc_name = '';
$db_elec_name = '';

/* AES ENCRYPT/DECRYPT KEY */
$AES_KEY = '';

/* SESSION ENCRYPT/DECRYPT KEY */
$SESSION_KEY = '';