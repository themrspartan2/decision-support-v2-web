<?php declare(strict_types=1);

use App\Classes\Student;
use App\Classes\Group;

/**
 * Generates a group configuration with students aggregated by their attributes
 * when given a list of students and the size of each group.
 * 
 * @param array $studList The array of students in the course.
 * @param int $groupSize The maximum number of students allowed in each group.
 * @return array An array of project choice-aggregated groups.
 */
function aggregateByGroupSize(array $studList, int $groupSize) {
    $qtyOfGroups = (int) ceil(sizeof($studList) / $groupSize);
    return aggregateByGroupQty($studList, $qtyOfGroups);
}

/**
 * Generates a group configuration with students aggregated by their attributes
 * when given a list of students and the number of groups.
 * 
 * @param array $studList The array of students in the course.
 * @param int $qtyOfGroups The maximum number of students allowed in each group.
 * @return array An array of project choice-aggregated groups.
 */
function aggregateByGroupQty(array $studList, int $qtyOfGroups) {
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

    // Correct the group size limits so that there is a good distribution of students.
    if ((sizeof($unassignedStuds) % $groupSizeLimit) < ($groupSizeLimit - 1) && (sizeof($unassignedStuds) % $groupSizeLimit) > 0) {
        $qtyOfMissingStuds = $groupSizeLimit - (sizeof($unassignedStuds) % $groupSizeLimit);
        if ($qtyOfMissingStuds < $qtyOfGroups) {
            $qtyOfBrokenGroups = $qtyOfMissingStuds;
        } else {
            $qtyOfBrokenGroups = $qtyOfGroups - 1;
        }
        for ($i = 0; $i < $qtyOfBrokenGroups; $i++) {
            $groups[$i]->setLimit($groupSizeLimit - (int) floor($qtyOfMissingStuds / $qtyOfBrokenGroups));
        }
    }

    // Fill each group with students that have similar attributes.
    while (!empty($unassignedStuds)) {

        // Find the first empty group.
        $group = null;
        for ($i = 0; $i < $qtyOfGroups; $i++) {
            if ($groups[$i]->isEmpty()) {
                $group = $groups[$i];
                break;
            }
        }

        // Add the last student from the list of unassigned students to the first empty group.
        $s = array_pop($unassignedStuds);
        $group->addStudent($s);

        // If the list of students is empty, break the loop here.
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
 * 
 * @param array $studList The array of students in the course.
 * @param Student $s The student list will be sorted based on this student's attributes.
 * @return array An array of students sorted by their similarity to $s, with most similar students at the end of the array.
 */
function sortStudsByAttr(array $studList, Student $s) {
    $attrs = $s->getAttributes();

    // Copy studList into an array that can be drained.
    $unassignedStuds = array();
    $i = 0;
    foreach ($studList as $stud) {
        $unassignedStuds[$i] = $stud;
        $i++;
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

/**
 * Returns a rating for how well an array of groups is aggregated.
 * 0 is the worst rating, and 100 is the best.
 * 
 * @param array $groups The array of groups to be rated.
 * @param array $studList The array of students in the course.
 * @return float The rating for how well the groups are project choice-aggregated as a percentage.
 */
function aggregationSuccess(array $groups, array $studList) {
    $numOfAttributes = sizeOf($groups[0]->getStudent(0)->getAttributes());
    $successScore = 0;

    $maxSuccessScore = 0;
    foreach ($groups as $group) {
        $maxSuccessScore += (($group->getCurrSize() - 1) * attributeSummation($numOfAttributes));
    }

    // Increase the success score for each different value for an attribute in the same group.
    foreach ($groups as $group) {
        for ($attributeIndex = 0; $attributeIndex < $numOfAttributes; $attributeIndex++) {

            // Add each different attribute value from this group into an array.
            $currValues = array();
            for ($studIndex = 0; $studIndex < $group->getCurrSize(); $studIndex++) {
                if (!in_array($group->getStudent($studIndex)->getAttributes()[$attributeIndex], $currValues)) {
                    array_push($currValues, $group->getStudent($studIndex)->getAttributes()[$attributeIndex]);
                }
            }

            // The size of the array of attribute values is the number of different values for that attribute in this group.
            $numOfValues = sizeOf($currValues);

            // For each different value, add to the total success score for this group set.
            $successScore += ($numOfValues - 1) * ($numOfAttributes - $attributeIndex);
        }
    }

    // Return the success score on a scale where the perfect group set had all the same attribute values within a group,
    // and the worst possible score is that of the maximum success score.
    return 100 - (100 * ($successScore / $maxSuccessScore));
}

/**
 * A helper method for aggregationSuccess().
 * Uses recursion to determine the sum of an integer and all positive integers less than it.
 * 
 * @param int $numOfAttributes The starting value of the summation.
 * @return int The sum of the starting value and all positive integgers less than it.
 */
function attributeSummation(int $numOfAttributes) {
    if ($numOfAttributes <= 1) {
        return 1;
    }
    return $numOfAttributes + attributeSummation($numOfAttributes - 1);
}

?>
