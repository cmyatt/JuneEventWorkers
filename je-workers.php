<?php

/*
 * Plugin Name: June Event Workers
 * Description: Worker management system for the June Event.
 * Version:     0.1
 * Author:      Chris Myatt
*/

require_once('constants.php');
require_once('debug.php');
require_once('applicant.php');
require_once('worker.php');

/* Settings Object
 *
 * -> Serialize -- transaction-style -- to ensure updates persist
 * -> Maintains pay for each role
 * -> Maintains numbers for each role
 * -> Booleans: allow preferences, allow online applications (vs email only)
 * -> Number of preferences allowed (1-3)
*/

/*
 * Want to be able to integrate this plugin with je-ticketing so that when a worker
 * signs in to the site they see their worker status as well as their ticket status.
 * Can then automatically ask worker if they want a refund for their ticket(s), or
 * if they want them re-named (provide a web interface for this so no emailing ticketing
 * necessary).
*/

?>
