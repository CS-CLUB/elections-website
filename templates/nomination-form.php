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


/*
 * The nomination period form, displays a form with the list of nominees for each
 * position.
 * 
 * 
 * DEPENDENCIES
 * ------------
 * 
 * Depends on the $_SESSION varialbe being set for the first and last name to set
 * the default nominee as the current user so they can nominate themselves
 * 
 * $_SESSION['first_name']
 * $_SESSION['last_name']
 * 
 * 
 * This template depends on a multi-dimensional array $nominees containing the positions
 * and an array of the nominees for each position
 * 
 * $nominees = array(  	'President'         => array(),
 *						'Vice President'    => array(),
 *						'Coordinator'       => array(),
 *						'Treasurer'         => array()
 *					)
 */
require_once 'inc/db_interface.php';
require_once 'inc/election_auth.php';
$mysqli_elections = new mysqli("localhost", $db_user, $db_pass, $db_elec_name);
$pres_disabled='';
$vice_disabled='';
$coor_disabled='';
$trea_disabled='';
$NONE = 'None';
$ALREADY_VOTED = 'Already Voted';
$disable=FALSE;

if(isset($_SESSION['access_account']) && has_voted_pos($mysqli_elections, $_SESSION['access_account'], 'President'))
//if($disable)
{
	$pres_disabled='disabled';
}

if(isset($_SESSION['access_account']) && has_voted_pos($mysqli_elections, $_SESSION['access_account'], 'Vice President'))
//if($disable)
{
	$vice_disabled='disabled';
}

if(isset($_SESSION['access_account']) && has_voted_pos($mysqli_elections, $_SESSION['access_account'], 'Coordinator'))
//if($disable)
{
	$coor_disabled='disabled';
}

if(isset($_SESSION['access_account']) && has_voted_pos($mysqli_elections, $_SESSION['access_account'], 'Treasurer'))
//if($disable)
{
	$trea_disabled='disabled';
}



?>
<section id="nomination-form">
	<div class="page-header">
		<h1>Nomination Period Voting</h1>
	</div>
	<?php include 'templates/nomination-notice.php'; ?>
	<br />
	<div class="row">
		<div class="span8">
			<form class="well form-horizontal" action="index.php" method="post" accept-charset="UTF-8">
				<fieldset>
					<div class="control-group">
			      		<label for="president_nom" class="control-label">President</label>
				      	<div class="controls">
				      		<select id="president_nom" name="president_nom" class="input-xlarge" 
				      		<?php if($pres_disabled === 'disabled') echo 'disabled="' . $pres_disabled . '"' ?>>
				      		
				      		<?php
				      				
				      							      				
				      				if ($pres_disabled === '')
				      				{
				      					echo '<option> ' . $NONE . ' </option>';
				      					echo '<option>' . $_SESSION['first_name'].' '.$_SESSION['last_name'] . '</option>';
					      				foreach ($nominees['President'] as $nominee)
					      				{
					      					echo '<option>' . $nominee . '</option>';
					      				}
				      				}
				      				else 
				      				{
				      					echo '<option> ' . $ALREADY_VOTED . ' </option>';
				      				}
				      			?>
				      		</select>
				      	</div>
		      		</div>
		      		<div class="control-group">
			      		<label for="vicepresident_nom" class="control-label">Vice President</label>
				      	<div class="controls">
				      		<select id="vicepresident_nom" name="vicepresident_nom" class="input-xlarge"
				      		<?php if($vice_disabled === 'disabled') echo 'disabled="' . $vice_disabled . '"' ?>>
				      			<?php

				      				
				      				if ($vice_disabled === '')
				      				{
				      					echo '<option> ' . $NONE . ' </option>';
				      					echo '<option>' . $_SESSION['first_name'].' '.$_SESSION['last_name'] . '</option>';
				      					foreach ($nominees['Vice President'] as $nominee)
					      				{
					      					echo '<option>' . $nominee . '</option>';
					      				}
				      				}
				      				else 
				      				{
				      					echo '<option> ' . $ALREADY_VOTED . ' </option>';
				      				}
				      			?>
				      		</select>
				      	</div>
		      		</div>
		      		<div class="control-group">
			      		<label for="coordinator_nom" class="control-label">Coordinator</label>
				      	<div class="controls">
				      		<select id="coordinator_nom" name="coordinator_nom" class="input-xlarge"
				      		<?php if($coor_disabled === 'disabled') echo 'disabled="' . $coor_disabled . '"' ?>>
				      			<?php
				      							      				
				      				if ($coor_disabled === '')
				      				{
				      					echo '<option> ' . $NONE . ' </option>';
					      				echo '<option>' . $_SESSION['first_name'].' '.$_SESSION['last_name'] . '</option>';
					      				foreach ($nominees['Coordinator'] as $nominee)
					      				{
					      					echo '<option>' . $nominee . '</option>';
					      				}
				      				}
				      				else 
				      				{
				      					echo '<option> ' . $ALREADY_VOTED . ' </option>';
				      				}
				      			?>
				      		</select>
				      	</div>
		      		</div>
      				<div class="control-group">
			      		<label for="treasurer_nom" class="control-label">Treasurer</label>
				      	<div class="controls">
				      		<select id="treasurer_nom" name="treasurer_nom" class="input-xlarge"
				      		<?php if($trea_disabled === 'disabled') echo 'disabled="' . $trea_disabled . '"' ?>>
				      			<?php
				      				
				      				if ($trea_disabled === '')
				      				{
				      					echo '<option> ' . $NONE . ' </option>';
				      					echo '<option>' . $_SESSION['first_name'].' '.$_SESSION['last_name'] . '</option>';
				      					foreach ($nominees['Treasurer'] as $nominee)
				      					{
				      						echo '<option>' . $nominee . '</option>';
				      					}
				      				}
				      				else
				      				{
				      					echo '<option> ' . $ALREADY_VOTED . ' </option>';
				      				}
				      			?>
				      		</select>
				      	</div>
		      		</div>
		      		<div class="form-actions">
		            	<button class="btn btn-primary" type="submit" name="nomination_vote" value="Submit Vote">Submit Vote</button>
		          </div>
				</fieldset>
			</form>
		</div>
	</div>
</section>