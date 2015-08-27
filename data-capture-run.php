<?php
require_once realpath(dirname(__FILE__).'/inc/code/globalIncludes.php'); /* Load classes */

if( isset($_GET) && isset($_GET['domain']) && isset($_GET['date']) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$_GET['date']) ) {
	/* Set the max allowed execution time for the page to allow for longer procesing times. */
	ini_set('max_execution_time', 600);  //300 seconds = 5 minutes

	$recordsImported = $dataCapture->downloadGoogleSearchAnalytics($_GET['domain'],$_GET['date']);
/* 	echo "<p>" . $recordsImported . " records succesfully imported to the database for " . $$_GET['domain'] . " for date: " . $_GET['date'] . ".</p>"; */
	echo number_format( $recordsImported ) . " records succesfully imported to the database for " . $_GET['domain'] . " for date: " . $_GET['date'] . ".";
	
} else {
	echo "<p>ERROR: Invalid request.  Domain: " . $_GET['domain'] . ", Date: " . $_GET['date'] . "</p>";
}
?>