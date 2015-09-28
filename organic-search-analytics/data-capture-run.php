<?php
require_once realpath(dirname(__FILE__).'/inc/code/globalIncludes.php'); /* Load classes */

if( isset( $_GET ) ) {
	$params = $_GET; /* Web Request */
} else {
	$params = $_SERVER['argv'][1]; /* CRON request */
}

if( isset($params) && isset($params['type']) && isset($params['domain']) && isset($params['date']) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$params['date']) ) {
	/* Set the max allowed execution time for the page to allow for longer procesing times. */
	ini_set('max_execution_time', 600);  //300 seconds = 5 minutes

	/* Set overrides from URL paramters */
	$overrideSettings = array();
	if( isset( $params['mode'] ) ) { $overrideSettings['mode'] = $params['mode']; }
	if( isset( $params['row_limit'] ) ) { $overrideSettings['row_limit'] = $params['row_limit']; }
	if( isset( $params['dimensions'] ) ) { $overrideSettings['dimensions'] = explode( ',', $params['dimensions'] ); }
	if( isset( $params['filters'] ) ) {
		$filters = explode( '|', $params['filters'] );
		foreach( $filters as $filter ) {
			$filterArgs = explode( ',', $filter );
			if( count( $filterArgs > 1 ) ) {
				$filterParmas = array( 'dimension' => $filterArgs[0], 'expression' => $filterArgs[1] );
				if( isset( $filterArgs[2] ) ) { $filterParmas['operator'] = $filterArgs[2]; } else { $filterParmas['operator'] = 'contains'; }
				$overrideSettings['filters'][] = $filterParmas;
			}
		}
	}
	if( isset( $params['aggregation_type'] ) ) { $overrideSettings['aggregation_type'] = $params['aggregation_type']; }

	switch( $params['type'] ) {
		case 'googleSearchAnalytics':
			$recordsImported = $dataCapture->downloadGoogleSearchAnalytics($params['domain'],$params['date'], $overrideSettings);
			if( !isset( $params['mode'] ) || $params['mode'] != 'return' ) {
				echo number_format( $recordsImported ) . " records succesfully imported to the database for " . $params['domain'] . " for date: " . $params['date'] . ".";
			}
			break;
		case 'bingSearchKeywords':
			$recordsImported = $dataCapture->downloadBingSearchKeywords($params['domain'],$params['date'], $overrideSettings);
			if( !isset( $params['mode'] ) || $params['mode'] != 'return' ) {
				echo number_format( $recordsImported ) . " records succesfully imported to the database for " . $params['domain'] . ".";
			}
			break;
	}

} else {
	echo "<p>ERROR: Invalid request.  Domain: " . $params['domain'] . ", Date: " . $params['date'] . "</p>";
}
?>