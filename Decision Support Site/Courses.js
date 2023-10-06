var APIKey="";
var baseURL="";
var URL="";

$(document).ready(function() {
	 clickAndSelect();
});





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

//Drag functionality
