<?php

require_once(WP_PLUGIN_DIR . '/je-workers/constants.php');
require_once(WP_PLUGIN_DIR . '/je-workers/debug.php');
require_once(WP_PLUGIN_DIR . '/je-workers/settings.php');

// Handle settings form submission here (settings_page function below)
if (isset($_POST['save'])) {
	$obj = Settings::getInstance();
	$obj->init_update();

	// Update worker settings
	$i = 0;
	$types = array();
	$pay = array();
	$numbers = array();
	$apps = array();
	$shifts = array();
	while (isset($_POST['type_'.$i])) {
		if ($_POST['delete_'.$i] === 'Delete') {
			$types[$_POST['type_'.$i]]   = $_POST['desc_'.$i];
			$pay[$_POST['type_'.$i]]     = $_POST['pay_'.$i];
			$numbers[$_POST['type_'.$i]] = $_POST['number_'.$i];
			$apps[$_POST['type_'.$i]]    = isset($_POST['online_apps_'.$i]);
			$shifts[$_POST['type_'.$i]]  = isset($_POST['shifts_'.$i]);
		} else {
			$obj->deleteWorker($_POST['type_'.$i]);
		}
		++$i;
	}
	$obj->addWorkerTypes($types);
	$obj->setWorkerPay($pay);
	$obj->setWorkerNumbers($numbers);
	$obj->setWorkerShifts($shifts);
	$obj->setOnlineApplications($apps);

	// Update applicants settings
	$obj->setNumPreferences($_POST['num_prefs']);
	$obj->setEnableRating(isset($_POST['enable_ratings']));
	$obj->setMaxRating($_POST['max_rating']);
	$obj->setDefaultRating($_POST['default_rating']);
	$obj->setCommitteeCrsids(explode(',', $_POST['crsid_list']));
	$obj->setEnableRating(isset($_POST['enable_debug']));

	$obj->commit();
}

