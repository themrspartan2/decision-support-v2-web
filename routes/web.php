<?php

use Illuminate\Support\Facades\Route;
USE App\Http\Controllers\CanvasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**
 * The landing page of the application.
 *
 * Method: GET
 * URI: /
 * Controller action: CanvasController@index
 * Route name: index
 */
Route::get('/', [CanvasController::class, 'index'])->name('index');

/**
 * Retrieve a list of courses from Canvas API and display them on the courses page.
 * 
 * Method: GET, POST
 * URI: /getcourses
 * Controller action: CanvasController@getCourses
 * Route name: getCourses
 * 
 * Parameters:
 * - api_key: The Canvas API key (optional if already stored in cache).
 */
Route::match(['get', 'post'], '/getcourses', [CanvasController::class, 'getCourses'])->name('getCourses');

/**
 * Retrieve a list of students enrolled in a course from Canvas API and display them on the students page.
 * 
 * MAY BE DEPRECATED!!!
 * FURTHER ANALYSIS REQUIRED!!!
 * 
 * Method: POST
 * URI: /seestudents/{id}/{name}
 * Controller action: CanvasController@getStudents
 * Route name: getStudents
 * 
 * Parameters:
 * - id: The ID of the course to retrieve the students from.
 * - name: The name of the course.
 */
Route::post('/seestudents/{id}/{name}', [CanvasController::class, 'getStudents'])->name('getStudents');

/**
 * Retrieve detailed information about a course from Canvas API.
 * 
 * MAY BE DEPRECATED!!!
 * FURTHER ANALYSIS REQUIRED!!!
 * 
 * Method: POST
 * URI: /getcourse/{courseid}
 * Controller action: CanvasController@getCourse
 * Route name: getCourse
 * 
 * Parameters:
 * - courseid: The ID of the course to retrieve information from.
 */
Route::post('/getcourse/{courseid}', [CanvasController::class, 'getCourse'])->name('getCourse');

/**
 * Create a survey from the selected groups of students.
 * 
 * Method: POST
 * URI: /makesurvey/{id}
 * Controller action: CanvasController@makeSurvey
 * Route name: makeSurvey
 * 
 * Parameters:
 * - id: The ID of the project to create the survey for.
 */
Route::post('/makesurvey/{id}', [CanvasController::class, 'makeSurvey'])->name('makeSurvey');

/**
 * Retrieve a list of groups associated with a project from Canvas API.
 * 
 * Method: GET
 * URI: /getgroups/{projectId}
 * Controller action: CanvasController@getGroups
 * Route name: getGroups
 * 
 * Parameters:
 * - projectId: The ID of the project to retrieve the groups from.
 */
Route::get('/getgroups/{projectId}', [CanvasController::class, 'getGroups'])->name('getGroups');

/**
 * Retrieve a list of projects from Canvas API and display them on the projects page.
 * 
 * Method: GET
 * URI: /getprojects/{courseId}
 * Controller action: CanvasController@getProjects
 * Route name: getProjects
 * 
 * Parameters:
 * - courseId: The ID of the course to retrieve the projects from.
 */
Route::get('/getprojects/{courseId}', [CanvasController::class, 'getProjects'])->name('getProjects');

/**
 * Use an algorithm to create groups for a project.
 * 
 * Method: GET
 * URI: /makegroupsbyalgol/{algol}/{size}/{numGroupsOn}
 * Controller action: CanvasController@makeGroupsByAlgol
 * Route name: makeGroupsByAlgol
 * 
 * Parameters:
 * - algol: The name of the algorithm to use for grouping.
 * - size: The desired group size.
 * - numGroupsOn: The number of groups to create.
 */
Route::get('/makegroupsbyalgol/{algol}/{size}/{numGroupsOn}', [CanvasController::class, 'makeGroupsByAlgol'])->name('makeGroupsByAlgol');

/**
 * Initialize the process of building groups.
 *
 * Method: GET
 * URI: /buildgroups
 * Controller action: CanvasController@initBuild
 * Route name: buildGroups
 */
Route::get('/buildgroups', [CanvasController::class, 'initBuild'])->name('buildGroups');

/**
 * Initialize the process of creating projects and surveys.
 *
 * Method: GET
 * URI: /createprojectssurvey/{result}
 * Controller action: CanvasController@initMakeSurvey
 * Route name: createProjectsSurvey
 *
 * Parameters:
 * - result: The result of the project creation process.
 */
Route::get('/createprojectssurvey/{result}', [CanvasController::class, 'initMakeSurvey'])->name('createProjectsSurvey');

/**
 * Display the Frequently Asked Questions (FAQ) page.
 *
 * Method: GET
 * URI: /faqPage
 * Controller action: CanvasController@faqPage
 * Route name: faqPage
 */
Route::get('/faqPage', [CanvasController::class, 'faqPage'])->name('faqPage');

/**
 * Display the About Us page.
 *
 * Method: GET
 * URI: /aboutUsPage
 * Controller action: CanvasController@aboutUsPage
 * Route name: aboutUsPage
 */
Route::get('/aboutUsPage', [CanvasController::class, 'aboutUsPage'])->name('aboutUsPage');

/**
 * Display the Course page.
 * 
 * ALMOST CERTAINLY DEPRECATED!!!
 *
 * Method: GET
 * URI: /course
 * View: Course
 */
Route::get('/course', function () {
    return view('Course');
});

/**
 * Display the Courses page.
 * 
 * ALMOST CERTAINLY DEPRECATED!!!
 *
 * Method: GET
 * URI: /courses
 * View: Courses
 */
Route::get('/courses', function () {
    return view('Courses');
});

/**
 * Display the Groups page.
 * 
 * ALMOST CERTAINLY DEPRECATED!!!
 *
 * Method: GET
 * URI: /groups
 * View: Groups
 */
Route::get('/groups', function () {
    return view('Groups');
});

/**
 * Display the Login page.
 * 
 * ALMOST CERTAINLY DEPRECATED!!!
 *
 * Method: GET
 * URI: /login
 * View: Login
 */
Route::get('/login', function () {
    return view('Login');
});

/**
 * Display the Students page.
 * 
 * ALMOST CERTAINLY DEPRECATED!!!
 *
 * Method: GET
 * URI: /students
 * View: Students
 */
Route::get('/students', function () {
    return view('Students');
});
