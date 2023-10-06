@extends('layouts.master')

@section("title")
Students
@endsection

@section("pageJS")
<script src="decisions.js?rev=1"></script>
@endsection

@section("content")
<table align="center" class="betterTable">
		<thead>
			<tr>
				<th>Name</th><th>ID</th><th>Courses</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Jereniah Elemenice</td><td>fridge</td><td>CSE 101, CSE 201</td>
			</tr>
			<tr>
				<td>Calibiri Ariala</td><td>font</td><td>HST 321</td>
			</tr>
			<tr>
				<td>Mandrino Naranjo</td><td>color</td><td>CSE 102, CSE 211</td>
			</tr>
		</tbody>
	</table>
@endsection
