@extends('layouts.master')

@section("title")
Course
@endsection

@section("pageJS")
<script src="{{ URL::asset('Course.js') }}"></script>
@endsection

@section("content")

@if(empty($projects))
<h1 style="padding: 10px;">No projects to display</h1>
@endif

<div class="center">
    <br>
    <button type="button" onclick="showProjectsModal()" class="btn btn-dark">Create Canvas Survey</button>
</div>

<!--modal for aggregation survey options-->
    <div id="projectsCreation" class="modal">
		<div class="modalContent">
			<div class="modalHeader">
				<span class="close">&times;</span>
				<h2>Projects</h2>
			</div>
			<div class="modalBody">
        <div>
          <p>If all students will be doing the same project, leave project choices blank.</p>
        </div>
				<div class="form-group" id="projectsForm">
                    <!-- Choices will be put here -->
				</div>
                <!-- button to add another project-->
                <button type="button" class="btn btn-secondary" onclick="addProject()">Add Project</button>
                <!-- <button type="button" onclick="addProject()" class="btn btn-primary">Add Project</button> //for adding new textboxes -->
                <button type="button" onclick="makeAggregationSurvey()" href="#" class="btn btn-dark">Create Canvas Survey</button>
                <script>
                    function makeAggregationSurvey() {
                        let result = "";    //string to be passed to aggregation survey making function
                        $('#projectsForm').children().each(function () { //rows
                            if ($(this).is("div")) {    //ignore the <br>s
                            let project = $(this).find("input").val();
                            if(project != ""){
                                console.log("got project: " + project);
                                result += project + ", ";   //projects will be comma separated
                            }
                        }
                    });
                    if (result != "") result = result.substring(0, result.length - 2);    //remove last comma and space
                    else result = "none";
                    console.log("Full project string: " + result);

                    //call route
                    var url = "{{ route('createProjectsSurvey', [':projectsString']) }}";
                    url = url.replace(':projectsString', result);
                    location.href=url;
                    //should say complete and then push back to projects page
}                   
                </script>
			</div>
			<div class="modalFooter">
			</div>
		</div>
	</div>

<div class="cards">
  @foreach ($projects as $project)
  <div class="card">
    <div class="card-body">
      <h2 class="card-title">{{ $project->name }}</h2>
      <a class="boxLink" href="{{route('getGroups', [$project->id]) }}">{{ csrf_field() }}</a>
    </div>
  </div>
  @endforeach

</div>
@if(session('surveySuccess'))
    <script type="text/javascript" >
        alert('{{ session('surveySuccess') }}');
    </script>
@endif
@endsection
