<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
require_once("common.php");
require_once("search_lib.php");
?>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php site_name(); ?> database search</title>
		<link rel="icon" type="image/png" href="css/Synteruptor_logo_square.png">
		<link rel="stylesheet" type="text/css" href="css/common.css">
		<link rel="stylesheet" type="text/css" href="css/search.css">
		<script type="text/javascript" src="js/jquery/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="js/common.js"></script>
		<script type="text/javascript" src="js/search.js"></script>
		<script type="text/javascript" src="js/search_status.js"></script>
	</head>
	<body>
<?php
print_header("search");
?>
<div id="content">
<div class="centered_box">
<h2>Database search</h2>
<p>Search a <?php site_name(); ?> database with Blast using a fasta file (multiple sequences possible) or a whole sequence.</p>

<h3>Search</h3>
<?php
if (get_setting( "can_search" )) {
	if (isset($_GET["id"])) {
		# Check id
		if (!check_id($id)) {
			echo "Invalid id ($id)<br>";
			echo '<a href="search_search.php">Start a new search</a>';
			exit;
		} else {
			echo "<p id='results'>Waiting...</p>";
		}
	} else {
		# Form
		echo '<form name="seqform" id ="blast_form" method="post">';
		echo '<label>Choose the database to search:</label>' . print_select("db", list_dbs()) . ' <a id="summary"></a><br>';
		echo "<p>Paste your sequence in the text area (fasta format):</p>";
		echo '<textarea name="seq" cols=80 rows=10 value="" id="seq"></textarea>';
		echo '<br><input id="submitter" type="submit"></input><button type="button" id="clear">Clear</button>';
		echo '</form>';
	}
} else {
	echo "Sequence search is not enabled on this server.";
}
?>
</div>
<div id="results_table" />
<div id="tail" />
</div>
</body>
</html>

