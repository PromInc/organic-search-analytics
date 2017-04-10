<?php
	/**
	 *  PHP class for importing CSV files from Google Webmaster Tools.
	 *
	 *  Copyright 2015 PromInc Productions. All Rights Reserved.
	 *  
	 *  @author: Brian Prom <prombrian@gmail.com>
	 *  @link:   http://promincproductions.com/blog/brian/
	 *
	 */

	class WMTimport
	{


		public $debug;


		function __construct() {
			$this->debug = new DebugLogger(); //Load Debugging Logger
		}


		/**
		 *  Break apart the filename to return the data pieces
		 *
		 *  @param $fileName     String   Filename of CSV file
		 */
		public function getDataFromReportName($fileName)
		{
			$fileNameParts = explode( "-", $_GET['file'] );
			$return = array();

			$return['report'] = strtolower( $fileNameParts[0] );
			$return['searchEngine'] = $fileNameParts[1];
			$return['domain'] = $fileNameParts[2];
			$return['dateStart'] = $fileNameParts[3];

			if( strpos( $fileNameParts[3], "_to_" ) ) {
				$dates = explode( "_to_", $fileNameParts[3] );
				$return['dateStart'] = $dates[0];
				$return['dateEnd'] =  $dates[1];
			} else {
				$return['dateStart'] = $return['dateEnd'] = $fileNameParts[3];
			}

			if( strpos( $fileNameParts[4], ".csv" ) ) {
				$return['type'] = substr( $fileNameParts[4], 0, strpos( $fileNameParts[4], ".csv" ) );
			} else {
				$return['type'] = $fileNameParts[4];
			}

			return $return;
		}


		/**
		 *  Import array of Google Search Analytics to database
		 *
		 *  @param $domain     String   Domain name for record
		 *  @param $date     String   Date for record YYYY-MM-DD
		 *  @param $searchType     String   Search type for record (web, image, video)
		 *  @param $searchAnalytics     Object   Search Analytics Results
		 *
		 *  @returns   Int   Count of records imported
		 */
		public function importGoogleSearchAnalytics($domain, $date, $searchType, $searchAnalytics, $dimensionMap) {
			$countImport = 0;
			foreach( $searchAnalytics->rows as $recordKey => $recordData ) {
				/* Prep data */
				$domain = addslashes( $domain );
				$searchType = addslashes( $searchType );
				$query = ( isset( $dimensionMap['query'] ) ? addslashes( $recordData['keys'][$dimensionMap['query']] ) : NULL );
				$page = ( isset( $dimensionMap['page'] ) ? addslashes( $recordData['keys'][$dimensionMap['page']] ) : NULL );
				$deviceType = ( isset( $dimensionMap['device'] ) ? addslashes( strtolower( $recordData['keys'][$dimensionMap['device']] ) ) : NULL );
				$country = ( isset( $dimensionMap['country'] ) ? addslashes( strtolower( $recordData['keys'][$dimensionMap['country']] ) ) : NULL );

				$import = "INSERT into ".MySQL::DB_TABLE_SEARCH_ANALYTICS."(domain, date, search_engine, search_type, device_type, country, query, page, impressions, clicks, ctr, avg_position) values('$domain', '$date', 'google', '$searchType', '$deviceType', '$country', '{$query}', '{$page}', '{$recordData['impressions']}', '{$recordData['clicks']}', '{$recordData['ctr']}', '{$recordData['position']}')";

				if( $GLOBALS['db']->query($import) ) {
					$countImport++;
				} else {
					if( config::DEBUG_LOGGER == Core::ENABLED ) {
						$this->debug->debugLog($GLOBALS['db']->error,Core::ERROR);
					}
				}
			}
			return $countImport;
		}


		/**
		 *  Import array of Bing Search Keywords to database
		 *
		 *  @param $domain     String   Domain name for record
		 *  @param $searchKeywords     Object   Search Keywords Results
		 *
		 *  @returns   Int   Count of records imported
		 */
		public function importBingSearchKeywords($domain, $searchKeywords) {
			$searchKeywords = json_decode($searchKeywords);
			if( isset( $searchKeywords->ErrorCode ) ) {
				$message = "Error connectiong to the Bing API.  ErrorCode: ".$searchKeywords->ErrorCode;
				if( isset( $searchKeywords->Message ) ) {
					$message .= "  ".$searchKeywords->Message;
				}
				$this->debug->debugLog($message, Core::ERROR, "BingApiAuthorization.log");
				$countImport = -1;
			} elseif ( isset( $searchKeywords->d ) ) {
				$countImport = 0;
	
				/* Check for prior import in DB */
				$lastImported = "SELECT MAX(date) AS 'lastImported' FROM ".MySQL::DB_TABLE_SEARCH_ANALYTICS." WHERE domain = '".$domain."' AND search_engine = 'bing'";
				if( $lastImportedResult = $GLOBALS['db']->query($lastImported) ) {
					$lastImportedDate = $lastImportedResult->fetch_row()[0];
	
					foreach( array_reverse( $searchKeywords->d ) as $recordKey => $recordData ) {
						preg_match( '/\d+/', $recordData->Date, $dateUnixMatch );
						$ctr = $recordData->Clicks / $recordData->Impressions;
						$date = date( "Y-m-d", substr($dateUnixMatch[0], 0, strlen($dateUnixMatch[0])-3) );
						$query = addslashes( $recordData->Query );
						$domain = addslashes( $domain );
		
						if( $date > $lastImportedDate ) {
							$import = "INSERT into ".MySQL::DB_TABLE_SEARCH_ANALYTICS."(domain, date, search_engine, query, impressions, clicks, ctr, avg_position, avg_position_click) values('$domain', '$date', 'bing', '{$query}','{$recordData->Impressions}','{$recordData->Clicks}','{$ctr}','{$recordData->AvgImpressionPosition}', '{$recordData->AvgClickPosition}')";
		
							if( $GLOBALS['db']->query($import) ) {
								$countImport++;
							} else {
								if( config::DEBUG_LOGGER == Core::ENABLED ) {
									$this->debug->debugLog($GLOBALS['db']->error,Core::ERROR);
								}
							}
						}
					}
				}
			}
			return $countImport;
		}


	}
?>