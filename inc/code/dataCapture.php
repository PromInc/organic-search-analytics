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
			
			/* Get date rante */
			$dates = $core->getDateRangeArray( $dateStart, $dateEnd );
			/* Loop through dates, removing those with data */
			foreach( $dates as $index => $date ) {
/* 				$numRows = $mysql->numRows( 'search_queries_table', array('date'=>$date,'domain'=>$website,'search_engine'=>'google') ); */
				$numRows = $mysql->numRows( MySQL::DB_TABLE_SEARCH_ANALYTICS, array('date'=>$date,'domain'=>$website,'search_engine'=>'google') );
				if( $numRows > 0 ) {
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
//			$searchAnalyticsRequest->setDimensions( array('query','device') );
			$searchAnalyticsRequest->setRowLimit( 5000 ); /* Valid options: 1-5000 */

			/* Loop through each of the dates requested */
/* 			foreach( $dateList as $dateListIndex => $date ) { */

				/* Set date for Search Analytics Request */
				$searchAnalyticsRequest->setStartDate( $date );
				$searchAnalyticsRequest->setEndDate( $date );
				/* Loop through each of the search types */
				foreach( $searchTypes as $searchTypesIndex => $searchType ) {

					/* Set search type in Search Analytics Request */
					$searchAnalyticsRequest->setSearchType( $searchType );

					/* Send Search Analytics Request */
/* This next line is where we are failing currently */
//echo "Script is failing to send the request to Google | inc/code/dataCapture.php";
					$searchAnalyticsResponse = $searchanalytics->query( $website, $searchAnalyticsRequest);

					/* Import Search Analytics to Database */
					if( is_object( $searchAnalyticsResponse ) ) {
						$wmtimport = new WMTimport();

						return $wmtimport->importGoogleSearchAnalytics( $website, $date, $searchType, $searchAnalyticsResponse );
					}
				}
/* 			} */

		}

	}
?>