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


/*
 * The template for the elections website header when a member has logged into the 
 * website, this displays the main user menu and logout option
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>UCSC Elections Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UOIT/DC Computer Science Club Elections">
    <meta name="author" content="UOIT/DC Computer Science Club">

    <!-- CSS styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/sparkbox-select.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="#">
            UCSC Elections Website
          </a>
          <div class="nav-collapse">
          <ul class="nav pull-right">
	          <li class="dropdown">
	              <a data-toggle="dropdown" class="dropdown-toggle" href="#">
	              	<i class="icon-user icon-white"></i> Bob Cajun<b class="caret"></b>
              	</a>
	              <ul class="dropdown-menu">
	                <li><a href="#">Edit Profile</a></li>
	                <li><a href="#">Vote</a></li>
	                <li><a href="#">View Election Results</a></li>
	                <li class="divider"></li>
	                <li>
	                	<form action="index.php" class="form-horizontal logout-form" method="post" accept-charset="UTF-8">
	                		<input id="signout_button" class="btn btn-primary" class="signout_button" type="submit" name="signout" value="Sign Out" />
                		</form>
                	</li>
	              </ul>
	            </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="container">