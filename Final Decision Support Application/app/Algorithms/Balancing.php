<?php declare(strict_types=1);

use App\Classes\Student;
use App\Classes\Group;

/**
 * Generates a grade-balanced group configuration given a list of students and the size of each group.
 * 
 * @param array $studList The array of students in the course.
 * @param int $groupSize The maximum number of students allowed in each group.
 * @return array An array of grade-balanced groups.
 */
function balanceByGroupSize(array $studList, int $groupSize) {
    $qtyOfGroups = (int) ceil(sizeof($studList) / $groupSize);
    return balanceByGroupQty($studList, $qtyOfGroups);
}

/**
 * Generates a grade-balanced group configuration given a list of students and the number of groups.
 * 
 * @param array $studList The array of students in the course.
 * @param int $qtyOfGroups The maximum number of students allowed in each group.
 * @return array An array of grade-balanced groups.
 */
function balanceByGroupQty(array $studList, int $qtyOfGroups) {
    $groupSizeLimit = (int) ceil(sizeof($studList) / $qtyOfGroups);

    // Copy studList into an array that can be drained
    $unassignedStuds = array();
    $i = 0;
    foreach ($studList as $stud) {
        $unassignedStuds[$i] = $stud;
        $i++;
    }

    // Randomize the order of elements in unassignedStuds.
    shuffle($unassignedStuds);

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
 * 
 * @param array $studList The array of students in the course.
 * @return float The average grade of all students in the course.
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
 * 
 * @param array $groups The array of groups to be rated.
 * @param array $studList The array of students in the course.
 * @return float The rating for how well the groups are grade-balanced as a percentage.
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
 * 
 * @param array $studList The array of students in the course.
 * @return array The array of students sorted by grade in ascending order.
 */
function sortStudsAsc(array $studList) {
    $ret = array();
    $unassignedStuds = array();
    $i = 0;
    foreach ($studList as $stud) {
        $unassignedStuds[$i] = $stud;
        $i++;
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
 * 
 * @param array $groups The array of groups to be rated.
 * @param array $studList The array of students in the course.
 * @return float The standard deviation of the average group grades from the average grade of all students in the class.
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

?>