function settings_page() {
?>

<h1><u>Settings</u></h1>
<br />

<span style="font-size: 115%;">
	Remember to click <b>Save changes</b> when done.
	Worker deletion will only occur once <b>Save changes</b> has been clicked.
</span>

<form action="admin.php?page=je_workers_settings" method="POST">

<h2><u>Workers</u></h2>
<table class="settings_table" id="workers_settings" cellspacing="0">
	<tr id="worker_types_row">
		<td class="settings_name">Role:</td>
		<?php
			$worker_types = array();
			$num_worker_types = 0;
			Settings::deepCopy(Settings::getInstance()->getWorkerTypes(), $worker_types);
			foreach ($worker_types as $type => $desc) {
				echo '<td class="heading">';
				echo '<input type="text" value="'.$type.'" id="type_'.$num_worker_types.'" name="type_'.$num_worker_types.'" style="width: 100px;" />';
				echo '</td>';
				++$num_worker_types;
			}
		?>
		<td class="heading" id="new_worker_col">
			<input type="hidden" id="num_new_types" name="num_new_types" value="<?php echo $num_worker_types; ?>" />
			<input type="button" id="add_worker" class="button-primary" style="width: 64px; padding: 0px;" value="+ Add" />
		</td>
	</tr>
	<tr class="settings_row shadow">
		<td class="settings_name">Description:</td>
		<?php
			$i = 0;
			foreach ($worker_types as $type => $desc) {
				echo '<td class="inner_input" style="padding: 0px;">';
				echo '<textarea rows="3" cols="17" id="desc_'.$i.'" name="desc_'.$i.'">'.$desc.'</textarea>';
				echo '</td>';
				++$i;
			}
		?>
		<td class="inner_input disabled" id="new_desc_col"></td>
	</tr>
	<tr class="settings_row">
		<td class="settings_name">Worker Pay (£):</td>
		<?php
			$worker_pay = array();
			Settings::deepCopy(Settings::getInstance()->getWorkerPay(), $worker_pay);
			$i = 0;
			foreach ($worker_pay as $type => $pay) {
				echo '<td class="inner_input">';
				echo '<input type="number" value="'.$pay.'" min="0.00" step="0.01" id="pay_'.$i.'" name="pay_'.$i.'" />';
				echo '</td>';
				++$i;
			}
		?>
		<td class="inner_input disabled" id="new_pay_col"></td>
	</tr>
	<tr class="settings_row shadow">
		<td class="settings_name">Worker numbers:</td>
		<?php
			$worker_numbers = array();
			Settings::deepCopy(Settings::getInstance()->getWorkerNumbers(), $worker_numbers);
			$i = 0;
			foreach ($worker_numbers as $type => $number) {
				echo '<td class="inner_input">';
				echo '<input type="number" value="'.$number.'" min="0" step="1" id="number_'.$i.'" name="number_'.$i.'" />';
				echo '</td>';
				++$i;
			}
		?>
		<td class="inner_input disabled" id="new_numbers_col"></td>
	</tr>
	<tr class="settings_row">
		<td class="settings_name">Allow online applications:</td>
		<?php
			$worker_apps = array();
			Settings::deepCopy(Settings::getInstance()->getOnlineApplications(), $worker_apps);
			$i = 0;
			foreach ($worker_apps as $type => $apps) {
				echo '<td class="inner_input">';
				echo '<input type="checkbox" id="online_apps_'.$i.'" name="online_apps_'.$i.'" ';
				echo ($apps === true)? 'checked /></td>' : '/></td>';
				++$i;
			}
		?>
		<td class="inner_input disabled" id="new_applications_col"></td>
	</tr>
	<tr class="settings_row shadow">
		<td class="settings_name">Split into shifts:</td>
		<?php
			$worker_shifts = array();
			Settings::deepCopy(Settings::getInstance()->getWorkerShifts(), $worker_shifts);
			$i = 0;
			foreach ($worker_shifts as $type => $shift) {
				echo '<td class="inner_input">';
				echo '<input type="checkbox" id="shifts_'.$i.'" name="shifts_'.$i.'" ';
				echo ($shift === true)? 'checked /></td>' : '/></td>';
				++$i;
			}
		?>
		<td class="inner_input disabled" id="new_shifts_col"></td>
	</tr>
	<tr class="settings_row">
		<td class="settings_name"></td>
		<?php
			for ($i = 0; $i < $num_worker_types; ++$i) {
				echo '<td class="inner_input" style="padding: 0px;">';
				echo '<input type="hidden" id="delete_'.$i.'" name="delete_'.$i.'" value="Delete" />';
				echo '<input type="button" id="delete_button_'.$i.'" value="Delete" class="button-primary" />';
				echo '</td>';
			}
		?>
		<td class="inner_input disabled" id="new_remove_col"></td>
	</tr>
</table>

<h2><u>Applicants</u></h2>
<table class="settings_table no_border" cellspacing="0">
<tr class="settings_row shadow">
	<td class="settings_name no_right_border">
		<label for="num_prefs">Number of preferences:</label>
	</td>
	<td class="settings_input">
		<input type="number" value="<?php echo Settings::getInstance()->getNumPreferences(); ?>" min="1" max="16" step="1" id="num_prefs" name="num_prefs" />
	</td>
	<td class="settings_input" style="padding-left: 10px; vertical-align: center;">
		The number of roles an individual can apply for.
	</td>
</tr>
<tr class="settings_row">
	<td class="settings_name no_right_border">
		<label for="enable_ratings">Enable applicant ratings:</lable>
	</td>
	<td class="settings_input">
		<input type="checkbox" id="enable_ratings" name="enable_ratings" <?php echo (Settings::getInstance()->getEnableRating())? 'checked' : ''; ?> />
	</td>
	<td class="settings_input" style="padding-left: 10px; vertical-align: center;">
		Allow committee members to rate applicants in order to ensure the best people are selected
		to be workers and the worst are avoided.
	</td>
</tr>
<tr class="settings_row shadow">
	<td class="settings_name no_right_border">
		<label for="max_rating">Maximum rating:</label>
	</td>
	<td class="settings_input">
		<input type="number" value="<?php echo Settings::getInstance()->getMaxRating(); ?>" min="1" step="1" id="max_rating" name="max_rating" />
	</td>
	<td class="settings_input" style="padding-left: 10px; vertical-align: center;">
		The highest rating a committee member can give to an applicant.
	</td>
</tr>
<tr class="settings_row">
	<td class="settings_name no_right_border">
		<label for="default_rating">Default rating:</label>
	</td>
	<td class="settings_input">
		<input type="number" value="<?php echo Settings::getInstance()->getDefaultRating(); ?>" min="0" step="1" id="default_rating" name="default_rating" />
	</td>
	<td class="settings_input" style="padding-left: 10px; vertical-align: center;">
		If a committee member does not rate an applicant the applicant will automatically be given
		this rating from the committee member.
	</td>
</tr>
<tr class="settings_row shadow">
	<td class="settings_name no_right_border">
		<label for="crsid_list">Committee CRSids:</label>
	</td>
	<td class="settings_input">
		<textarea cols="60" id="crsid_list" name="crsid_list"><?php echo implode(',', Settings::getInstance()->getCommitteeCrsids()); ?></textarea>
	</td>
	<td class="settings_input" style="padding-left: 10px; vertical-align: center;">
		Comma separated list of the CRSids of the committee members. Used to ensure only they can
		rate applicants and they can only give each applicant one rating.
	</td>
</tr>
</table>

<h2><u>Miscellaneous</u></h2>
<table class="settings_table no_border" cellspacing="0">
<tr class="settings_row shadow">
	<td class="settings_name no_right_border">
		<label for="enable_debug">Enable debugging:</label>
	</td>
	<td class="settings_input">
		<input type="checkbox" id="enable_debug" name="enable_debug" <?php echo (Settings::getInstance()->getEnableDebugging())? 'checked' : ''; ?> />
	</td>
	<td class="settings_input" style="padding-left: 10px; vertical-align: center;">
		For the webmaster: check this to keep a log of all debug messages and errors (can view log
		on the Debug page).
	</td>
</tr>
</table>

<input type="submit" class="button-primary" style="margin-top: 13px;" name="save" value="Save changes" />

</form>

<?php
}

?>