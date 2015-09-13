<?php
if( !strpos( $_SERVER['SCRIPT_NAME'], "upgrade.php" ) ) {
	include_once realpath(dirname(__FILE__).'/../code/globalIncludes.php'); /* Load classes */
	$GLOBALS['scriptName'] = str_replace( "/".$GLOBALS['appInstallDir'], "", $_SERVER['SCRIPT_NAME'] );
}
?>


<html>
	<head>
		<title><?php echo $titleTag; ?></title>

		<link rel="stylesheet" href="css/styles.css">

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
			<div class="floatleft">
				<span><a href="index.php">Home</a></span>
				<span><a href="data-capture.php">Data Capture</a></span>
				<span><a href="settings.php">Settings</a></span>
				<span><a href="report.php">Reports</a></span>
			</div>
			<div class="floatright">
				<span class="donate">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="P5WHT23LSGLE4">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				</span>
			</div>
			<div class="clear"></div>
		</header>

		<div id="siteContent">