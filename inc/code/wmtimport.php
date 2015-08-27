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
		const DB_TABLE_SEARCH_ANALYTICS = 'search-analytics';

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
		public function importGoogleSearchAnalytics($domain, $date, $searchType, $searchAnalytics) {
			$countImport = 0;
			foreach( $searchAnalytics->rows as $recordKey => $recordData ) {
				$deviceType = strtolower( $recordData['keys'][1] );
				$import = "INSERT into ".MySQL::DB_TABLE_SEARCH_ANALYTICS."(domain, date, search_engine, search_type, device_type, query, impressions, clicks, ctr, avg_position) values('$domain', '$date', 'google', '$searchType', '$deviceType', '{$recordData['keys'][0]}','{$recordData['impressions']}','{$recordData['clicks']}','{$recordData['ctr']}','{$recordData['position']}')";

				if( $GLOBALS['db']->query($import) ) {
					$countImport++;
				}
			}
			return $countImport;
		}
		
		
		
		
		
		
		
		
		
		
		
		

	}
?>