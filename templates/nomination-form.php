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
 * The nomination period form, displays a form with the list of nominees for each
 * position.
 * 
 * 
 * DEPENDENCIES
 * ------------
 * 
 * This template depends on a multi-dimensional array $nominees containing the positions
 * and an array of the nominees for each position
 * 
 * $nominees = array(  	'President'         => array(),
 *						'Vice President'    => array(),
 *						'Coordinator'       => array(),
 *						'Treasurer'         => array()
 *					)
 * 
 */
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
				      		<select id="president_nom" class="input-xlarge">
				      			<?php 
				      				foreach ($nominees['President'] as $nominee)
				      				{
				      					echo '<option>' . $nominee . '</option>';
				      				}
				      			?>
				      		</select>
				      	</div>
		      		</div>
		      		<div class="control-group">
			      		<label for="vicepresident_nom" class="control-label">Vice President</label>
				      	<div class="controls">
				      		<select id="vicepresident_nom" class="input-xlarge">
				      			<?php 
				      				foreach ($nominees['Vice President'] as $nominee)
				      				{
				      					echo '<option>' . $nominee . '</option>';
				      				}
				      			?>
				      		</select>
				      	</div>
		      		</div>
		      		<div class="control-group">
			      		<label for="coordinator_nom" class="control-label">Coordinator</label>
				      	<div class="controls">
				      		<select id="coordinator_nom" class="input-xlarge">
				      			<?php 
				      				foreach ($nominees['Coordinator'] as $nominee)
				      				{
				      					echo '<option>' . $nominee . '</option>';
				      				}
				      			?>
				      		</select>
				      	</div>
		      		</div>
      				<div class="control-group">
			      		<label for="treasurer_nom" class="control-label">Treasurer</label>
				      	<div class="controls">
				      		<select id="treasurer_nom" class="input-xlarge">
				      			<?php 
				      				foreach ($nominees['Treasurer'] as $nominee)
				      				{
				      					echo '<option>' . $nominee . '</option>';
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