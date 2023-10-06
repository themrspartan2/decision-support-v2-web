<?php declare(strict_types=1);

use App\Classes\Student;
use App\Classes\Group;

/**
 * Clusters minority students in a group configuration given a list of students and the size of each group.
 * 
 * @param array $studList The array of students in the course.
 * @param int $groupSize The maximum number of students allowed in each group.
 * @return array An array of gender-clustered groups.
 */
function clusterByGroupSize(array $studList, int $groupSize) {
    $qtyOfGroups = (int) ceil(sizeof($studList) / $groupSize);
    return clusterByGroupQty($studList, $qtyOfGroups);
}

/**
 * Clusters minority students in a group configuration given a list of students and the number of groups.
 * 
 * @param array $studList The array of students in the course.
 * @param int $qtyOfGroups The maximum number of students allowed in each group.
 * @return array An array of gender-clustered groups.
 */
function clusterByGroupQty(array $studList, int $qtyOfGroups) {
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

    // Create a list of minority students and a list of majority students.
    $unassignedMinorities = createMinorityList($unassignedStuds);
    $unassignedMajorities = createMajorityList($unassignedStuds);

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
 * 
 * @param array $studList The array of students in the course.
 * @return array An array of students with a minoStatus of 'true'.
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
 * 
 * @param array $studList The array of students in the course.
 * @return array An array of students with a minoStatus of 'false'.
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
 * 
 * @param array $groups The array of groups to be rated.
 * @param array $studList The array of students in the course.
 * @return float The rating for how well the groups are minority-clustered as a percentage.
 */
function clusteringSuccess(array $groups, array $studList) {
    if ($groups[0]->getLimit() <= 2) {
        return 100;
    }
    $success = 100;
    foreach ($groups as $group) {
        $minorityCounter = 0;
        for ($i = 0; $i < $group->getCurrSize(); $i++) {
            if ($group->getStudent($i)->getMinoStatus()) {
                $minorityCounter++;
            }
        }
        if ($minorityCounter == 1) {
            $success -= (100 / sizeOf($groups));
        }
    }
    return $success;
}

?>
