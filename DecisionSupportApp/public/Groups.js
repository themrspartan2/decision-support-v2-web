var APIKey="";
var baseURL="";
var URL="";
var studentBoxes;
var modal;
var span;
var createGroupsButton;

$(document).ready(function() {
	//Page setup
	modal = document.getElementById("groupCreation");
	span = document.getElementsByClassName("close")[0];
	createGroupsButton = document.getElementById("createGroups");
	createGroupsButton.onclick=function(){
		modal.style.display = "block";
	}
	
	span.onclick = function() {
		modal.style.display = "none";
	}

	studentBoxes = document.querySelectorAll('.groupBox .fullBox');
	emptyBoxes = document.querySelectorAll('.groupBox .emptyBox');
	setUpDrag();


});



//Drag functionality
function setUpDrag(){
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

function handleDragStart(e){
	this.style.opacity='0.4';	//visual indicator of dragging element
	dragSrcEl=this;
	e.dataTransfer.effectAllowed = 'move';
	e.dataTransfer.setData('text/html', this.innerHTML);	//set MIME type and set data to the html of the div
}

function handleDragEnd(e){
	this.style.opacity='1';
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
		dragSrcEl.innerHTML = this.innerHTML;
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

		dragSrcEl.remove();
		$(this).parent().append("<div class='studentBox emptyBox'></div>");	//make new empty box to replace
	
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


