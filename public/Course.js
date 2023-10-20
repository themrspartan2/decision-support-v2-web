var createProjectsModal;
var span;

$(document).ready(function () {
    //Page setup
    createProjectsModal = document.getElementById("projectsCreation");
    span = document.getElementsByClassName("close")[0];
    addProject();

    span.onclick = function () {
        createProjectsModal.style.display = "none";
    }
});

function showProjectsModal() {
    console.log("Displaying project choice creation modal");
    createProjectsModal.style.display = "block";
}

function addProject() {
    let newEntry = "";  //do you have to do it like this? doesn't matter, it works
    newEntry += "<div class='row'>";
    newEntry += "<div class='col-lg-8'>";
    newEntry += "<input class='form-control' type='text' placeholder='Project name'>";
    newEntry += "</div>";
    newEntry += "<div class='col-lg-4'>";
    newEntry += "<button type='button' class='btn-close deleteProject'></button>";
    newEntry += "</div>";
    newEntry += "</div><br>";

    $('#projectsForm').append(newEntry);

    makeDeletes();  //reset deletes. Why do we have to do it this way?
}

function makeDeletes() {    //makes delete project buttons work
    console.log("setting up delete buttons");
    $('.deleteProject').click(function () {
        console.log("removing project");
        $(this).parent().parent().next().remove();  //remove <br> after project box
        $(this).parent().parent().remove(); //remove project box
    });
}

/* moved into html
//creates survey getting data from inputs from projects modal
function makeAggregationSurvey() {
    let result = "";    //string to be passed to aggregation survey making function
    $('#projectsForm').children().each(function () { //rows
        if ($(this).is("div")) {    //ignore the <br>s
            let project = $(this).find("input").val();
            console.log("got project: " + project);
            result += project + ", ";   //projects will be comma separated
        }
    });
    if (result != "") result = result.substring(0, result.length - 2);    //remove last comma and space
    else result = "none";
    console.log("Full project string: " + result);
    
    //should say complete and then push back to projects page
}
*/

