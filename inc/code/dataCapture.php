<?php
	/**
	 *  PHP class for capturing data from API services
	 *
	 *  Copyright 2015 PromInc Productions. All Rights Reserved.
	 *  
	 *  @author: Brian Prom <prombrian@gmail.com>
	 *  @link:   http://promincproductions.com/blog/brian/
	 *
	 */

	class DataCapture
	{
	
		const GOOGLE_SEARCH_ANALYTICS_MAX_DATE_OFFSET = 4;
		const GOOGLE_SEARCH_ANALYTICS_MAX_DAYS = 90;
		
		/**
		 *  Query database.  Retrun all values from a table
		 *
		 *  @param $table     String   Table name
		 *
		 *  @returns   Object   Database records.  MySQL object
		 */
		public function checkNeededDataGoogleSearchAnalytics($website) {
			$core = new Core(); //Load core
			$mysql = new MySQL(); //Load MySQL

			$now = $core->now();

			/* Identify date range */
			$dateStartOffset = self::GOOGLE_SEARCH_ANALYTICS_MAX_DATE_OFFSET+self::GOOGLE_SEARCH_ANALYTICS_MAX_DAYS;
			$dateStart = date('Y-m-d', strtotime('-'.$dateStartOffset.' days', $now));
			$dateEnd = date('Y-m-d', strtotime('-'.self::GOOGLE_SEARCH_ANALYTICS_MAX_DATE_OFFSET.' days', $now));

			/* Query database for dates with data */
			$query = "SELECT COUNT( DISTINCT date ) AS record, date FROM ".MySQL::DB_TABLE_SEARCH_ANALYTICS." WHERE domain LIKE '".$website."' AND date >= '".$dateStart."' AND date <= '".$dateEnd."' GROUP BY date";
			$result = $mysql->query( $query );

			/* Create array from database response */
			$datesWithData = array();
			foreach( $result as $row ) {
				array_push( $datesWithData, $row['date'] );
			}

			/* Get date rante */
			$dates = $core->getDateRangeArray( $dateStart, $dateEnd );

			/* Loop through dates, removing those with data */
			foreach( $dates as $index => $date ) {
				if( in_array( $date, $datesWithData ) ) {
					unset( $dates[ $index ] );
				}
			}

			/* Reindex dates array */
			$dates = array_values($dates);

			$returnArray = array(
				'dateStart' => $dateStart,
				'dateEnd' => $dateEnd,
				'datesWithNoData' => $dates
			);

			return $returnArray;
		}


		public function downloadGoogleSearchAnalytics($website,$date) {
			$importCount = 0;
			/* Website requires a trailing slash */
//			if( substr( $website, -1 ) != "/" ) { $website .= "/"; }

			/* Authorize Google via oAuth 2.0 */
			$gapiOauth = new GAPIoAuth();
			$client = $gapiOauth->LogIn();

			/* Define what search types to request from Google Search Analytics */
			$searchTypes = array('web','image','video');

			/* Load Google Webmasters API */
			$webmasters = new Google_Service_Webmasters($client);

			/* Load Search Analytics API */
			$searchAnalyticsRequest = new Google_Service_Webmasters_SearchAnalyticsQueryRequest($client);

			/* Prepare Search Analytics Resource */
			$searchanalytics = $webmasters->searchanalytics;

			/* Build Search Analytics Request */
			$searchAnalyticsRequest->setDimensions(['query','device']);
			$searchAnalyticsRequest->setRowLimit( 5000 ); /* Valid options: 1-5000 */

			/* Set date for Search Analytics Request */
			$searchAnalyticsRequest->setStartDate( $date );
			$searchAnalyticsRequest->setEndDate( $date );
			/* Loop through each of the search types */
			foreach( $searchTypes as $searchType ) {
				/* Set search type in Search Analytics Request */
				$searchAnalyticsRequest->setSearchType( $searchType );

				/* Send Search Analytics Request */
				$searchAnalyticsResponse = $searchanalytics->query( $website, $searchAnalyticsRequest);

				/* Import Search Analytics to Database */
				if( is_object( $searchAnalyticsResponse ) ) {
					$wmtimport = new WMTimport();
					$importCount += $wmtimport->importGoogleSearchAnalytics( $website, $date, $searchType, $searchAnalyticsResponse );
				}
			}

			return $importCount;

		}

	}
?>