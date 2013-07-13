<?php

require_once('student.php');
require_once('constants.php');
require_once('debug.php');

/*
 * TODO Add save function to save $this to DB
*/

/*
 * Provide helper function to load from Table::Applicants and return an Applicant object.
 * Also have helper function to load entire table and return an array of Applicant objects.
 *
 * Similar function(s) for saving to Table::Applicants.
*/

/* TODO: Sort out ranking system
 *
 * Maintain a rating from each user (i.e. member of the committee).
 * $rank is average of these (default to average rating e.g. 5/10 if committee member has not voted).
 *
 * Add function to get the rating from a particular user:
 *    function getRating($userid) { ... }
 *
 * Add some constant which specifies the maximum rating
 *  --> make it a configurable variable (held in settings object?)
 *      so whoever is in charge of workers can specify what rating
 *      out of AND the default rating (if a user hasn't voted).
*/

class Applicant extends Student {
    private $roles = array();
    private $rank;
    private $notes;

    public function Applicant($name, $crsid, $college, $roles, $rank, $notes) {
        parent::Student($name, $crsid, $college);
        setRoles($roles);
        setRank($rank);
        setNotes($notes);
    }

    public function getRoles() {
        return $roles;
    }

    public function getRank() {
        return $rank;
    }

    public function getNotes() {
        return $notes;
    }

    public function setRoles($roles) {
        // Check that $roles are valid worker roles
        foreach ($roles as $role) {
            if (!WorkerType::isValid($role)) {
                Debug::log(Debug::InvalidAssignment, $role, 'Applicant::setRoles');
                return false;
            } else {
                $this->roles[] = $role;
            }
        }
        return $this;
    }

    public function setRank($rank) {
        $this->rank = $rank;
        return $this;
    }

    public function setNotes($notes) {
        $this->notes = $notes;
        return $this;
    }

    private static function makeApplicantFromRow($row) {
        $roles = array();
        // $row->roles interpreted as 16 4-bit chunks, where the ith chunk stores the ith preference
        for ($i = 0; $i < $row->num_roles; ++$i) {
            $roles[] = $row->roles & (15 << $i);
        }
        return new Applicant($row->name, $row->crsid, $row->college, $roles, $row->rank, $row->notes);
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
            Debug::log(
                Debug::Message,
                'Loaded '.count($apps).' applicants from '.Table::Applicants,
                'Applicant::load');
            return $apps;
        } else if (is_array($crsid)) {
            $rows = $wpdb->get_results('SELECT * FROM '.Table::Applicants);
            $apps = array();
            foreach ($rows as $row) {
                if (in_array($row->crsid, $crsid))
                    $apps[] = makeApplicantFromRow($row);
            }
            Debug::log(
                Debug::Message,
                'Loaded '.count($apps).' applicants from '.Table::Applicants,
                'Applicant::load');
            return $apps;
        }
        $row = $wpdb->get_row(
              $wpdb->prepare('SELECT * FROM '.Table::Applicants.' WHERE crsid = %s', $crsid),
              ARRAY_A
            );
        Debug::log(Debug::Message,
            'Loaded applicant from '.Table::Applicants,
            'Applicant::load');
        return makeApplicantFromRow($row);
    }
}

?>
