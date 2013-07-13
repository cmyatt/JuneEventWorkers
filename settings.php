<?php

require_once('constants.php');
require_once('debug.php');

/* To make updates to the settings:
 *
 *	$obj = Settings::getInstance();
 *	$obj->init_update();
 *	// Perform updates here (e.g. $obj->setWorkerPay(...))
 *	$result = $obj->commit();
*/
class Settings {
	const Version = '0.1';
	const MinPreferences = 1;
	const MaxPreferences = 16;
	const MinRating      = 0;

	private static $File1;
	private static $File2;

	// Configurable values
	private $worker_types = array();
	private $worker_pay = array();
	private $worker_numbers = array();
	private $online_applications = array();
	private $worker_shifts = array();
	private $num_preferences = 1;
	private $enable_debugging = true;
	private $enable_rating = true;
	private $max_rating = 10;
	private $default_rating = 5;
	private $committee_crsids = array(); // Used by Applicant to ensure one vote per committee member

	// Internal variables
	private static $instance = null;
	private $shadow = null;
	private $queries = array();	// SQL queries to be executed on committing an update
	private $file = '';	// determine whether 1 or 2 by inspecting files - choose non-empty one

	private function Settings() {}
	
	public static function getInstance() {
		if (is_null(Settings::$instance)) {
			Settings::$File1 = 'C:/Program Files (x86)/EasyPHP-12.1/www/wordpress/wp-content/plugins/je-workers/includes/data/settings1.txt';
			Settings::$File2 = 'C:/Program Files (x86)/EasyPHP-12.1/www/wordpress/wp-content/plugins/je-workers/includes/data/settings2.txt';
			Settings::$instance = Settings::load(0);
		}
		return Settings::$instance;
	}

	/* Do a deep copy of $var into $copy */
	public static function deepCopy($var, &$copy) {
		if (is_array($var)) {
			foreach ($var as $key => $value) {
				Settings::deepCopy($value, $copy[$key]);
			}
		} else if (is_object($var)) {
			foreach (get_object_vars($var) as $field => $value) {
				if ($field != 'shadow') {
					Settings::deepCopy($value, $copy->$field);
				}
			}
		} else {
			// Primitive type - just assign as normal
			$copy = $var;
		}
	}
	
	// Should return a deep copy of all arrays here
	public function getWorkerTypes() {
		return $this->worker_types;
	}

	public function getWorkerPay() {
		$copy = array();
		Settings::deepCopy($this->worker_pay, $copy);
		return $copy;
	}

	public function getWorkerNumbers() {
		return $this->worker_numbers;
	}

	public function getWorkerShifts() {
		return $this->worker_shifts;
	}

	public function getNumPreferences() {
		return $this->num_preferences;
	}

	public function getOnlineApplications() {
		return $this->online_applications;
	}

	public function getEnableDebugging() {
		return $this->enable_debugging;
	}

	public function getEnableRating() {
		return $this->enable_rating;
	}

	public function getMaxRating() {
		return $this->max_rating;
	}

	public function getDefaultRating() {
		return $this->default_rating;
	}

	public function getCommitteeCrsids() {
		return $this->committee_crsids;
	}

	// Unserialize data from file and return the generated object
	private static function load($file_num) {
		$obj = null;
		if ($file_num === 1 || $file_num === 2) {
			$data = file_get_contents(($file_num === 1)? Settings::$File1 : Settings::$File2);
			$obj = unserialize($data);	
			$obj->file = ($file_num === 1)? Settings::$File1 : Settings::$File2;
		} else {
			$data1 = file_get_contents(Settings::$File1);
			$data2 = file_get_contents(Settings::$File2);
			if (strlen($data1) > strlen($data2)) {
				$obj = unserialize($data1);
				$obj->file = Settings::$File1;
			} else if (strlen($data2) > strlen($data1)) {
				$obj = unserialize($data2);
				$obj->file = Settings::$File2;
			} else {
				$obj = new Settings();
				$obj->file = Settings::$File1;
			}
		}
		$obj->queries = array();	// discard previous SQL queries
		return $obj;
	}

	/* To change settings (OCC):
	 *
	 * Call init_transaction() to create a shadow of the current instance.
	 * All changes are performed on the shadow.
	 * Call commit() to commit changes to file.
	 * If file written successfully then make the shadow the current instance and execute all SQL queries.
	 * Otherwise discard the shadow and return failure.
	*/
	
	/* Create a deep copy of the current instance */
	public function init_update() {
		$this->shadow = new Settings();
		Settings::deepCopy($this, $this->shadow);
		// Make shadow use whichever file $this isn't using
		$this->shadow->file = ($this->file === Settings::$File1)? Settings::$File2 : Settings::$File1;
		return $this;
	}

	// Setter functions here
	// If shadow is null then return false
	// Otherwise make change to shadow and return true

