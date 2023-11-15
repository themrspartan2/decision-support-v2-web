var studentBoxes;
var createGroupsModal;
var groupsPulledModal;
var span;
var createGroupsButton;
var draggedElement;
var studentData = [];
var studentObjects = [];
var algorithm = "C";
var numbering = "true";    //true is number of groups, false is number of students per group

$(document).ready(function() {
	//Page setup
    createGroupsModal = document.getElementById("groupCreation");
    groupsPulledModal = document.getElementById("groupsPulled");

    if (groupsPulledModal!=null) {
        groupsPulledModal.style.display = "block";
    }
    
    span = document.getElementsByClassName("close")[0];    //for whichever modal is present (groups pulled/create groups)

    createGroupsButton = document.getElementById("createGroups");
    if (createGroupsButton != null) {
        createGroupsButton.onclick = function () {
            console.log("Displaying group creation modal");
            createGroupsModal.style.display = "block";
        }
    }
    
    span.onclick = function () {    //closes modal with x button
        console.log("close button 1 pressed");
        if (groupsPulledModal != null) {
            groupsPulledModal.style.display = "none";
        }
        if (createGroupsModal != null) {
            createGroupsModal.style.display = "none";
        }
    }

    //tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

	//setUpDrag();

});

//Make dropdown options show on the dropdown button when selected
$('.dropdown-menu').on('click', 'a', function () {
    var text = $(this).html();
    var htmlText = text + ' <span class="caret"></span>';
    $(this).closest('.dropdown').find('.dropdown-toggle').html(htmlText);
});

//Algorithm selections
function cluster() {
    console.log("selected cluster");
    algorithm = "C";
    $('#algorithmButton').html("Cluster Gender");
    //$('#groupOptions').empty();
    //makeNumGroups();
    //makeMakeGroups();
}

function balance() {
    console.log("selected balance");
    algorithm = "B";
    $('#algorithmButton').html("Balance Grades");
    //$('#groupOptions').empty();
    //makeNumGroups();
    //makeMakeGroups();
}

function aggregate() {
    console.log("selected aggregate");
    algorithm = "A";
    $('#algorithmButton').html("Project Choices");
    //$('#groupOptions').empty();
    //makeNumGroups();
    //makeMakeGroups();
}

function numGroups() {
    console.log("selected number of groups");
    numbering = true;
    $('#numberingButton').html("Number of Groups");
}

function numStudentsInGroup() {
    console.log("selected number of students per group");
    numbering = false;
    $('#numberingButton').html("Number of Students Per Group");
}

function makeNumGroups() {
    $('#groupOptions').append("<label for= 'numGroups' ># of groups</label> <input type='number' class='form-control' id='numGroups' min='2' value='2'>");
}

function makeMakeGroups() {
    $('#groupOptions').append("<br><button type='button' class='btn btn-primary' onclick='createGroups()'>Create groups</button>");
}

/*  //replaced with in-html script
//creating groups
function createGroups() {
    let num = $('#numberingNumber').val();
    console.log("creating groups with algorithm=" + algorithm + ", numbering=" + numbering + ", number=" + num);

    a = $.ajax({
        url: "{{route('makegroupsbyalgol')}}",
        method: "POST",
        data: {
            "algol": algorithm,
            "size:": num,
            "numGroupsOn": numbering
        }
    }).done(function (data) {
        var result = data.result;
    }).fail(function (error) {
        console.log("Call failed");
    });
}
*/


/*
//Drag functionality
function setUpDrag() {
    studentBoxes = document.querySelectorAll('.groupBox .fullBox');
    emptyBoxes = document.querySelectorAll('.groupBox .emptyBox');
	studentBoxes.forEach(function (item) {
		item.addEventListener('dragstart', handleDragStart);
		//item.addEventListener('dragover', handleDragOver);
		//item.addEventListener('dragenter', handleDragEnter);
		//item.addEventListener('dragleave', handleDragLeave);
		item.addEventListener('dragend', handleDragEnd);
		//item.addEventListener('drop', handleDrop);
	});

	emptyBoxes.forEach(function (item) {
		//item.addEventListener('dragstart', handleDragStart);
		item.addEventListener('dragover', handleDragOver);
		item.addEventListener('dragenter', handleDragEnter);
		item.addEventListener('dragleave', handleDragLeave);
		item.addEventListener('dragend', handleDragEnd);
		item.addEventListener('drop', handleDrop);
	});
}

function handleDragStart(e) {
    draggedElement = this;  //this is what will be dropped into the new place
    draggedElement = draggedElement.cloneNode(true);    //makes sure everything inside the div is cloned
    this.style.opacity = '0.4';	//visual indicator of dragging element
    draggedElement.style.opacity = '0.4';
	dragSrcEl=this;
	e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);	//set MIME type and set data to the html of the div
    console.log(draggedElement);
}

function handleDragEnd(e){
    this.style.opacity = '1';
    draggedElement.style.opacity = '1';
	studentBoxes.forEach(function (item){
		item.classList.remove('over');
	});
}

function handleDragOver(e) {
    e.preventDefault();
    return false;
  }

function handleDragEnter(e) {
	this.classList.add('over');
}

function handleDragLeave(e) {
    this.classList.remove('over');
}

function handleDrop(e) {
    e.stopPropagation(); // stops the browser from redirecting.
    
	//dragSrcEl.innerHTML = this.innerHTML;
	this.innerHTML = e.dataTransfer.getData('text/html');
	this.classList.remove('emptyBox');
	this.classList.remove('over');
	this.classList.add('fullBox');
    this.setAttribute("draggable", true);

	this.removeEventListener('dragover', handleDragOver);	//transform empty box into full box
	this.removeEventListener('dragenter', handleDragEnter);
	this.removeEventListener('dragleave', handleDragLeave);
	this.removeEventListener('drop', handleDrop);
    this.addEventListener('dragstart', handleDragStart);
    
    this.innerHTML = e.dataTransfer.getData('text/html');
    console.log(this.innerHTML);
    

    draggedElement.innerHTML=this.innerHTML;
    $(this).parent().append(draggedElement);

    dragSrcEl.remove();
    $(this).parent().append("<div class='studentBox emptyBox'></div>");	//make new empty box to replace

    $(this).remove();
	
	emptyBoxes = document.querySelectorAll('.groupBox .emptyBox');	//recalculate drag & drop
	setUpDrag();

		
	return false;
  }

function getStudents() {

	a=$.ajax({
		url: URL,
		method: "GET"
	}).done(function(data) {
		var result=data.result;
		
	}).fail(function(error) {
		console.log("Call failed");
	});
}
*/

