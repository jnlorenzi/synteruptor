<!DOCTYPE HTML>
<html>
<?php
require_once('lib_db.php');
require_once('common.php');
?>
	<head>
		<meta charset="UTF-8">
		<title><?php site_name(); ?> tools</title>
		<link rel="icon" type="image/png" href="css/Synteruptor_logo_square.png">
		<link rel="stylesheet" type="text/css" href="css/common.css">
		<link rel="stylesheet" type="text/css" href="css/index.css">
		<script type="text/javascript" src="js/jquery/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="js/common.js"></script>
		<script type="text/javascript" src="js/index.js"></script>
	</head>
	<body>
<?php	print_header(); ?>
<div id="content">
	<div class="centered_box">
		<div id="site_description">
		<div class='index_box'>
			<!--<img src='css/Synteruptor_logo_full.png' id='logo' alt='logo' />-->
			<h2>About <?php echo $site_name ?></h2>
			<p><?php echo $site_name ?> is a tool designed to find synteny breaks between closely related bacterial genomes. The breaks found can be explored with this web interface to find clusters of genes of interest.</p>
			<br /><span id="last_update">Last update: <?php echo get_setting("last_update"); ?></span>
			<br /><span id="contact">Contact: <?php echo get_mail(); ?></span>
			<span style="float:right;"><a href="download.php">Download <?php echo $site_name ?></a></span>
			<div style='clear:both;'></div>
			</div>

		<div class='index_box'>
			<h2>How to use the <?php echo $site_name ?> website</h2>
			<!--
			<p>Synteruptor can be used in several ways:<ul>
				<li>either explore a precomputed, available database</li>
				<li>or create your own database with your own genome files. The created database will be private, but it can be explored like any of the precomputed database</li>
			</ul>
			</p>
			-->
			<?php
				// Display public databases (if any)
				echo "<h3>Available databases</h3>";
				$allowed = get_available_dbs_list();
				if ($allowed) {
					echo "<p>The following databases were generated with preselected genomes and can be freely explored:</p>";
					echo "<div id='databases'></div>";
				} else {
					echo "<p>No public database available</p>";
				}
				// Show database creator link if active
				echo "<h3>Database creation</h3>";
				if (get_setting("can_upload")) {
				echo "<p>Upload your genomes to create a $site_name database:</p>";
					echo "<div class='button_container'><a href='createdb.php'><div class='button_link'>Create a $site_name database</div></a></div>";
				} else {
					echo "<p>Database creation is not activated on this website</p>";
				}
				
			?>
		</div>
	</div>
	<div id="tail">
		<ul>
			<li><a href="//www.agence-nationale-recherche.fr/?Projet=ANR-13-BSV6-0009"><img src="css/LogoANR_175.png" title="ANR Migenis" "Link to ANR Migenis" /></a></li>
			<li><a href="//u-psud.fr"><img src="css/LogoUPSUD_200.png" title="Université Paris-Sud" alt="Link to University Paris-Sud" /></a></li>
			<li><a href="//www.i2bc.paris-saclay.fr/?lang=en"><img src="css/LogoI2BC.png" title="I2BC" alt="Link to I2BC" /></a></li>
		</ul>
	</div>
	</div>
</div>
</body>
</html>

