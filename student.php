<?php

require_once('constants.php');
require_once('debug.php');

abstract class Student {
    private $name;
    private $crsid;
    private $college;

    public function Student($name, $crsid, $college) {
        setName($name);
        setCrsid($crsid);
        setCollege($college);
    }

    /* Not sure if this will ever be used.
     * If not, delete, otherwise test it.
    */
    public function equals($obj) {
        if ($obj instanceof Student) {
            return $obj->crsid === $this->crsid;
        }
        return false;
    }

    public function getName() {
        return $this->name;
    }

    public function getCrsid() {
        return $this->crsid;
    }

    public function getCollege() {
        return $this->college;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setCrsid($crsid) {
        $this->crsid = $crsid;
        return $this;
    }

    public function setCollege($college) {
        // Ensure we're assigning an actual Cambridge college
        if (!College::isValid($college)) {
            Debug::log(Debug::InvalidAssignment, $college, 'Student::setCollege');
            return false;
        } 
        $this->college = $college;
        return $this;
    }
}

?>
