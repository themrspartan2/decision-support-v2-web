<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Cache;

use App\Classes\Group;
use App\Classes\Student;
require_once(app_path() . '/Algorithms/Balancing.php');
require_once(app_path() . '/Algorithms/Clustering.php');
require_once(app_path() . '/Algorithms/Aggregation.php');
require_once(app_path() . '/Algorithms/Random.php');

class CanvasController extends Controller
{
    /**
     * Displays the Login page
     * 
     * Directly called by the index route
     * 
     * @return \Illuminate\View\View The 'Login' view.
     */
    public function index() {
        Cache::flush();
        return view('Login');
    }

    /**
     * Displays the FAQ page
     * 
     * Directly called by the faqPage route
     * 
     * @return \Illuminate\View\View The 'FAQs' view.
     */
    public function faqPage(){
        return view('FAQs');
    }

    /**
     * Displays the About Us page
     * 
     * Directly called by the aboutUsPage route
     * 
     * @return \Illuminate\View\View The 'AboutUs' view.
     */
    public function aboutUsPage(){
        return view('AboutUs');
    }

    /**
     * Retrieves groups based on project ID
     * 
     * Directly called by the getProjects route.
     *
     * @param int $projectId The ID of the project.
     * @return \Illuminate\View\View The view containing groups, unassigned students, and initialization status.
     */
    public function getGroups($projectId) {
        $courseId=Cache::get('courseId');
        //\Log::info("Project ID from cache: " . $projectId);
        //\Log::info("Course ID from cache: " . $courseId);
        $response = Http::withHeaders([ //get project from project ID
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $courseId . '/assignments/' . $projectId);
        $project=$response->body();
        //\Log::info("Response to getting assignment:");
        //\Log::info($project);
        Cache::put('project', $project);

        $this->makeGroupSets();

        $groupCategoryId=$this->getGroupCategoryIdFromProject();   //uses project ID to get assignment name, which should be the same as the group set name

        $response = Http::withHeaders([ //get groups in the group category
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'group_categories/' . $groupCategoryId . '/groups');
        $groups=json_decode($response->body());

        $groupsWithStudents=array();//list of groups with group name and students
        $unassigned=array();        //list of unassigned students
        $groupsInitialized; //boolean saying whether the groups exist yet
        if(empty($groups)){ //assume all students are unassigned if groups are empty
            $response = Http::withHeaders([ //get all students in the group category
                'Authorization' => 'Bearer ' . Cache::get('key')
            ])->get(\Config::get('values.default_canvas_url') . 'courses/' . Cache::get('courseId') . '/users?enrollment_type[]=student&per_page=100');
            $unassigned=$response->body();
            Cache::put('students', json_decode($unassigned));
            $groupsInitialized=false;
        } else {    //assume all students have been assigned to groups as long as groups exist. Very bold assumption
            foreach($groups as $group){
                $groupWithStudents=array(); //array of students in a single group
                array_push($groupWithStudents, $group->name);  //first element in each group is the group name
                $response = Http::withHeaders([ //get students in a group
                'Authorization' => 'Bearer ' . Cache::get('key')
                ])->get(\Config::get('values.default_canvas_url') . 'groups/' . $group->id . '/users');
                $studentsInGroup=json_decode($response->body());
                foreach($studentsInGroup as $studentInGroup){
                    $response = Http::withHeaders([ //get students in a group
                    'Authorization' => 'Bearer ' . Cache::get('key')
                    ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $courseId . '/users/' . $studentInGroup->id);
                    array_push($groupWithStudents, $response->body());
                }
                array_push($groupsWithStudents, $groupWithStudents);
            }

            $groupsInitialized=true;
        }
        
        // \Log::info("Groups being passed to groups page:");
        // \Log::info($groupsWithStudents);

        return view('/groups', ['groups'=>$groupsWithStudents, 'unassigned'=>$unassigned, 'initialized'=>$groupsInitialized]);
    }

    /**
     * Retrieves a list of courses for a user.
     * Only gets the courses where the user is marked as a teacher.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View The redirect response or view containing the list of courses.
     */
    public function getCourses(Request $request) {
        Cache::forget('courseId');
        if (!Cache::has('key')) {   //key is API key
            Cache::put('key', $request->api_key);
        }
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses?enrollment_type=teacher');

        $courses = json_decode($response->body());

        // Checks if the provided API key is a valid API key; if not redirect to login screen with a message saying that the key is invalid
        if (isset(json_decode($response->body())->errors)) {
            return redirect('/')->with('badKey', 'Key Authentication Failed');
        }

        // Checks if the provided API key only has student level access to all of their courses. If so return to login screen.
        if (empty(json_decode($response->body()))) {
            return redirect('/')->with('isStudent', 'This application may only be accessed by accounts who are marked as a Teacher in at least one class.');
        }

        //foreach ($courses as $course) {
            //\Log::info("name: " . $course->name . ", ID: " . $course->id);
        //}
        return view('/courses', ['courses'=>$courses]);
    }

    /**
     * Retrieves a list of students for a given course.
     * 
     * MAY BE DEPRECATED!!
     * REQUIRES IN-DEPTH ANALYSIS!!
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing the course ID and name.
     * @return \Illuminate\View\View The view containing the list of students and course name.
     */
    public function getStudents(Request $request) {
        $id = $request->id;
        $courseName = $request->name;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/users?enrollment_type[]=student');
        // \Log::info(json_decode($response->body()));
        $students = json_decode($response->body());
        //foreach ($students as $student) {
            //\Log::info($student->name);
        //}
        return view('/students', ['students'=>$students, 'courseName'=>$courseName]);
       
    }

    /**
     * Initializes the process of creating a survey
     * 
     * Directly called by the createProjectsSurvey Route
     *
     * This method is responsible for initiating the creation of a survey
     * It calls the `makeSurvey()` method, passing the projects as a parameter.
     * If no projects were input, projects will be 'none'.
     * After the survey creation is complete, it redirects back to the previous page with a success message.
     *
     * @param Request $request The HTTP request object containing the projects to create the survey from.
     * @return \Illuminate\Http\RedirectResponse A redirect response back to the previous page with a success message.
     */
    public function initMakeSurvey(Request $request) {
        $projects = $request->result;
        $this->makeSurvey($projects);
        return back()->with('surveySuccess', 'Survey created successfully');
    }

    private function checkSurvey($courseId) {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $courseId . '/quizzes?search_term=Survey2');
        if (!empty(json_decode($response->body()))) {
            $id = json_decode($response->body(), true)['id'];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . Cache::get('key')
            ])->delete(\Config::get('values.default_canvas_url') . 'courses/' . $courseId . '/quizzes' . '/' . $id);
        }
    }

