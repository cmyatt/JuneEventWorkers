<?php

/*
 * This provides the definition of the dbDelta function
 * which is used to update/create databses. It will create
 * one if it doesn't exist or update its schema to that
 * specified if it does.
*/
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

require_once('constants.php');

/* Create/update the applications and workers tables.
*/
function make_tables() {
    global $wpdb;

    /* Represents those who have applied to be workers for the event.
     * Two roles to allow for preferences.
     * Rank to allow committee members to rank candidates.
    */
    $sql = 'CREATE TABLE '.Table::Applicants.' (
        name    VARCHAR(255) NOT NULL,
        crsid   VARCHAR(10) NOT NULL UNIQUE,
        college VARCHAR(16) NOT NULL,
        role1   SMALLINT NOT NULL,
        role2   SMALLINT,
        role3   SMALLINT,
        rank    TINYINT,
        notes   TEXT,
        PRIMARY KEY (crsid));';
    dbDelta($sql);
    
    /* Represents those selected to be workers for the event.
    */
    $sql = 'CREATE TABLE '.Table::Workers.' (
        name    VARCHAR(255) NOT NULL,
        crsid   VARCHAR(10) NOT NULL UNIQUE,
        college VARCHAR(16) NOT NULL,
        role    SMALLINT NOT NULL,
        shift   TINYINT(1),
        PRIMARY KEY (crsid));';
    dbDelta($sql);
}

?>
