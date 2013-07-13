<?php

/* TODO: Save/Load support
 *
 * Want to preserve settings -- do so in settings object? And then restore
 * using Debug::set* on load?
*/

/* TODO Change log filename when move to an actual web server. */

/* Manages the logging and display of error and debug messages.
 * This include wpdb errors and any other debug output.
 *
 * To DISABLE DEBUGGING, set $show, $use_log, and
 * wordpress/wp-config.php:WP_DEBUG to false.
*/
final class Debug {
	const Message = '';	// not an error -- just a debug message
	const Error = 'Error';
    const InvalidAssignment = 'Invalid assignment';

    private static $log = array();
    private static $show = true;	// set to false for release version
    private static $use_log = true;	// set to false for release version

    private function Debug() {}

    public static function toggleShow() {
    	setShow(!$show);
    }

    public static function setShow($sh) {
    	if (is_bool($sh)) {
	    	global $wpdb;
	    	$show = $sh;
	    	if ($show)
				$wpdb->show_errors();
			else
				$wpdb->hide_errors();
		}
    }

    public static function useLog($use) {
    	if (is_bool($use)) {
    		$use_log = $use;
    	}
    }

    public static function isVisible() {
    	return $show;
    }

    public static function clearLog() {
    	file_put_contents('C:/Program Files (x86)/EasyPHP-12.1/www/wordpress/wp-content/plugins/je-workers/includes/data/debug_log.txt', '', LOCK_EX);
    	Debug::$log = array();
    }

	/* Add a debug message to the log. If Debug::Show is true then
	 * the log will be displayed on the frontend.
	*/
	public static function message($data, $location) {
		if (!Debug::$use_log) {
			return;
		}
		$entry = array('msg' => "<b>$location:</b>  $data", 'type' => Debug::Message);
		$filename = 'C:/Program Files (x86)/EasyPHP-12.1/www/wordpress/wp-content/plugins/je-workers/includes/data/debug_log.txt';
		file_put_contents($filename, "\t".serialize($entry), LOCK_EX|FILE_APPEND);
	}

	public static function error($data, $location) {
		if (!Debug::$use_log) {
			return;
		}
		$entry = array('msg' => "<b>$location:</b>  $data", 'type' => Debug::Error);
		$filename = 'C:/Program Files (x86)/EasyPHP-12.1/www/wordpress/wp-content/plugins/je-workers/includes/data/debug_log.txt';
		file_put_contents($filename, "\t".serialize($entry), LOCK_EX|FILE_APPEND);
	}

	/* Returns the HTML code which displays the debug log if $show is true.
	 * An unordered list with id 'debug_output'.
	 * Each li has class 'error_msg' OR 'debug_msg'.
	*/
	public static function getFrontend() {
		if (!Debug::$show) {
			return '';
		}
		$filename = 'C:/Program Files (x86)/EasyPHP-12.1/www/wordpress/wp-content/plugins/je-workers/includes/data/debug_log.txt';
		$contents = file_get_contents($filename);
		$entries = explode("\t", $contents);
		$html = '<ul id="debug_output">';
		if ($contents === '') {
			return $html.'</ul>';
		}
		$num_items = count($entries);
		$i = 0;
		foreach ($entries as $entry) {
			$item = unserialize($entry);
			if (!is_array($item)) {
				continue;
			}
			$style_class = ($item['type'] === Debug::Error)? 'error_msg' : 'debug_msg';
			if ($i === 0) {
				$style_class .= ' msg_first';
			} else if ($i === $num_items-2) {
				$style_class .= ' msg_last';
			}
			$text = $item['msg'];
			$html .= "<li class=\"$style_class\">&nbsp;$text</li>";
			++$i;
		}
		return $html.'</ul>';
	}
}

?>