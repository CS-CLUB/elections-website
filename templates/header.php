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
 * The default template for the elections website header, this is for when a user
 * is not logged in and displays a login menu
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
              <li class="divider-vertical"></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  Sign in
                  <b class="caret"></b>
                </a>
                <div class="dropdown-menu login-dropdown">
                    <form class="form-horizontal" action="election.php" method="post" accept-charset="UTF-8">
                    <input id="login_username" class="input-large login-form" required type="text" maxlength="31" pattern="^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$" name="login_username" placeholder="Username" />
                    <input id="login_password" class="input-large login-form" required type="password" maxlength="31" pattern="^[a-zA-Z0-9\!\$\%\^\&amp;\*\(\)\_\?]{6,31}$" name="login_password" placeholder="Password"/>
                    <input id="login_remember" class="login-checkbox" type="checkbox" name="login_remember" checked="checked" value="1" />
                    <label class="string optional" for="login_remember">Remember me</label>
                    <input id="login_button" class="btn btn-primary" class="login-button" type="submit" name="signin" value="Sign In" />
                  </form>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="container">