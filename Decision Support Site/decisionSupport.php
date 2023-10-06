<?php declare(strict_types=1);

/**
 * Student Class
 * Creates Student objects with a name, grade, minority status, and attributes for aggregation
 */
class Student {

    // ========================================= Properties
    private $name;
    private $grade;
    private $minoStatus;
    private $attributes;

    // ========================================= Constructor / Destructor
    function __construct(string $name, float $grade, bool $minoStatus, array $attributes = array()) {
        $this->name = $name;
        $this->grade = $grade;
        $this->minoStatus = $minoStatus;
        $this->attributes = $attributes;
    }
    
    // ========================================= Getters / Setters
    function getName() {
    	return $this->name;
    }

    function getGrade() {
        return $this->grade;
    }

    function getMinoStatus() {
        return $this->minoStatus;
    }

    function getAttributes() {
        return $this->attributes;
    }
}

/**
 * Group Class
 * Creates group objects in which to store student group configurations.
 */
class Group {

    // ========================================= Properties
    private $students;
    private $limit;
    private $currSize;

    // ========================================= Constructor
    function __construct(int $limit) {
        $this->students = array();
        $this->limit = $limit;
        $this->currSize = 0;
    }

    // ========================================= Methods
    function addStudent(Student $s) {
        if ($this->currSize < $this->limit) {
            $this->students[$this->currSize] = $s;
            $this->currSize++;
        } else {
            // Throw OutOfBounds or some equivalent idk
        }
    }

    function isFull() {
        return $this->currSize == $this->limit;
    }

    function isEmpty() {
        return $this->currSize == 0;
    }

    function getAvgGrade() {
        $sum = 0;
        foreach ($this->students as $s) {
            $sum += $s->getGrade();
        }
        return $sum / $this->currSize;
    }

    function getGradeSum() {
        $sum = 0;
        foreach ($this->students as $s) {
            $sum += $s->getGrade();
        }
        return $sum;
    }

    function replaceStudent(int $i, student $s) {
        if ($i < $this->currSize && $i >= 0) {
            $this->students[$i] = $s;
        } else {
            // Throw IllegalArgument or some equivalent idk
        }
    }

    // ========================================= Getters / Setters
    function getLimit() {
        return $this->limit;
    }

    function getCurrSize() {
        return $this->currSize;
    }

    function getStudent(int $i) {
        return $this->students[$i];
    }
}

// ============================================================= Clustering

/**
 * Clusters minority students in a group configuration given a list of students and the size of each group.
 */
function clusterByGroupSize(array $studList, int $groupSize) {
    $qtyOfGroups = (int) ceil(sizeof($studList) / $groupSize);
    return clusterByGroupQty($studList, $qtyOfGroups);
}

/**
 * Clusters minority students in a group configuration given a list of students and the number of groups.
 */
function clusterByGroupQty(array $studList, int $qtyOfGroups) {
    $groupSizeLimit = (int) ceil(sizeof($studList) / $qtyOfGroups);

    // Create an array of empty groups.
    $groups = array();
    for ($i = 0; $i < $qtyOfGroups; $i++) {
        $groups[$i] = new Group($groupSizeLimit);
    }

    // Create a list of minority students and a list of majority students.
    $unassignedMinorities = createMinorityList($studList);
    $unassignedMajorities = createMajorityList($studList);

    // Add two minority students to each group if possible.
    foreach ($groups as $group) {
        // If there are zero students to add, break.
        if (sizeof($unassignedMinorities) <= 0) {
            break;
        }
        // If there is one student to add, add them to the first group if possible, put them in a group alone otherwise.
        if (sizeof($unassignedMinorities) == 1) {
            if ($groupSizeLimit == 2) {
                $group->addStudent(array_pop($unassignedMinorities));
            } else {
                $groups[0]->addStudent(array_pop($unassignedMinorities));
            }
        }
        // If there are two or more students to add, add two to a new group.
        else {
            $group->addStudent(array_pop($unassignedMinorities));
            $group->addStudent(array_pop($unassignedMinorities));
        }
    }

    // If there are still minority students to add, use them to fill out groups.
    while (!empty($unassignedMinorities)) {
        foreach ($groups as $group) {
            $group->addStudent(array_pop($unassignedMinorities));
            if (empty($unassignedMinorities)) {
                break;
            }
        }
    }

    // Add majority students to groups with zero students.
    foreach ($groups as $group) {
        if ($group->getCurrSize() == 0 && !$group->isFull() && !empty($unassignedMajorities)) {
            $group->addStudent(array_pop($unassignedMajorities));
        }
    }

    // Add majority students to groups with one student.
    foreach ($groups as $group) {
        if ($group->getCurrSize() == 1 && !$group->isFull() && !empty($unassignedMajorities)) {
            $group->addStudent(array_pop($unassignedMajorities));
        }
    }

    // Fill out remaining empty slots with majority students.
    // Add them from the last group to the first so that students are added to smaller groups first.
    while (!empty($unassignedMajorities)) {
        for ($i = sizeof($groups) - 1; $i >= 0; $i--) {
            if (!$groups[$i]->isFull() && !empty($unassignedMajorities)) {
                $groups[$i]->addStudent(array_pop($unassignedMajorities));
            }
        }
    }

    return $groups;
}

