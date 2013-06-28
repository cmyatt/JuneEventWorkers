<?php

/* Manages the logging and display of error and debug messages.
 * This include wpdb errors and any other debug output.
*/
final class Debug {
	const Message = '';	// not an error -- just a debug message
	const Error = 'Error';
    const InvalidAssignment = 'Invalid assignment';

    private static $log = array();
    private static $show = true;	// set to false for release version

    private Debug() {}

    public static function toggleShow() {
    	setShow(!$show);
    }

    public static function setShow($sh) {
    	global $wpdb;
    	$show = $sh;
    	if ($show)
			$wpdb->show_errors();
		else
			$wpdb->hide_errors();
    }

	/* Add a debug message to the log. If Debug::Show is true then
	 * the log will be displayed on the frontend.
	*/
	public static function log($type, $data, $location) {
		switch ($type) {
		case Message:
			$log[] = array('msg' => "$location: $data", 'type' => Message);
			break;
		case Error:
			$log[] = array('msg' => "$type in $location: $data", 'type' => Error);
			break;
		case InvalidAssignment:
			$log[] = array('msg' => $type . " in $location: trying to assign $data", 'type' => Error);
			break;
		default:
			$log[] = array('msg' => "Unknown log type in $location: $data", 'type' => Message);
		}
	}

	/* Returns the HTML code which displays the debug log.
	 * An unordered list with id 'debug_output'.
	 * Each li has class 'error_msg' OR 'debug_msg'.
	*/
	public static function getFrontend() {
		$html = '<ul id="debug_output">';
		foreach ($log as $item) {
			$style_class = ($item['type'] === Error)? 'error_msg' : 'debug_msg';
			$html .= '<li class="'.$style_class.'">'.$item['msg'].'</li>';
		}
		return $html.'</ul>';
	}
}

?>