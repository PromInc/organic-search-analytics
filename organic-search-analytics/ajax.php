<?php
	include_once realpath(dirname(__FILE__).'/inc/code/globalIncludes.php'); /* Load classes */

	if( isset( $_POST['requestType'] ) ) {
		switch( $_POST['requestType'] ) {
			case "saveReport":
				/* Report name and category */
				$formData = $core->parseQueryString( urldecode( $_POST['formData'] ) );

				/* Report Name */
				if( !isset( $formData['reportName'] ) || strlen( $formData['reportName'] ) <= 0 ) {
					$name = "untitled";
				} else {
					$name = $formData['reportName'];
				}

				/* Report Category */
				if( $formData['reportCatType'] == "new" ) {
					/* New Category */
					$reportCategory = $formData['reportCatNew'];
				} else {
					/* Existing Category */
					$reportCategory = intval( $formData['reportCatExisting'] );
				}

				/* Report Parameters */
				$reportParams = json_decode( urldecode( $formData['reportParams'] ) );

				/* Load report class */
				$report = new Reports();

				/* Save Report */
				$report->saveReport($reportParams->domain, $formData['reportName'], $reportCategory, $reportParams);
				break;
		}
	}

?>