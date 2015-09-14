<?php
require_once realpath(dirname(__FILE__).'/inc/code/globalIncludes.php'); /* Load classes */

if( isset($_GET) && isset($_GET['type']) && isset($_GET['domain']) && isset($_GET['date']) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$_GET['date']) ) {
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

	switch( $_GET['type'] ) {
		case 'googleSearchAnalytics':
			$recordsImported = $dataCapture->downloadGoogleSearchAnalytics($_GET['domain'],$_GET['date'], $overrideSettings);
			if( !isset( $_GET['mode'] ) || $_GET['mode'] != 'return' ) {
				echo number_format( $recordsImported ) . " records succesfully imported to the database for " . $_GET['domain'] . " for date: " . $_GET['date'] . ".";
			}
			break;
		case 'bingSearchKeywords':
			$recordsImported = $dataCapture->downloadBingSearchKeywords($_GET['domain'],$_GET['date'], $overrideSettings);
			if( !isset( $_GET['mode'] ) || $_GET['mode'] != 'return' ) {
				echo number_format( $recordsImported ) . " records succesfully imported to the database for " . $_GET['domain'] . ".";
			}
			break;
	}

} else {
	echo "<p>ERROR: Invalid request.  Domain: " . $_GET['domain'] . ", Date: " . $_GET['date'] . "</p>";
}
?>