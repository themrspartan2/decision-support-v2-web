@extends('layouts.master')

@section("title")
Groups
@endsection

@section("pageJS")
<script src="Groups.js?rev=1"></script>
@endsection

@section("content")
<div class="center">
		<button type="button" id="createGroups" class="btn btn-primary">Create New Groups</button>
	</div>

	<div id="groupCreation" class="modal">
		<div class="modalContent">
			<div class="modalHeader">
				<span class="close">&times;</span>
				<h2>Group Options</h2>
			</div>
			<div class="modalBody">
				<form action="/action_page.php">
					<div class="form-group">
					  <label for="algorithmType">Create By:</label>
					  <div class="dropdown">
						<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">Dropdown Example
						<span class="caret"></span></button>
						<ul class="dropdown-menu">
						  <li><a href="#">HTML</a></li>
						  <li><a href="#">CSS</a></li>
						  <li><a href="#">JavaScript</a></li>
						</ul>
					  </div> 
					</div>
					<div class="form-group">
					  <label for="numGroups"># of groups</label>
					  <input type="number" class="form-control" id="numGroups" min="2" value="2">
					</div>
					<div class="checkbox">
					  <label><input type="checkbox"> Randomize order</label>
					</div>
					<button type="submit" class="btn btn-primary">Create groups</button>
				  </form> 
			</div>
			<div class="modalFooter">

			</div>
			
    		
		</div>
	</div>

	<table align="center" class="betterTable">
		<thead>
			<tr>
				<th>Group #</th><th>Students</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>1</td>
				<td>
					<div class="groupBox">
						<div draggable="true" class="studentBox fullBox">Jorgantha</div>
						<div draggable="true" class="studentBox fullBox">Kranoss</div>
						<div class="studentBox emptyBox"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td>2</td>
				<td>
					<div class="groupBox">
						<div draggable="true" class="studentBox fullBox">Calfin</div>
						<div draggable="true" class="studentBox fullBox">C'more</div>
						<div class="studentBox emptyBox"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td>3</td>
				<td>
					<div class="groupBox">
						<div draggable="true" class="studentBox fullBox">Plusan</div>
						<div draggable="true" class="studentBox fullBox">Subtractorr</div>
						<div class="studentBox emptyBox"></div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<br>
	<h1>Unassigned Students</h1>
	<hr>
	<div class="groupBox">
		<div draggable="true" class="studentBox fullBox">Skbid</div>
		<div draggable="true" class="studentBox fullBox">Chele</div>
		<div class="studentBox emptyBox"></div>
	</div>
@endsection
