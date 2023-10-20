@extends('layouts.master')

@section("title")
Groups
@endsection

@section("pageJS")
<script src="{{ URL::asset('Groups.js') }}"></script>
@endsection

@section("content")
<!-- Variables we can use on this page:
groups - array of groups. Each group is an array with the first element being the group name and subsequent elements being students
unassigned - array of unassigned students
initialized - boolean saying whether there are groups formed yet
Note: either groups or unassigned will be null depending on the value of initialized
-->
    @if(!$initialized)
    <div class="centerText">
        <button type="button" id="createGroups" class="btn btn-dark">Create New Groups</button>
        @if($unassigned == NULL)
            <button type="button" onclick="pushGroups()" href="#" class="btn btn-dark" id="pushGroupsButton">Push Groups to Canvas</button>
            <script>
                function pushGroups() {
                    showLoader();
                    //call route
                    var url = "{{ route('buildGroups') }}";
                    console.log("here's the route url:'");
                    console.log(url);
                    location.href=url;
                }
            </script>
        @endif
    </div>
    @endif
    @if($initialized)
    <div class="modal" id="groupsPulled">
		<div class="modalContent">
			<div class="modalHeader">
                <span class="close">&times;</span>
				<h2>Groups pulled from Canvas</h2>
            </div>
            <div class="modalBody">
                Groups already exist for this project, so you must erase them from Canvas if you wish to create new ones.
            </div>
		</div>
	</div>
    @endif

    <!--
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
            </div>
            <div class="col-sm-3">
                @if(!$initialized)
                <button type="button" id="createGroups" class="btn btn-dark">Create New Groups</button>
                @endif
                @if($initialized) <h3>Got groups from canvas</h3> @endif
            </div>
            <div class="col-sm-3">
                @if(!$initialized && $unassigned == NULL)
                <button type="button" onclick="pushGroups()" href="#" class="btn btn-dark" id="pushGroupsButton">Push Groups to Canvas</button>
                <script>

                    function pushGroups() {
                        //call route
                        var url = "{{ route('buildGroups') }}";
                        console.log("here's the route url:'");
                        console.log(url);
                        location.href=url;
                    }
                </script>
                @endif
            </div>
            <div class="col-sm-3">
            </div>
        </div>
    </div>
    -->

    <!-- modal for group formation options -->
	<div id="groupCreation" class="modal">
		<div class="modalContent">
			<div class="modalHeader">
				<span class="close">&times;</span>
				<h2>Group Options</h2>
			</div>
			<div class="modalBody">
				<form action="/action_page.php">
					<div class="form-group">
					  <div class="dropdown">
						<button class="btn btn-secondary dropdown-toggle" id="algorithmButton"
                        type="button" data-bs-toggle="dropdown">Cluster Gender
						<span class="caret"></span></button>
						<ul class="dropdown-menu dropdownOptions">
						  <li><a onclick="cluster()">Cluster Gender</a></li>
						  <li><a onclick="balance()">Balance Grades</a></li>
						  <li><a onclick="aggregate()">Project Choices</a></li>
						</ul>
					  </div> 
					</div>
                    <br>
					<div class="form-group" id="groupOptions">
                        <div class="dropdown">
						<button class="btn btn-secondary dropdown-toggle" id="numberingButton"
                        type="button" data-bs-toggle="dropdown">Number of Groups
						<span class="caret"></span></button>
						<ul class="dropdown-menu dropdownOptions">
						  <li><a onclick="numGroups()">Number of Groups</a></li>
						  <li><a onclick="numStudentsInGroup()">Number of Students Per Group</a></li>
						</ul>
					  </div>
                      <br>
					  <input type="number" class="form-control" id="numberingNumber" min="2" value="2">
                      <br>
                      
                      <button type="button" onclick="createGroups()" href="#" class="btn btn-dark" id="createGroupsButton">Create groups</button>
                      <script>
                            function createGroups() {
                                showLoader();
                                let num = $('#numberingNumber').val();
                                let algorithm="C";
                                let numbering=true;
                                switch($('#algorithmButton').html()){   //set algorithm
                                    case "Cluster Gender":
                                        algorithm="C";
                                        break;
                                    case "Balance Grades":
                                        algorithm="B";
                                        break;
                                    case "Project Choices":
                                        algorithm="A";  //for aggregation
                                        break;
                                    default:
                                        break;
                                }

                                switch($('#numberingButton').html()){
                                    case "Number of Groups":
                                        numbering=true;
                                        break;
                                    case "Number of Students Per Group":
                                        numbering=false;
                                        break;
                                    default:
                                        break;
                                }

                                console.log("creating groups with algorithm=" + algorithm + ", numbering=" + numbering + ", number=" + num);

                                //call route
	                            var url = "{{ route('makeGroupsByAlgol', [':algorithm', ':size', ':numbering']) }}";
	                            url = url.replace(':algorithm', algorithm);
                                url = url.replace(':size', num);
                                url = url.replace(':numbering', numbering);
                                console.log("here's the route url:'");
                                console.log(url);
                                location.href=url;
                            }
                      </script>
                      

					</div>

				  </form> 
			</div>
			<div class="modalFooter">
			</div>
		</div>
	</div>

    @if($initialized)
	<table align="center" class="betterTable" id="assignedStudentsTable">
		<thead>
			<tr>
				<th>Group Name</th>
                <th>Students</th>
			</tr>
		</thead>
        
		<tbody>
            @foreach($groups as $group)
                <tr>
				    <td>{{ $group[0] }}</td>
                    <td>
                    <div class="groupBox">
                    @for($i=1; $i<sizeof($group); $i++)
						<div class="studentBox fullBox">{{ json_decode($group[$i])->name }}</div>
                    @endfor
                    </div>
                    </td>
			    </tr>
            @endforeach
            <!-- old example name box
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
            -->
		</tbody>
	</table>
    @endif

    @if(!$initialized && $unassigned == NULL)
    <div class="algorithmSuccess">
        <div class="div-circle div-circle-sm" style="float: left" data-toggle="tooltip" data-placement-"top"
        title="
            Displayed is the success of the algorithm followed by the average success of 10,000 random group creation trials.
            Group success is measured by 
            @if ($algol == 'C')
                the number of minority students which are in a group with another minority student.
            @endif
            @if ($algol == 'B')
                the standard deviation from the mean of each group's average course grade.
            @endif
            @if ($algol == 'A')
                how closely students' project preferences align within a group.
            @endif
        "
        >
        ?
        </div>

        @if ($algol == 'C')
            Gender Clustering
        @endif
        @if ($algol == 'B')
            Grade Balancing
        @endif
        @if ($algol == 'A')
            Project Choice
        @endif
        Algorithm Success: {{ number_format((float)($success), 2) . '%' }} | {{ number_format((float)($randomSucc), 2) . '%' }}
    </div>

	<table align="center" class="betterTable" id="assignedStudentsTable">
		<thead>
			<tr>
				<th>Group</th>
                @if($algol=='B')    <!-- show GPAs for grade balancing -->
                <th>GPA
                <div class="div-circle div-circle-sm" data-toggle="tooltip" data-placement="top" style="float: right; margin-top: 7px"
                title="Average Canvas course grade of the students in the group"
                >
                  ?
                </div>
                </button>
                </th>
                @endif
                @if($algol=='A')    <!-- show project choices for project choice aggregation -->
                <th>Top 3 Choices
                <div class="div-circle div-circle-sm" data-toggle="tooltip" data-placement="top" style="float: right; margin-top: 7px"
                title="Top 3 choices for the group in order, taking all members' choices into account"
                >
                  ?
                </div>
                </button>
                </th>
                @endif
                <th>Students</th>
			</tr>
		</thead>
        
		<tbody>
            @php $index = 1 @endphp
            @foreach($groups as $group)
                @if(!empty($group->getStudents()))  <!-- don't want groups with no students to show up -->
                    <tr>
				        <td>
                            <div class="groupName">
                                {{ 'Group ' . $index }}
                            </div>
                        </td>
                        @if($algol=='B')
                        <td>
                            <div class="groupName">
                                {{ number_format((float)$group->getAvgGrade(), 2, '.', '') }}
                            </div>
                        </td>
                        @endif
                        @if($algol=='A')    <!-- show project choices for project choice aggregation -->
                        <td>
                        <div style="max-width: 300px">
                            @php $choices = $group->getTop3Choices(); @endphp
                            @if(count($choices)>=1) <span style="color: #70d4ff">{{ $choices[0] }}</span> <br> @endif
                            @if(count($choices)>=2) <span style="color: #009de0">{{ $choices[1] }}</span> <br> @endif
                            @if(count($choices)>=3) <span style="color: #00648f">{{ $choices[2] }}</span> @endif
                        </div>
                        </td>
                        @endif
                        <td>
                        <div class="groupBox">
                        @foreach($students = $group->getStudents() as $student)
                            <div style="border: 3px solid
                            @if($algol == 'C' && $student->getGenderString() == "Male") #4287f5 @endif
                            @if($algol == 'C' && $student->getGenderString() == "Female") #a74482 @endif
                            @if($algol!='C') gray @endif"
                            class="studentBox fullBox" data-toggle="tooltip" data-bs-html="true" data-placement="top"
                            title="
                                Gender: {{ $student->getGenderString() }}<br>
                                Course Grade: {{ $student->getGrade() }}
                            "
                            >
                                @if($algol=='C')
                                    <div style="float: left; width: 15px;">
                                        @if($student->getGenderString() == "Male") &male; @endif
                                        @if($student->getGenderString() == "Female") &female; @endif
                                        @if($student->getGenderString() == "Unknown") ? @endif
                                    </div>
                                @endif
                                <div style="text-align: center">
                                    {{ $student->getName() }}
                                </div>
                            </div>
                        @endforeach
                        </div>
                        </td>
			        </tr>
                    @php $index++ @endphp
                @endif
            @endforeach
            <!-- old example name box
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
            -->

		</tbody>
	</table>
    @endif

    @if(!$initialized && $unassigned != NULL)
	<h1>Unassigned Students</h1>
	<hr>
	<div class="groupBox" id="unassigned">

        @foreach(json_decode($unassigned) as $student)
            <div class="studentBox fullBox">{{ $student->name }}</div>
        @endforeach
        <!--example box
		<div draggable="true" class="studentBox fullBox"
        data-studID="120398"
        data-name="Charl"
        data-grade="3.6"
        data-minoStatus="false"
        data-attributes='[{"choice1":"golf","choice2":"soccer","choice3":"basketball"}]'
        >Charl</div>
		<div draggable="true" class="studentBox fullBox">Chele</div>

        <div class="studentBox emptyBox"></div>
        -->
        
	</div>
    @endif
    <br>
@endsection
