<?php
@session_start();
// if( !strpos( $_SERVER['SCRIPT_NAME'], "upgrade.php" ) ) {
	include_once realpath(dirname(__FILE__).'/../code/globalIncludes.php'); /* Load classes */
	$GLOBALS['scriptName'] = str_replace( "/".$GLOBALS['appInstallDir'], "", $_SERVER['SCRIPT_NAME'] );
// }
?>

<html>
	<head>
		<title><?php echo $titleTag; ?></title>

		<link rel="stylesheet" href="css/styles.css">
		<link rel="stylesheet" href="css/lib/jquery/jquery-ui.1.11.4.custom.min.css">
		<link rel="stylesheet" href="css/lib/font-awesome/font-awesome.min.css">

		<script language="javascript" type="text/javascript" src="js/lib/jquery/jquery-1.11.3.min.js"></script>
		<script src="js/lib/jquery/jquery-ui.1.11.4.custom.min.js"></script>
		<script type="text/javascript" src="js/script.js"></script>

		<?php if( isset( $GLOBALS['scriptName'] ) ) { ?>
			<?php if( $GLOBALS['scriptName'] == "report.php" ) { ?>
				<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="js/lib/jqplot/excanvas.js"></script><![endif]-->
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/jquery.jqplot.min.js"></script>
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/plugins/jqplot.cursor.min.js"></script>
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/plugins/jqplot.highlighter.min.js"></script>
				<!-- Bar Charts -->
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/plugins/jqplot.barRenderer.min.js"></script>
				<script language="javascript" type="text/javascript" src="js/lib/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
				<link rel="stylesheet" type="text/css" href="css/lib/jqplot/jquery.jqplot.css" />
			<?php } ?>

			<?php if( $GLOBALS['scriptName'] == "settings.php" ) { ?>
				<script type="text/javascript" src="js/settings.js"></script>
			<?php } ?>
		<?php } ?>
	</head>

	<body id="page_<?php echo $GLOBALS['file_name'] ?>">
		<header>
			<div id="nav_primary" class="floatleft">
				<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="data-capture.php">Data Capture</a></li>
				<li><a href="settings.php">Settings</a></li>
				<li><a href="report.php">Reports</a></li>
				</ul>
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