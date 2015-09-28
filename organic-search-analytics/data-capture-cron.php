<?php
require_once realpath(dirname(__FILE__).'/inc/code/globalIncludes.php'); /* Load classes */

/***** This script is to capture arguments passed on by command line via Cronjob *****/
$servType = $_SERVER['argv'][1];
$servDomain = $_SERVER['argv'][2];
//$servDate = $_SERVER['argv'][3];
$serveExecuteDate = date('Y-m-d');
$serveToDate = date('Y-m-d', strtotime('-4 days'));
$mailTo = "someone@example.com";
$mailSubject = "Search Analytics Data Automation Report";
$mailHeaders = "From: someone@example.com";

if (isset($_SERVER['argv'][1]) && isset($_SERVER['argv'][2]) ) {
	/* Set the max allowed execution time for the page to allow for longer procesing times. */
	ini_set('max_execution_time', 600);  //300 seconds = 5 minutes
	
	/* Set overrides from URL paramters */
	$overrideSettings = array();
	if( isset( $_GET['mode'] ) ) { $overrideSettings['mode'] = $_GET['mode']; }
	if( isset( $_GET['row_limit'] ) ) { $overrideSettings['row_limit'] = $_GET['row_limit']; }
	if( isset( $_GET['dimensions'] ) ) { $overrideSettings['dimensions'] = explode( ',', $_GET['dimensions'] ); }
	if( isset( $_GET['filters'] ) ) {
		$filters = explode( '|', $_GET['filters'] );
		foreach( $filters as $filter ) {
			$filterArgs = explode( ',', $filter );
			if( count( $filterArgs > 1 ) ) {
				$filterParmas = array( 'dimension' => $filterArgs[0], 'expression' => $filterArgs[1] );
				if( isset( $filterArgs[2] ) ) { $filterParmas['operator'] = $filterArgs[2]; } else { $filterParmas['operator'] = 'contains'; }
				$overrideSettings['filters'][] = $filterParmas;
			}
		}
	}
	if( isset( $_GET['aggregation_type'] ) ) { $overrideSettings['aggregation_type'] = $_GET['aggregation_type']; }

	switch( $_SERVER['argv'][1] ) {
		case 'googleSearchAnalytics':
			$recordsImported = $dataCapture->downloadGoogleSearchAnalytics($_SERVER['argv'][2],$serveToDate, $overrideSettings);
			if( !isset( $_GET['mode'] ) || $_GET['mode'] != 'return' ) {
				echo number_format( $recordsImported ) . " records succesfully imported to the database for " . $_SERVER['argv'][2] . " for date: " . $serveToDate . ".";
			}
			break;
		case 'bingSearchKeywords':
			$recordsImported = $dataCapture->downloadBingSearchKeywords($_SERVER['argv'][2],$serveToDate, $overrideSettings);
			if( !isset( $_GET['mode'] ) || $_GET['mode'] != 'return' ) {
				echo number_format( $recordsImported ) . " records succesfully imported to the database for " . $_SERVER['argv'][2] . ".";
			}
			break;
		}

	echo $mailMessage = "<p>Success!! These are cron arguments: " . "Argv 1/Type: " . $_SERVER['argv'][1] . "; Argv 2/Domain: " . $_SERVER['argv'][2] . "; Argv 3/Date of execution: " . $serveExecuteDate . "; Data will be extracted up to: " . $serveToDate . ".</p>";
	mail($mailTo, $mailSubject, $mailMessage, $mailHeaders);
} else {
//	echo "<p>ERROR: Invalid request.  Domain: " . $_GET['domain'] . ", Date: " . $_GET['date'] . "</p>";
	echo "<p>Failed!! ERROR: Invalid request.  Type: " . $_SERVER['argv'][1] . "; Domain: " . $_SERVER['argv'][2] . "; Date of execution: " . $serveExecuteDate . ".</p>";
}
?>
