<?php

require_once('student.php');
require_once('constants.php');
require_once('debug.php');

/*
 * TODO Add save function to save $this to DB
*/

class Worker extends Student {
    private $role;
    private $shift;
    private $is_pic_uploaded;
    
    public Worker($name, $crsid, $college, $role, $shift, $is_pic_uploaded) {
        parent::Student($name, $crsid, $college);
        setRole($role);
        setRank($shift);
        setPicUploaded($is_pic_uploaded);
    }

    public function getRole() {
        return $this->role;
    }

    public function getShift() {
        return $shift;
    }

    public function isPicUploaded() {
        return $is_pic_uploaded;
    }

    public function setRole($role) {
        if (!WorkerType::isValid($role)) {
            Debug::log(Debug::InvalidAssignment, $role, 'Worker::setRole');
            return false;
        }
        $this->role = $role;
        return $this;
    }

    public function setShift($shift) {
        if (!Shift::isValid($shift)) {
            Debug::log(Debug::InvalidAssignment, $shift, 'Worker::setShift');
            return false;
        }
        $this->shift = $shift;
        return $this;
    }

    public function setNotes($notes) {
        $this->notes = $notes;
        return $this;
    }

    private static function makeApplicantFromRow($row) {
        return new Applicant(
              $row->name,
              $row->crsid,
              $row->college,
              array($row->role1, $row->role2, $row->role3),
              $row->rank,
              $row->notes
            );
    }
    
    /* If $crsid is null then return an array of all applicants in the DB.
     * If $crsid is an array then return an array of all applicants whose
     * CRSid is in the $crsid array.
     * Otherwise return the single Applicant object whose CRSid is $crsid.
    */
    public static function load($crsid) {
        global $wpdb;
        if (is_null($crsid)) {
            $rows = $wpdb->get_results('SELECT * FROM '.Table::Applicants);
            $apps = array();
            foreach ($rows as $row) {
                $apps[] = makeApplicantFromRow($row);
            }
            return $apps;
        } else if (is_array($crsid)) {
            $rows = $wpdb->get_results('SELECT * FROM '.Table::Applicants);
            $apps = array();
            foreach ($rows as $row) {
                if (in_array($row->crsid, $crsid))
                    $apps[] = makeApplicantFromRow($row);
            }
            return $apps;
        }
        $row = $wpdb->get_row(
              $wpdb->prepare('SELECT * FROM '.Table::Applicants.' WHERE crsid = %s', $crsid),
              ARRAY_A
            );
        return makeApplicantFromRow($row);
    }
    
    public function generateIdCard() {
        // Save in $this->crsid . '_id_card.pdf' ???
        // No -> want to be able to have multiple cards on one pdf
        //
        // Have helper function to generate IDs for array of workers
        // and output them as one pdf.
    }
}

?>
