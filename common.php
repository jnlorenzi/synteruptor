<?php
$settings_path = 'settings.ini';
$settings = parse_ini_file($settings_path);
$site_name = get_setting("site_name");

error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('display_errors', 'Off');
ini_set('error_log','../migenis.log');

function get_setting($key) {
	global $settings;
	if (isset($settings[ $key ])) {
		return $settings[ $key ];
	} else {
		return '';
	}
}

function site_name() {
	echo get_setting("site_name");
}
function get_mail() {
	$contact = get_setting("contact");
	$pholder = str_replace("@", " at ", $contact);
	return "<a href='mailto:$contact'>$pholder</a>";
}

function print_header($type=null, $name=null) {
	echo "<span id='top'></span>";
	$sep = "&nbsp;> ";
	# Write the site name anyway (with link to root)
	echo "<div id='entete'>";
	echo "<a id='backtop' style='display:none;' href='#top'>^Top^</a>";
	$dir = '.';
	echo "<a href='$dir/index.php' alt='" . get_setting("site_name") . "'>";
	echo "<div id='sitelink'></div>";
	echo "</a>";
	echo "<span id='entete_links'>";
	if (!isset($type)) {
	}
	elseif ($type == 'viewer' || $type == 'aligner') {
		# If the database is given
		if (isset($_GET['version'])) {
			echo "<span id='entete_database'>";
			echo "<a href='$dir/summary.php?version=" . $_GET['version'] . "'>";
			echo "Database: " . $_GET['version'];
			echo "</a>";
			echo "</span>";
			# Create permalink container
			if (isset($name)) {
				echo $sep;
				echo "<span><a id='permalink' href=''>" . $name . "</a></span>";
			}
		}
	}
	elseif ($type == 'upload') {
		echo "<a href='createdb.php'>Database creator</a>";
	}
	elseif ($type == 'search') {
		$link = "search.php";
		if (isset($_GET['version'])) {
			$link .= '?version=' . $_GET['version'];
		}
		echo "<a href='$link'>Database search</a>";
	}
	else {
	}
	echo "</span>";
	echo "</div>";
}
?>

