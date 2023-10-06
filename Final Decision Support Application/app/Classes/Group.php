<?php declare(strict_types=1);

namespace App\Classes;

/**
 * Group Class
 * Creates group objects in which to store student group configurations.
 */
class Group {

    // ========================================= Properties
    private $students; // The array of students in this group.
    private $limit; // The maximum number of students allowed in this group.
    private $currSize; // The current number of students in this group.

    // ========================================= Constructor
    function __construct(int $limit) {
        $this->students = array();
        $this->limit = $limit;
        $this->currSize = 0;
    }

    // ========================================= Methods

    /**
     * Adds a student to this group if there is room in the group.
     * 
     * @param Student $s The student to add to the group.
     */
    function addStudent(Student $s) {
        if ($this->currSize < $this->limit) {
            $this->students[$this->currSize] = $s;
            $this->currSize++;
        } else {
            // Do not add the student
        }
    }

    /**
     * Determines if this group is full.
     * 
     * @return bool true if the group is full; false otherwise.
     */
    function isFull() {
        return $this->currSize == $this->limit;
    }

    /**
     * Determines if this group is empty.
     * 
     * @return bool true if the group is empty; false otherwise.
     */
    function isEmpty() {
        return $this->currSize == 0;
    }

    /**
     * Gets the sum of all student's grades in this group.
     * 
     * @return float The sum of all student's grades in this group.
     */
    function getGradeSum() {
        $sum = 0;
        foreach ($this->students as $s) {
            $sum += $s->getGrade();
        }
        return $sum;
    }

    /**
     * Replaces a student in this group with another student.
     * 
     * @param int $i The index in this group where the new student will replace the existing one.
     * @param Student $s The student to add to this group.
     */
    function replaceStudent(int $i, Student $s) {
        if ($i < $this->currSize && $i >= 0) {
            $this->students[$i] = $s;
        } else {
            // Do nothing
        }
    }

    /**
     * Gets the average grade of all students in this group.
     * 
     * @return float The average grade of all students in this group.
     */
    function getAvgGrade() {
        $sum = 0;
        foreach ($this->students as $s) {
            $sum += $s->getGrade();
        }
        if ($this->currSize == 0) {
            return 0;
        }
        return $sum / $this->currSize;
    }

    /**
     * Gets the three most common project choices among the students in this group.
     * 
     * @return array An array containing the three most common project choices in this group.
     */
    function getTop3Choices() {
        $frequency = [];
        foreach($this->students as $student) {
            foreach($student->getAttributes() as $choice) {
                if ($choice == 'none') {
                    continue;
                }
                if (isset($frequency[$choice])) {
                    $frequency[$choice]++;
                } else {
                    $frequency[$choice] = 1;
                }
            }
        }
        arsort($frequency);
        return array_slice(array_keys($frequency), 0, 3);
    }

    // ========================================= Getters / Setters

    /**
     * A getter for the array of students in this group.
     * 
     * @return array the array of students in this group.
     */
    function getStudents() {
        return $this->students;
    }

    /**
     * A getter for the size limit of this group.
     * 
     * @return int The size limit for this group.
     */
    function getLimit() {
        return $this->limit;
    }

    /**
     * A getter for the current size of this group.
     * 
     * @return int The number of students currently in this group.
     */
    function getCurrSize() {
        return $this->currSize;
    }

    /**
     * A getter for a specific student in this group.
     * 
     * @param int $i The index of the student to return.
     * @return Student The student at the specified index.
     */
    function getStudent(int $i) {
        return $this->students[$i];
    }

    /**
     * A setter for the size limit of this group.
     * 
     * @param int $limit The new size limit for this group.
     */
    function setLimit(int $limit) {
        $this->limit = $limit;
    }
}