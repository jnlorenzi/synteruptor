<?php
require_once('search_lib.php');
$tempdir = "";

function temp_dir() {
	global $basedir, $tempdir;

	error_log("Creating temp dir in $basedir");
	$tempdir = tempnam($basedir, 'blast_');
	error_log("Using temp dir $tempdir");
	if (is_file($tempdir)) {
		unlink($tempdir);
		mkdir($tempdir);
		if (is_dir($tempdir)) {
			chmod($tempdir, 0777);
			return basename($tempdir);
		}
	}
	return null;
}

function check_seq($seq) {
	$seq = urldecode( $seq );
	//echo "Check seq: $seq<br>\n";
	$first_line = strstr($seq, "\n", true);
	// Add seqname if no fasta style sequence is given
	if (preg_match("/^>(.+)$/", $first_line)) {
		return $seq;
	} else {
		//echo "Added name<br>\n";
		$seq = ">Query\n$seq";
		return $seq;
	}
}

function check_db($db) {
	global $dbdir;
	//echo "Check db: $db<br>\n";
	$first_line = strstr($db, "\n", true);
	$db = filter_var($db, FILTER_SANITIZE_STRING);
	//echo "Filtered: $db<br>\n";
	if (file_exists("$dbdir/$db.faa")) {
		// echo "Couldn't find the faa!";
		return $db;
	} else {
		return '';
	}
}

function write_file($filename, $content) {
	global $basedir, $tempdir;
	$outpath = "$basedir/$tempdir/$filename";
	file_put_contents($outpath, $content);
	if (file_exists($outpath)) {
		// echo "File written in $outpath<br>";
		chmod($outpath, 0666);
		return true;
	} else {
		// echo "Error: couldn't write file $filename<br>";
		return false;
	}
}

function copy_db($dbname) {
	global $dbdir, $basedir, $tempdir;
	$dborigin = "$dbdir/$dbname.faa";
	$dbpath =  "$basedir/$tempdir/db.faa";
	copy($dborigin, $dbpath);
	if (file_exists($dbpath)) {
		//echo "File written in $dbpath<br>";
		return true;
	} else {
		//echo "Error: couldn't write db file $dbname<br>";
		return false;
	}
	chmod($dbpath, 0666);
}

// Check if a sequence was submitted
$ans = array(
	'status' => 'failed',
	'detail' => 'unknown error',
	'new_url' => '',
	'id' => ''
);

if (isset($_POST['seq']) && isset($_POST['db'])) {
	$seq = check_seq($_POST['seq']);
	$db = check_db($_POST['db']);

	if ($seq != '' && $db != '') {
		// Create an empty folder with a unique random name
		$tempdir = temp_dir();

		if (isset($tempdir)) {
			global $basedir;
			// Write query, status and dbname file
			$sq = write_file("query.faa", $seq);
			$ss = write_file("status.txt", "waiting");
			$sd = write_file("database.txt", $db);
			// Copy the db
			$sc = copy_db($db);
			
			// Use the dir name as an id
			if ($sq && $ss && $sd && $sc) {
				$ans['status'] = 'success';
				$ans['id'] = $tempdir;
				$ans['detail'] = "";
			} else {
				$ans['status'] = "failed";
				$ans['detail'] = "One or more files couldn't be created.";
			}
		} else {
			$ans['status'] = "failed";
			$ans['detail'] = "Invalid generated id. Please refresh the page.";
		}
	} else {
		$ans['status'] = "failed";
		if ($seq == '') {
			$ans['detail'] = "Invalid sequence.";
		}
		if ($db == '') {
			$ans['detail'] = "Invalid db.";
		}
	}
} else {
	$ans['status'] = "failed";
	if (!isset($_POST['seq'])) {
		$ans['detail'] = "No sequence provided.";
	}
	if (!isset($_POST['db'])) {
		$ans['detail'] = "No database provided.";
	}
}
echo json_encode($ans);
?>