    /**
     * Creates a survey based on the provided projects.
     *
     * This method is responsible for creating a survey based on the projects provided as a parameter. It performs an API
     * request to create a quiz (survey) in the specified course. The survey is set as a graded survey with hidden results,
     * and the due date is set to 7 days from the current date. After the survey is created, it calls the
     * `createDataQuestions()` method to create data-related questions for the survey.
     *
     * @param string $projects The projects to create the survey from. If no projects were entered, $projects is the string 'none'
     * @return void
     */
    public function makeSurvey($projects) {
        $id = Cache::get('courseId');
        //$date = new \DateTime('now', new \DateTimeZone(date_default_timezone_get()));
        //\Log::info($id);
        //$this->checkSurvey($id);
        $surveyName = 'Survey';
        //$surveyName = "{$date->format('Y-m-d\TH:i:s\Z')}";
        $due = $this->getDateIn7Days();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->asForm()->post(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/quizzes', [
            'quiz[title]' => $surveyName,
            'quiz[description]' => 'A survey that will be used to collect data for group projects',
            'quiz[quiz_type]' => 'graded_survey',
            'quiz[hide_results]' => "always",
            'quiz[due_at]' => $due,
            'quiz[lock_at]' => $due,
            'quiz[published]' => false
        ]);
        $quiz = json_decode($response->body());
        // \Log::info(json_encode($quiz));
        $this->createDataQuestions($quiz, $id, $projects);
        return;
    }

    /**
     * Retrieves the projects (assignments) for a specified course.
     * 
     * Directly Called by the getProjects Route
     *
     * This function makes an API request to retrieve the assignment groups and their assignments for the specified course.
     * It searches for the assignment group named "Projects" and caches it for later use. Then, it retrieves the assignments
     * from the cached projects and returns them as a view parameter to be displayed in the 'Course' view.
     *
     * @param int $courseIdd The ID of the course to retrieve the projects from.
     * @return \Illuminate\View\View The 'Course' view with the projects (assignments) as a view parameter.
     */
    public function getProjects(String $courseId) {
        // \Log::info("getProjects function called");
        $id = $courseId;
        Cache::put('courseId', $id);
        // \Log::info("Id passed in to getProjects: " . $id);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/assignment_groups?include[]=assignments');
        //\Log::info(json_decode($response));
        $groups = json_decode($response->body()); //groups is groups of assignments
        foreach($groups as $group) {
            if ($group->name == "Projects") {
                Cache::put('projects', $group);
                break;
            }
        }
        //\Log::info("successfully cached projects assignment group");
        $projects = Cache::pull('projects');
        if($projects==null)$projects=array();
        else $projects=$projects->assignments;
        return view('Course', ['projects'=>$projects]);
    }

    /**
     * Creates data-related questions for the survey.
     *
     * This function is responsible for creating data-related questions for the survey. It makes an API request to add a multiple
     * choice question to the specified quiz. The question pertains to gender identification and includes three possible
     * choices: Male, Female, and Other / prefer not to say. The function also calls the `makeAggQuestions()` method if
     * projects are available, passing the quiz, course ID, and projects as parameters.
     *
     * @param stdClass $quiz The quiz object obtained from creating the quiz.
     * @param int $courseId The ID of the course where the quiz is created.
     * @param string $projects The projects associated with the survey, or 'none' if no projects are available.
     * @return void
     */
    private function createDataQuestions($quiz, $courseId, $projects){
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->asForm()->post(\Config::get('values.default_canvas_url') . 'courses/' . $courseId . '/quizzes' . '/' . $quiz->id . '/questions', [
            'question' => [
                'question_name' => 'Gender',
                'question_text' => 'What gender do you identify as?',
                'question_type' => 'multiple_choice_question',
                'points_possible' => 1,
                'answers' => [
                    [
                        'text' => 'Male',
                        'weight' => 100
                    ],
                    [
                        'text' => 'Female',
                        'weight' => 100
                    ],
                    [
                        'text' => 'Other / prefer not to say',
                        'weight' => 100
                    ]
                ]
            ]
        ]);

        if ($projects != 'none') {
            $this->makeAggQuestions($quiz, $courseId, $projects);
        }
        return;
    }

    /**
     * Creates aggregate questions for a quiz based on projects.
     * 
     * Called by createDataQuestions if projects were provided.
     *
     * This function is responsible for creating aggregate questions for a quiz based on the projects provided. It makes an
     * API request to add multiple-choice questions to the specified quiz. The number of questions created is determined
     * by the number of projects. Each question represents a project and has the project name as the answer choice. The
     * weight of each answer choice is set to 100. The function creates aggregate questions equal to the number of projects.
     *
     * @param stdClass $quiz The quiz object obtained from creating the quiz.
     * @param int $courseId The ID of the course where the quiz is created.
     * @param string $projects The projects associated with the survey.
     * @return void
     */
    private function makeAggQuestions($quiz, $courseId, $projects) {
        $choices = 3;
        $projectsArr = explode(', ', $projects);
        $answers = array();
        foreach ($projectsArr as $project) {
            $answer = [
                'text' => $project,
                'weight' => 100
            ];
            $answers[] = $answer;
        }

        for ($i = 1; $i<=$choices; $i++) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . Cache::get('key')
            ])->asForm()->post(\Config::get('values.default_canvas_url') . 'courses/' . $courseId . '/quizzes' . '/' . $quiz->id . '/questions', [
                'question' => [
                    'question_name' => 'Choice #' . strval($i),
                    'question_text' => 'Choice #' . strval($i),
                    'question_type' => 'multiple_choice_question',
                    'points_possible' => 1,
                    'answers' => $answers
                ]
            ]);
        }
        return;

    }

    /**
     * Creates a group set for projects if it doesn't already exist.
     *
     * This function ensures that a project group set exists when selecting a project. It retrieves the course ID and project
     * ID from the cache, and then makes API requests to retrieve the assignment groups and group categories for the course.
     *  Next, the function checks if a group set with the project ID already exists.
     * If not, it creates a new group set using the project name obtained from the cache.
     *
     * @return void
     */
    public function makeGroupSets() { //ensures that project group set exists when selecting a project. Selected project should be in cache at this point.
        $project=Cache::get('project');
        $project=json_decode($project);
        $projectId=$project->id;
        $id = Cache::get('courseId'); //course id
        //\Log::info("Course ID From cache: " . $id);
        //\Log::info("Project ID from cache: " . $projectId);
        $response = Http::withHeaders([ //get assignment groups in the course
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/assignment_groups?include[]=assignments');
        //https://canvas.instructure.com:443/api/v1/courses/10530000000193988/group_categories
        //\Log::info("Response to getting group sets: " . $response->body());
        $groups = json_decode($response->body());
        //\Log::info("group sets:");
        //\Log::info($groups);
        foreach($groups as $group) {    //group refers to a group set, not an actual group.
            //\Log::info("group set:");
            //\Log::info(json_encode($group));
            if ($group->name == "Projects") {
                Cache::put('projects', $group);
                break;
            }
        }
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/group_categories');
        $currGroupSets = json_decode($response->body());
        //\Log::info(collect($currGroupSets)->pluck('name')->toArray());
        //$sets = collect($currGroupSets)->pluck('name')->toArray();
        $projects = Cache::pull('projects')->assignments; //array of projects in "projects" assignement group
        $existingGroupSetNames= collect($currGroupSets)->pluck('name')->toArray();
        if(!in_array($projectId, $existingGroupSetNames)){
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . Cache::get('key')
                ])->post(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/group_categories', [
                    'name' => $project->name
                ]);
        }

        /*
        foreach($projects as $project) {
            if (!in_array($project->id, $projectId)) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . Cache::get('key')
                ])->post(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/group_categories', [
                    'name' => $project->name
                ]);
            }
        }
        */

        return;
    }

    /**
     * Retrieves the group category ID associated with a project.
     *
     * This function makes an API request to retrieve the group categories for a specific course and searches for a category
     * that matches the project name. If a match is found, the function returns the corresponding group category ID.
     *
     * @return string The ID of the matching group category, or null if no matching category is found.
     */
    private function getGroupCategoryIdFromProject(){
        $courseId=Cache::get('courseId');
        $project=Cache::get('project');
        $project=json_decode($project);
        $projectName=$project->name;    //this should be the same as the groupset/group category name
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $courseId . '/group_categories');
        $groupCategories=json_decode($response->body());
        // \Log::info("group categories:");
        // \Log::info($groupCategories);
        foreach($groupCategories as $groupCategory){
            if($groupCategory->name == $projectName){
                // \Log::info("got group set:");
                // \Log::info(json_encode($groupCategory));
                return $groupCategory->id;
                break;
            }
        }
    }

    /**
     * Get the date and time 7 days from now in UTC timezone.
     * 
     * This function does not take into account daylight savings time.
     *
     * This function returns a formatted date and time string that represents the date and time 7 days from the current
     * date and time. The date and time are adjusted based on the default timezone set in the system. The resulting date
     * and time are converted to the UTC timezone.
     *
     * @return string The formatted date and time string in the format 'Y-m-d\TH:i:s\Z'.
     */
    private function getDateIn7Days() {
        $date = new \DateTime('now', new \DateTimeZone(date_default_timezone_get()));
        $date->modify('+8 days');
        $date->setTime(3, 59, 0);
        $date->setTimezone(new \DateTimeZone('UTC'));
        return $date->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * Generate groups of students based on the selected algorithm and parameters.
     * 
     * Directly called by makeGroupsByAlgol route
     *
     * This function generates groups of students based on the selected algorithm and the provided parameters. The algorithm
     * determines how the groups will be formed, and the parameters specify the size of each group and whether the groups
     * should be made by the number of groups or by the size. The function collects the necessary data about the students
     * and their attributes from the cache, performs the group generation process, and calculates the success metrics for
     * the generated groups. The resulting groups are stored in the cache and then passed to the corresponding view for display.
     *
     * @param Request $request The HTTP request object containing the algorithm and parameters.
     * @return Illuminate\View\View The view that displays the generated groups, success metrics, and other related data.
     */
    public function makeGroupsByAlgol(Request $request) {
        // \Log::info("making groups");
        $size = $request->size;
        $algol = $request->algol;
        $madeByNumGroups = $request->numGroupsOn;
        $students = Cache::get('students');
        $iters = 10000;
        $success = 0;
        $groups = array();

        // \Log::info("algorithm:");
        // \Log::info($algol);
        // \Log::info("size:");
        // \Log::info($size);
        // \Log::info("numgroupson:");
        // \Log::info($madeByNumGroups);
        
        $id = Cache::get('courseId');

        $studs = $this->collectSurvey($id);

        if ($algol == 'B') {
            // $studs = $this->collectGrades($students, $id);
            for ($i = 0; $i<$iters; $i++) {
                $testGroups = $this->balance($size, $madeByNumGroups, $id, $studs);
                $succ = balancingSuccess($testGroups, $this->getAllStudentsFromGroups($testGroups));
                if ($succ > $success) {
                    $success = $succ;
                    $groups = $testGroups;
                }
            }
            $randomSucc = $this->randomSucc($algol, $groups, $size, $madeByNumGroups);
            // foreach ($groups as $group) {
            //     $group->avgGrade();
            // }
        }
        if ($algol == 'C') {
            //$studs = $this->collectGender($students, $id);
            $groups = $this->cluster($size, $madeByNumGroups, $id, $studs);
            $success = clusteringSuccess($groups, $this->getAllStudentsFromGroups($groups));
            $randomSucc = $this->randomSucc($algol, $groups, $size, $madeByNumGroups);
        }
        if ($algol == 'A') {
            //$studs = $this->collectProjectChoices($students, $id);
            for ($i = 0; $i<$iters; $i++) {
                $testGroups = $this->aggregate($size, $madeByNumGroups, $id, $studs);
                $succ = aggregationSuccess($testGroups, $this->getAllStudentsFromGroups($testGroups));
                if ($succ > $success) {
                    $success = $succ;
                    $groups = $testGroups;
                }
            }
            $randomSucc = $this->randomSucc($algol, $groups, $size, $madeByNumGroups);
            // foreach ($groups as $group) {
            //     $group->top3Choices();
            //     //\Log::info($group->getTop3Choices());
            // }
        }

        Cache::put('groups', $groups);

        return view('/groups', ['groups'=>$groups,
                                'unassigned'=>NULL,
                                'initialized'=>false,
                                'success'=>$success,
                                'randomSucc'=>$randomSucc,
                                'algol'=>$algol]);
        
    }

    /**
     * Calculate the average success metric of randomly generated groups.
     *
     * This function calculates the average success metric of randomly generated groups by performing multiple iterations.
     * The number of iterations is determined by the `$iters` variable. The function randomly shuffles the students within
     * the groups. The success metric is then calculated for each iteration and added to the `$sumSucc` variable. Finally,
     * the average success metric is returned by dividing `$sumSucc` by `$iters`.
     *
     * @param string $algol The selected algorithm ('A', 'B', or 'C').
     * @param array $groups The array of groups containing the students.
     * @param int $size The size of each group.
     * @param string $madeByNumGroups Indicates whether the groups were made by the number of groups or by the size.
     * @return float The average success metric of randomly generated groups.
     */
    private function randomSucc($algol, $groups, $size, $madeByNumGroups) {
        $sumSucc = 0;
        $iters = 10000;
        for ($i = 0; $i<$iters; $i++) {
            if ($madeByNumGroups == 'true') {
                $random = randomizeByGroupQty($this->getAllStudentsFromGroups($groups), $size);
            } else {
                $random = randomizeByGroupSize($this->getAllStudentsFromGroups($groups), $size);
            }
            switch ($algol) {
                case 'A':
                    $sumSucc += aggregationSuccess($random, $this->getAllStudentsFromGroups($groups));
                    break;
                case 'B':
                    $sumSucc += balancingSuccess($random, $this->getAllStudentsFromGroups($groups));
                    break;
                case 'C':
                    $sumSucc += clusteringSuccess($random, $this->getAllStudentsFromGroups($groups));
                    break;
            }
        }
        return $sumSucc / $iters;
    }

    /**
     * Collect grades, gender, and project choices for the students enrolled in the course from the survey.
     *
     * @param int $id The ID of the course.
     * @return array The array of `Student` objects with collected grades.
     */
    private function collectSurvey($id) {
        $students = Cache::get('students');
        $studs = array();
        foreach($students as $student) {
            $studs[$student->id] = new Student($student);
        }
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/enrollments?type[]=StudentEnrollment&per_page=100');
        //\Log::info($response->body());
        $grades = json_decode($response->body());
        // \Log::info($grades);

        foreach ($grades as $enrollment) {
            // \Log::info($enrollment->grades->unposted_current_score);
            $studs[$enrollment->user_id]->setGrade(floatval($enrollment->grades->unposted_current_score));
            // \Log::info($studs[$enrollment->user_id]->getName() . ": " . $studs[$enrollment->user_id]->getGrade());
        }
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/quizzes?search_term=Survey');
        // \Log::info(json_decode($response->body(), true));
        $url = json_decode($response->body())[0]->quiz_statistics_url;
        // \Log::info($url);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get($url);

        // \Log::info(json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][0]['answers'][1]['user_ids']);
        
        $males = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][0]['answers'][0]['user_ids'];
        $females = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][0]['answers'][1]['user_ids'];
        $others = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][0]['answers'][2]['user_ids'];
        foreach($students as $student) {
            if (in_array($student->id, $males)) {
                $studs[$student->id]->setMinoStatus(false);
                $studs[$student->id]->setGenderString("Male");
            }
            if (in_array($student->id, $females)) {
                $studs[$student->id]->setMinoStatus(true);
                $studs[$student->id]->setGenderString("Female");
            }
            if (in_array($student->id, $others)) {
                $studs[$student->id]->setMinoStatus(true);
                $studs[$student->id]->setGenderString("Other");
            }
            //\Log::info($studs[$student->id]->getName() . ": " . $studs[$student->id]->getMinoStatus());
        }
        if (count(json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics']) > 1) {
            $choices = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][1]['answers'];
            $questions = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'];
            for($i = 1; $i<4; $i++) {
                // \Log::info($questions[$i]['question_text']);
                // \Log::info(strval($i) . ' Choice');
                $question = $questions[$i];
                foreach($question['answers'] as $choice) {
                    //\Log::info($choice['text']);
                    //\Log::info($choice['user_names']);
                    foreach($choice['user_ids'] as $studentId) {
                        $studs[$studentId]->setAttribute($i, $choice['text']);
                    }
                }
            }
        }
        return $studs;
    }

    /**
     * Collect grades for the students enrolled in the course.
     * 
     * DEPRECATED!!! PLEASE USE collectSurvey()
     *
     * @param array $students The array of students.
     * @param int $id The ID of the course.
     * @return array The array of `Student` objects with collected grades.
     */
    private function collectGrades($students, $id) {
        $studs = array();
        foreach($students as $student) {
            $studs[$student->id] = new Student($student);
        }
        //\Log::info($id);
        //\Log::info($numGroups);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/enrollments?type[]=StudentEnrollment&per_page=100');
        //\Log::info($response->body());
        $grades = json_decode($response->body());
        // \Log::info($grades);

        foreach ($grades as $enrollment) {
            // \Log::info($enrollment->grades->unposted_current_score);
            $studs[$enrollment->user_id]->setGrade(floatval($enrollment->grades->unposted_current_score));
            // \Log::info($studs[$enrollment->user_id]->getName() . ": " . $studs[$enrollment->user_id]->getGrade());
        }
        return $studs;
    }

    /**
     * Generate balanced groups based on the size of the group or number of students per group.
     *
     * This function generates balanced groups of students based on the size of the group or number of students per group.
     * The students are passed as an array to the function, along with the group size/number of groups, and creation method indicator.
     * If the groups are created by the number of groups, the function calls the `balanceByGroupQty()` helper function to generate balanced
     * groups. If the groups are created by the size, the function calls the `balanceByGroupSize()` helper function to
     * generate balanced groups. The generated groups are returned.
     *
     * @param int $size The size of each group or number of groups.
     * @param string $madeByNumGroups Indicates whether the groups were made by the number of groups or by the size.
     * @param int $id The ID of the course.
     * @param array $studs The array of students.
     * @return array The generated balanced groups.
     */
    public function balance($size, $madeByNumGroups, $id, $studs) {
        if ($madeByNumGroups == 'true') {
            $groups = balanceByGroupQty($studs, $size);
        } else {
            $groups = balanceByGroupSize($studs, $size);
        }
        
        return $groups;
        // $groupSetId = $this->getGroupCategoryIdFromProject();
        // $this->buildGroups($groupSetId, $groups);

        // $i = 1;
        // foreach ($groups as $group) {
        //     \Log::info("Group " . $i . ":");
        //     foreach ($group->getStudents() as $student) {
        //         \Log::info($student->getName());
        //     }
        //     $i++;
        //     \Log::info(calcAvgGrade($group->getStudents()));
        //     \Log::info(" ");
        // }
        // \Log::info(balancingSuccess($groups, $studs));
    }

    /**
     * Collect gender information for the students enrolled in the course from the Survey
     *
     * DEPRECATED!!! PLEASE USE collectSurvey()
     *
     * @param array $students The array of students.
     * @param int $id The ID of the course.
     * @return array The array of `Student` objects with collected gender information.
     */
    private function collectGender($students, $id) {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/quizzes?search_term=Survey');
        // \Log::info(json_decode($response->body(), true));
        $url = json_decode($response->body())[0]->quiz_statistics_url;
        // \Log::info($url);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get($url);

        // \Log::info(json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][0]['answers'][1]['user_ids']);
        
        $males = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][0]['answers'][0]['user_ids'];
        $females = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][0]['answers'][1]['user_ids'];
        $others = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][0]['answers'][2]['user_ids'];

        $studs = array();
        foreach($students as $student) {
            $studs[$student->id] = new Student($student);
            if (in_array($student->id, $males)) {
                $studs[$student->id]->setMinoStatus(false);
                $studs[$student->id]->setGenderString("Male");
            }
            if (in_array($student->id, $females)) {
                $studs[$student->id]->setMinoStatus(true);
                $studs[$student->id]->setGenderString("Female");
            }
            if (in_array($student->id, $others)) {
                $studs[$student->id]->setMinoStatus(true);
                $studs[$student->id]->setGenderString("Other");
            }
            //\Log::info($studs[$student->id]->getName() . ": " . $studs[$student->id]->getMinoStatus());
        }
        return $studs;
    }

    /**
     * Cluster students based on gender for creating groups.
     *
     * This function clusters the students based on gender for creating groups. It takes four parameters: the group size/number of groups,
     * whether the groups are made by the number of groups or group size, the ID of the course, and the array of students.
     * The function determines the clustering method based on the value of `$madeByNumGroups`.
     *
     * If `$madeByNumGroups` is `'true'`, the function clusters the students by group quantity using the `clusterByGroupQty()`
     * method. If `$madeByNumGroups` is `'false'`, the function clusters the students by group size using the `clusterByGroupSize()`
     * method.
     *
     * The function returns the array of groups after clustering the students.
     *
     * @param int $size The group size or number of groups
     * @param string $madeByNumGroups Whether the groups are made by the number of groups or group size.
     * @param int $id The ID of the course.
     * @param array $studs The array of students.
     * @return array The array of groups after clustering the students.
     */
    public function cluster($size, $madeByNumGroups, $id, $studs) {
        if ($madeByNumGroups == 'true') {
            $groups = clusterByGroupQty($studs, $size);
        } else {
            $groups = clusterByGroupSize($studs, $size);
        }

        return $groups;
        // $groupSetId = $this->getGroupCategoryIdFromProject();
        // $this->buildGroups($groupSetId, $groups);

        // $i = 1;
        // foreach ($groups as $group) {
        //     \Log::info("Group " . $i . ":");
        //     foreach ($group->getStudents() as $student) {
        //         $m = "Male";
        //         if ($student->getMinoStatus()) {
        //             $m = "Female";
        //         }
        //         \Log::info($student->getName() . ": " . $m);
        //     }
        //     $i++;
        //     \Log::info(" ");
        // }
        // \Log::info(clusteringSuccess($groups, $studs));
    }

    /**
     * Collect project choices from students for grouping.
     *
     * DEPRECATED!!! PLEASE USE collectSurvey()
     *
     * @param array $students The array of students.
     * @param int $id The ID of the course.
     * @return array The updated array of students with project choices assigned.
     */
    private function collectProjectChoices($students, $id) {
        $studs = array();
        // \Log::info($students);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get(\Config::get('values.default_canvas_url') . 'courses/' . $id . '/quizzes?search_term=Survey');
        // \Log::info(json_decode($response->body())->quiz_statistics_url);
        $url = json_decode($response->body())[0]->quiz_statistics_url;
        // \Log::info($url);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . Cache::get('key')
        ])->get($url);
        
        // Logs the first question
        // \Log::info(json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics']);
        $choices = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'][1]['answers'];

        

        foreach($students as $student) {
            $studs[$student->id] = new Student($student);
        }

        //\Log::info(array_keys($studs));

        $questions = json_decode($response->body(), true)['quiz_statistics'][0]['question_statistics'];
        for($i = 1; $i<4; $i++) {
            // \Log::info($questions[$i]['question_text']);
            // \Log::info(strval($i) . ' Choice');
            $question = $questions[$i];
            foreach($question['answers'] as $choice) {
                //\Log::info($choice['text']);
                //\Log::info($choice['user_names']);
                foreach($choice['user_ids'] as $studentId) {
                    $studs[$studentId]->setAttribute($i, $choice['text']);
                }
            }

        }
        return $studs;
    }

    /**
     * Aggregate students into groups based on projectChoice
     *
     * The function checks the value of `$madeByNumGroups` to determine the grouping method. If it is set to `true`, the groups
     * are created based on the number of groups; otherwise, they are created based on the group size. The function calls the
     * respective grouping function (`aggregateByGroupQty()` or `aggregateByGroupSize()`) to generate the groups.
     *
     * Finally, the function returns the resulting groups.
     * 
     * @param int $size The group size or number of groups.
     * @param string $madeByNumGroups A string indicating whether groups are made by the number of groups or group size.
     * @param int $id The ID of the course.
     * @param array $studs The array of students.
     * @return array The resulting groups.
     */
    public function aggregate($size, $madeByNumGroups, $id, $studs) {
        if ($madeByNumGroups == 'true') {
            $groups = aggregateByGroupQty($studs, $size);
        } else {
            $groups = aggregateByGroupSize($studs, $size);
        }

        return $groups;
        // $groupSetId = $this->getGroupCategoryIdFromProject();
        // $this->buildGroups($groupSetId, $groups);

        // $i = 1;
        // foreach ($groups as $group) {
        //     \Log::info("Group " . $i . ":");
        //     foreach ($group->getStudents() as $student) {
        //         \Log::info($student->getName() . ": " . implode($student->getAttributes()));
        //     }
        //     $i++;
        //     \Log::info(" ");
        // }
        // \Log::info(aggregationSuccess($groups, $studs));

    }

    /**
     * Initialize the building of groups.
     * 
     * Directly called by the buildGroups route
     *
     * This function initializes the building of groups by retrieving the groups from the cache, obtaining the group category ID
     * from the project, and calling the `buildGroups()` function. Finally, it redirects to the `getGroups` route with the project ID.
     *
     * @return \Illuminate\Http\RedirectResponse The redirect response to the `getGroups` route.
     */
    public function initBuild() {
        $groups = Cache::get('groups');
        $id = $this->getGroupCategoryIdFromProject();
        $this->buildGroups($id, $groups);
        return redirect()->route('getGroups', json_decode(Cache::get('project'))->id);
    }

    private function buildGroups($id, $groups) {
        ///group_categories/10530000000041664/groups
        $headers = ['Authorization' => 'Bearer ' . Cache::get('key')];
        $promises = [];
        $endpoint = \Config::get('values.default_canvas_url') . 'group_categories/' . $id . '/groups';
        for ($i = 1; $i <= count($groups); $i++) {
            $promises[] = Http::withHeaders($headers)->async()->post($endpoint, ['name' => 'Group ' . $i,]);
        }
        $results = collect($promises)->map(function ($promise) {
            return $promise->wait();
        });
        $groupIds = [];
        foreach ($results as $result) {
            array_push($groupIds, json_decode($result->body(), true)['id']);
            // \Log::info($result->header('X-Rate-Limit-Remaining'));
        }
        // $results->each(function ($response) use($groupIds) {
        //     // \Log::info(json_decode($response->body(), true));
        //     array_push($groupIds, json_decode($response->body(), true)['id']);
        // });
        $this->addStudents($groupIds, $headers, $groups);
    }

    /**
     * Add students to groups.
     *
     * This function adds students to the groups specified by their group IDs. It sends asynchronous POST requests to the Canvas API
     * to add each student to their respective group.
     *
     * @param array $groupIds The array of group IDs.
     * @param array $headers The headers to be included in the API requests.
     * @param array $groups The array of Group objects containing the students to be added.
     * @return void
     */
    private function addStudents($groupIds, $headers, $groups) {

        $promises = [];
        for ($i = 0; $i < count($groups); $i++) {
            $endpoint = \Config::get('values.default_canvas_url') . 'groups/' . $groupIds[$i] . '/memberships';
            $students = $groups[$i]->getStudents();
            foreach ($students as $student) {
                $promises[] = Http::withHeaders($headers)->async()->post($endpoint, ['user_id' => $student->getStudId(),]);
            }
            $results = collect($promises)->map(function ($promise) {
                return $promise->wait();
            });
            // $results->each(function ($response) {
            //     \Log::info($response->header('X-Rate-Limit-Remaining'));
            // });
        }
    }

    /**
     * Get all students from groups.
     *
     * This function retrieves all the students from the given array of Group objects.
     *
     * @param array $groups The array of Group objects.
     * @return array The array of Student objects representing all the students from the groups.
     */
    private function getAllStudentsFromGroups($groups) {
        $students = array();
        foreach ($groups as $group) {
            $students = array_merge($students, $group->getStudents());
        }
        return $students;
    }

}
