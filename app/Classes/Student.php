<?php declare(strict_types=1);

namespace App\Classes;

/**
 * Student Class
 * Creates Student objects with a name, grade, minority status, and attributes for aggregation
 */
class Student {

    // ========================================= Properties
    private $jsonStudArray; // The json array for this student from the Canvas API.
    private $studId; // The student ID of this student from the Canvas API.
    private $name; // The name of tis student from the Canvas API.
    private $grade; // The course grade of this student.
    private $minoStatus; // The gender minority status of this student. Currently, this is true if they are male, and false otherwise.
    private $genderString; // The gender of the Student either "Male", "Female", or "Other".
    private $attributes; // The array of this students attributes to aggregate with. Currently, this is an array of their project choices.

    // ========================================= Constructor
    function __construct($jsonStudArray, float $grade = 0, bool $minoStatus = false, array $attributes = array('none', 'none', 'none')) {
        $this->jsonStudArray = $jsonStudArray;
        $this->studId = $jsonStudArray->id;
        $this->name = $jsonStudArray->name;
        $this->grade = $grade;
        $this->minoStatus = $minoStatus;
        $this->attributes = $attributes;
        $this->genderString = "Unknown";
    }
    
    // ========================================= Getters / Setters

    /**
     * A getter for the json array of this student.
     * 
     * @return array The json array of this student.
     */
    function getJsonStudArray() {
        return $this->jsonStudArray;
    }

    /**
     * A getter for the student ID of this student.
     * 
     * @return int The student ID of this student.
     */
    function getStudId() {
        return $this->studId;
    }

    /**
     * A getter for this student's name.
     * 
     * @return string The name of this student.
     */
    function getName() {
    	return $this->name;
    }

    /**
     * A getter for the course grade of this student.
     * 
     * @return float The course grade of this student.
     */
    function getGrade() {
        return $this->grade;
    }

    /**
     * A getter for the gender minority status of this student.
     * 
     * @return bool The gender minority status of this student.
     */
    function getMinoStatus() {
        return $this->minoStatus;
    }

    /**
     * A getter for the project choices array of this student.
     * 
     * @return array The array of this student's project choices.
     */
    function getAttributes() {
        return $this->attributes;
    }

    /**
     * A setter for this student's student ID.
     * 
     * @param int $studId The new student ID of this student.
     */
    function setStudId(int $studId) {
        $this->studId = $studId;
    }

    /**
     * A setter for this student's name.
     * 
     * @param string $name The new name of this student.
     */
    function setName(string $name) {
        $this->name = $name;
    }

    /**
     * A setter for the course grade of this student.
     * 
     * @param float $grade The new course grade of this student.
     */
    function setGrade(float $grade) {
        $this->grade = $grade;
    }

    /**
     * A setter for the gender minority status of this student.
     * 
     * @param bool $minoStatus The new gender minority status of this student.
     */
    function setMinoStatus(bool $minoStatus) {
        $this->minoStatus = $minoStatus;
    }

    /**
     * A setter for the project choice array of this student.
     * 
     * @param array $attributes The new project choice array of this student.
     */
    function setAttributes(array $attributes) {
        $this->attributes = $attributes;
    }

    /**
     * A setter for an individual project choice within the attributes array of this student.
     * 
     * @param int $num The location of the attribute in the array to be replaced.
     * @param string $attribute The new value of the attribute being replaced.
     */
    function setAttribute(int $num, string $attribute) {
        $this->attributes[$num - 1] = $attribute;
    }

    /**
     * A setter for the gender string of the student.
     * 
     * @param string $gender The gender string pulled from the survey
     */
    function setGenderString(String $gender) {
        $this->genderString = $gender;
    }

    /**
     * A getter for the gender string of the student.
     * 
     * @return string The gender string pulled from the survey.
     */
    function getGenderString() {
        return $this->genderString;
    }
}