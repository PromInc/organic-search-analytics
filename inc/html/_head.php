<?php include_once realpath(dirname(__FILE__).'/../code/globalIncludes.php'); /* Load classes */ ?>
<?php $GLOBALS['scriptName'] = str_replace( "/".$GLOBALS['appInstallDir'], "", $_SERVER['SCRIPT_NAME'] ) ?>

<html>
	<head>
		<title><?php echo $titleTag; ?></title>

		<link rel="stylesheet" href="css/gwt.css">

		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
		<script type="text/javascript" src="js/script.js"></script>

		<?php if( $GLOBALS['scriptName'] == "settings.php" ) { ?>
			<script type="text/javascript" src="js/settings.js"></script>
		<?php } ?>
	</head>

	<body>
		<header>
			<span><a href="index.php">Home</a></span>
			<span><a href="data-capture.php">Data Capture</a></span>
			<span><a href="settings.php">Settings</a></span>
<!--
			<span><a href="search.php">GWT Report Download</a></span>
			<span><a href="import.php">Import</a></span>
-->
			<!-- <span><a href="report.php">Reports</a></span> -->
		</header>

		<div id="siteContent">