<?php declare(strict_types=1);

use App\Classes\Student;
use App\Classes\Group;

/**
 * Generates a randomized group configuration given a list of students and the size of each group.
 * 
 * @param array $studList The array of students in the course.
 * @param int $groupSize The maximum number of students allowed in each group.
 * @return array An array of randomized groups.
 */
function randomizeByGroupSize(array $studList, int $groupSize) {
    $qtyOfGroups = (int) ceil(sizeof($studList) / $groupSize);
    return randomizeByGroupQty($studList, $qtyOfGroups);
}

/**
 * Generates a randomized group configuration given a list of students and the number of groups.
 * 
 * @param array $studList The array of students in the course.
 * @param int $qtyOfGroups The maximum number of students allowed in each group.
 * @return array An array of randomized groups.
 */
function randomizeByGroupQty(array $studList, int $qtyOfGroups) {
    $groupSizeLimit = (int) ceil(sizeof($studList) / $qtyOfGroups);
    
    // Copy studList into an array that can be drained.
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

    // Add one student to each group, repeat until there are no more students to add.
    while (!empty($unassignedStuds)) {
        foreach ($groups as $group) {
            if (empty($unassignedStuds)) {
                break;
            }
            $group->addStudent(array_pop($unassignedStuds));
        }
    }

    return $groups;
}