/**
 * Returns a list of students from the student list with a minoStatus of 'true'.
 */
function createMinorityList(array $studList) {
    $minorityList = array();
    $j = 0;
    foreach ($studList as $s) {
        if ($s->getMinoStatus()) {
            $minorityList[$j++] = $s;
        }
    }
    return $minorityList;
}

/**
 * Returns a list of students from the student list with a minoStatus of 'false'.
 */
function createMajorityList(array $studList) {
    $majorityList = array();
    $j = 0;
    foreach ($studList as $s) {
        if (!$s->getMinoStatus()) {
            $majorityList[$j++] = $s;
        }
    }
    return $majorityList;
}

/**
 * Returns a rating for how well an array of groups is minority-clustered.
 * 0 is the worst rating, and 100 is the best.
 */
function clusteringSuccess(array $groups, array $studList) {
    if ($groups[0]->getLimit() <= 2) {
        return 100;
    }
    foreach ($groups as $group) {
        $minorityCounter = 0;
        for ($i = 0; $i < $group->getCurrSize(); $i++) {
            if ($group->getStudent($i)->getMinoStatus()) {
                $minorityCounter++;
            }
        }
        if ($minorityCounter == 1) {
            return 0;
        }
    }
    return 100;
}

// ============================================================= Balancing

/**
 * Generates a grade-balanced group configuration given a list of students and the size of each group.
 */
function balanceByGroupSize(array $studList, int $groupSize) {
    $qtyOfGroups = (int) ceil(sizeof($studList) / $groupSize);
    return balanceByGroupQty($studList, $qtyOfGroups);
}

/**
 * Generates a grade-balanced group configuration given a list of students and the number of groups.
 */
function balanceByGroupQty(array $studList, int $qtyOfGroups) {
    $groupSizeLimit = (int) ceil(sizeof($studList) / $qtyOfGroups);

    // Copy studList into an array that can be drained.
    $unassignedStuds = array();
    for ($i = 0; $i < sizeof($studList); $i++) {
        $unassignedStuds[$i] = $studList[$i];
    }

    // Create an array of empty groups.
    $groups = array();
    for ($i = 0; $i < $qtyOfGroups; $i++) {
        $groups[$i] = new Group($groupSizeLimit);
    }

    // Calculate the average grade of all students.
    $avgGrade = calcAvgGrade($unassignedStuds);

    // Assign one student to each group
    foreach ($groups as $group) {
        if (empty($unassignedStuds)) {
            break;
        }
        $group->addStudent(array_pop($unassignedStuds));
    }

    // For each group, pick the most optimal student and add them to the group.
    // Repeat until there are no more students to add.
    while (!empty($unassignedStuds)) {
        foreach ($groups as $group) {
            $deviation = INF;
            $newStud = null;
            $newStudIndex = -1;
            for ($i = 0; $i < sizeof($unassignedStuds); $i++) {
                $groupAvg = ($group->getGradeSum() + $unassignedStuds[$i]->getGrade()) / ($group->getCurrSize() + 1);
                if (abs($groupAvg - $avgGrade) < $deviation) {
                    $deviation = abs($groupAvg - $avgGrade);
                    $newStud = $unassignedStuds[$i];
                    $newStudIndex = $i;
                }
            }
            if ($newStud != null) {
                $group->addStudent($newStud);
                array_splice($unassignedStuds, $newStudIndex, 1);
            }
        }
    }

    return $groups;
}

/**
 * Calculates the average grade from a list of students.
 */
function calcAvgGrade(array $studList) {
    $sum = 0;
    foreach ($studList as $s) {
        $sum += $s->getGrade();
    }
    return $sum / sizeof($studList);
}

/**
 * Returns a rating for how well an array of groups is grade-balanced.
 * 0 is the worst rating, and 100 is the best.
 */
function balancingSuccess(array $groups, array $studList) {

    // Creating the worst groups known to man (this will have a rating of 0).
    $qtyOfGroups = sizeof($groups);
    $groupSizeLimit = $groups[0]->getLimit();
    $badGroups = array();
    for ($i = 0; $i < $qtyOfGroups; $i++) {
        $badGroups[$i] = new Group($groupSizeLimit);
    }
    $ascList = sortStudsAsc($studList);
    foreach ($badGroups as $group) {
        while (!empty($ascList) && !$group->isFull()) {
            $group->addStudent(array_pop($ascList));
        }
        if (empty($ascList)) {
            break;
        }
    }

    return 100 - (100 * calcStdDev($groups, $studList) / calcStdDev($badGroups, $studList));
}

