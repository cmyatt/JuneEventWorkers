<?php

require_once(WP_PLUGIN_DIR . '/je-workers/constants.php');
require_once(WP_PLUGIN_DIR . '/je-workers/debug.php');

// Handle form submission
if (isset($_POST['clear'])) {
	Debug::clearLog();
}

function debug_page() {
?>

<h1><u>Debug</u></h1>
<br />

<div id="page_info">
	To disable debugging set <i>Debug::$show</i>, <i>Debug::$use_log</i> and
	<i>wordpress/wp-config.php:WP_DEBUG</i> to false.
	<br />
	Also deactivate the two debugging plugins (Debug Bar; Log Deprecated Notices).
</div>

<br /><br />
<div id="viewing_options">
	<form action="admin.php?page=je_workers_debug" method="POST">
		<b>Show:</b>
		<input type="checkbox" id="show_msgs" checked />
		<label for="show_msgs">Messages</label>
		<input type="checkbox" id="show_errors" checked />
		<label for="show_errors">Errors</label>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" class="button-primary" value="Clear log" name="clear" />
	</form>
</div>

<?php
	echo Debug::getFrontend();
}

?>