	/* @types: associative array of (type, description) pairs */
	public function addWorkerTypes($types) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setWorkerPay');
			return false;
		}
		foreach ($types as $type => $desc) {
			$this->shadow->worker_types[$type] = $desc;
		}
		return true;
	}

	/* @types: array of worker types */
	public function deleteWorker($type) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setWorkerPay');
			return false;
		}
		if (array_key_exists($type, $this->shadow->worker_types)) {
			unset($this->shadow->worker_types[$type]);
			unset($this->shadow->worker_pay[$type]);
			unset($this->shadow->worker_numbers[$type]);
			unset($this->shadow->online_applications[$type]);
			unset($this->shadow->worker_shifts[$type]);
			Debug::message('Deleting worker: '.$type, 'Settings::deleteWorker');
		}
		return true;
	}

	/* @changes: associative array of (role, new_pay) pairs */
	public function setWorkerPay($changes) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setWorkerPay');
			return false;
		}
		foreach ($changes as $type => $pay) {
			if (array_key_exists($type, $this->shadow->worker_types)) {
				$this->shadow->worker_pay[$type] = $pay;
			}
		}
		return true;
	}
	
	/* @changes: associative array of (role, new_pay) pairs */
	public function setWorkerNumbers($changes) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setWorkerNumbers');
			return false;
		}
		foreach ($changes as $role => $num) {
			if (array_key_exists($role, $this->shadow->worker_types)) {
				$this->shadow->worker_numbers[$role] = $num;
			}
		}
		return true;
	}

	/* @changes: associative array of (role, new_shift [bool]) pairs */
	public function setWorkerShifts($changes) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setWorkerShifts');
			return false;
		}
		foreach ($changes as $role => $shift) {
			if (array_key_exists($role, $this->shadow->worker_types) && is_bool($shift)) {
				$this->shadow->worker_shifts[$role] = $shift;
			}
		}
		return true;
	}

	/* @prefs: must be >= MinPreferences and <= MaxPreferences */
	public function setNumPreferences($prefs) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setNumPreferences');
			return false;
		}
		if ($prefs > Settings::MinPreferences && $prefs < Settings::MaxPreferences) {
			$this->shadow->num_preferences = $prefs;
			return true;
		}
		return false;
	}

	/* @changes: associative array of (role, boolean) pairs */
	public function setOnlineApplications($changes) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setOnlineApplications');
			return false;
		}
		foreach ($changes as $role => $online_apps) {
			if (array_key_exists($role, $this->shadow->worker_types)) {
				$this->shadow->online_applications[$role] = $online_apps;
			}
		}
		return true;
	}

	/* @enable: must be a boolean */
	public function setEnableDebugging($enable) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setEnableDebugging');
			return false;
		}
		if (is_bool($enable)) {
			$this->shadow->enable_debugging = $enable;
			return true;
		}
		return false;
	}

	/* @enable: must be a boolean */
	public function setEnableRating($enable) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setEnableRating');
			return false;
		}
		if (is_bool($enable)) {
			$this->shadow->enable_rating = $enable;
			return true;
		}
		return false;
	}

	/* @rating: must be an integer in some form (i.e. 12 or "12") */
	public function setMaxRating($rating) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setMaxRating');
			return false;
		}
		// Use unary addition operator to try to coerce $rating into an integer
		if (is_int(+$rating)) {
			$this->shadow->max_rating = $rating;
			return true;
		}
		return false;
	}

	/* @rating: must be an integer in some form (i.e. 12 or "12") and >= MinRating */
	public function setDefaultRating($rating) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setDefaultRating');
			return false;
		}
		// Use unary addition operator to try to coerce $rating into an integer
		if (is_int(+$rating) && $rating >= Settings::MinRating) {
			$this->shadow->default_rating = $rating;
			return true;
		}
		return false;
	}

	/* @changes: array of CRSids */
	public function setCommitteeCrsids($changes) {
		if (is_null($this->shadow)) {
			Debug::error('Setting data before init_update called', 'Settings::setCommitteeCrsids');
			return false;
		}
		if (is_array($changes)) {
			$this->shadow->committee_crsids = $changes;
		}
		return true;
	}

	/* Try to write all changes to disk, set the current instance to the newest one.
	 * Return true for success, false for failure (in which case none of the updates are made).
	*/
	public function commit() {
		// Serialize the shadow and save it to the current shadow file
		$result = file_put_contents($this->shadow->file, serialize($this->shadow), LOCK_EX);

		// No need to null or unset anything here - once function exited then getInstance will return shadow
		if ($result !== false) {
			// Wipe the old file
			file_put_contents(($this->shadow->file === Settings::$File1)? Settings::$File2 : Settings::$File1, '', LOCK_EX);

			// Execute SQL queries
			global $wpdb;
			foreach ($this->shadow->queries as $query) {
				switch ($query[0]) {
					case 'update':
						$wpdb->update($query[1], $query[2], $query[3]);
						Debug::message('Updated DB table ('.$query[1].'): '.$query[2][0], 'Settings::commit');
						break;
					default:
						Debug::error('Unrecognised SQL query function', 'Settings::commit');
				}
			}

			// Update debugging
			Debug::setShow($this->shadow->getEnableDebugging());
			Debug::useLog($this->shadow->getEnableDebugging());

			Settings::$instance = $this->shadow;
			Debug::message('Committed settings update to disk', 'Settings::commit');
		} else {
			// Ensure shadow file is empty
			file_put_contents($this->shadow->file, '');
			Debug::error('Failed to commit settings update to disk', 'Settings::commit');
		}
		return ($result === true);
	}
}

?>