/**
 * Returns an array containing the students sorted by grade in ascending order.
 */
function sortStudsAsc(array $studList) {
    $ret = array();
    $unassignedStuds = array();
    for ($i = 0; $i < sizeof($studList); $i++) {
        $unassignedStuds[$i] = $studList[$i];
    }
    while (!empty($unassignedStuds)) {
        $lowestGrade = INF;
        $lowestStud = null;
        $lowestStudIndex = -1;
        for ($i = 0; $i < sizeof($unassignedStuds); $i++) {
            if ($unassignedStuds[$i]->getGrade() < $lowestGrade) {
                $lowestGrade = $unassignedStuds[$i]->getGrade();
                $lowestStud = $unassignedStuds[$i];
                $lowestStudIndex = $i;
            }
        }
        array_push($ret, $lowestStud);
        array_splice($unassignedStuds, $lowestStudIndex, 1);
    }
    return $ret;
}

/**
 * Calculates the standard deviation of the average group grades from the total class grade average.
 */
function calcStdDev(array $groups, array $studList) {
    $ret = 0;
    $sum = 0;
    foreach ($studList as $s) {
        $sum += $s->getGrade();
    }

    $mean = $sum / sizeof($studList);

    foreach ($groups as $group) {
        $ret += ($group->getAvgGrade() - $mean) ** 2;
    }

    return sqrt($ret / sizeof($groups));
}

// ============================================================= Aggregation

/**
 * Generates a group configuration with students aggregated by their attributes given a list of students and the size of each group.
 */
function aggregateByGroupSize(array $studList, int $groupSize) {
    $qtyOfGroups = (int) ceil(sizeof($studList) / $groupSize);
    return aggregateByGroupQty($studList, $qtyOfGroups);
}

/**
 * Generates a group configuration with students aggregated by their attributes given a list of students and the number of groups.
 */
function aggregateByGroupQty(array $studList, int $qtyOfGroups) {
    $groupSizeLimit = (int) ceil(sizeof($studList) / $qtyOfGroups);
    
    // Copy studList into an array that can be drained.
    $unassignedStuds = array();
    for ($i = 0; $i < sizeof($studList); $i++) {
        $unassignedStuds[$i] = $studList[$i];
    }

    // Create an array of empty groups.
    $groups = array();
    for ($i = 0; $i < $qtyOfGroups; $i++) {
        $groups[$i] = new Group($groupSizeLimit);
    }

    // Fill each group with students that have similar attributes.
    while (!empty($unassignedStuds)) {

        // Find the first empty group.
        $group = null;
        for ($i = 0; $i < $qtyOfGroups; $i++) {
            if (empty($groups[$i])) {
                $group = $groups[$i];
                break;
            }
        }

        // Add the last student from the list of unassigned students to the first empty group.
        $s = array_pop($unassignedStuds);
        $group->addStudent($s);

        // TODO this opens up the possibility for a group of one, but prevents an error.
        if (empty($unassignedStuds)) {
            break;
        }

        // Fill the group with similar students
        $unassignedStuds = sortStudsByAttr($unassignedStuds, $s);
        while (!$group->isFull()) {
            if (empty($unassignedStuds)) {
                break;
            }
            $group->addStudent(array_pop($unassignedStuds));
        }
    }
    
    return $groups;
}

/**
 * Sorts a student array such that the students at the end of the array have similar attributes to a given student.
 */
function sortStudsByAttr(array $studList, Student $s) {
    $attrs = $s->getAttributes();

    // Copy studList into an array that can be drained.
    $unassignedStuds = array();
    for ($i = 0; $i < sizeof($studList); $i++) {
        $unassignedStuds[$i] = $studList[$i];
    }

    // For every attribute, from lowest to highest priority...
    for ($a = sizeof($attrs) - 1; $a >= 0; $a--) {

        // Search unassignedStuds from beginning to end. If the current attribute of a student in unassignedStuds
        // DOES NOT match that of s, add that student to the end of tmp.
        $tmp = array();
        for ($i = 0; $i < sizeof($unassignedStuds); $i++) {
            if ($attrs[$a] != $unassignedStuds[$i]->getAttributes()[$a]) {
                array_push($tmp, $unassignedStuds[$i]);
            }
        }
        // Find the students from unassignedStuds where the value of their current attribute DOES match
        // that of s. Add them to tmp in order, then set unassignedStuds equal to tmp.
        for ($i = 0; $i < sizeof($unassignedStuds); $i++) {
            if ($attrs[$a] == $unassignedStuds[$i]->getAttributes()[$a]) {
                array_push($tmp, $unassignedStuds[$i]);
            }
        }
        $unassignedStuds = $tmp;
    }

    return $unassignedStuds;
}

?>
