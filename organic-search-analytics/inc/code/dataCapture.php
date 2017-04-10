<?php
	/**
	 *  PHP class for capturing data from API services
	 *
	 *  Copyright 2017 PromInc Productions. All Rights Reserved.
	 *  
	 *  @author: Brian Prom <prombrian@gmail.com>
	 *  @link:   http://promincproductions.com/blog/brian/
	 *
	 */

	class DataCapture
	{


		const GOOGLE_SEARCH_ANALYTICS_MAX_DATE_OFFSET = 4;
		const GOOGLE_SEARCH_ANALYTICS_MAX_DAYS = 90;


		public $core;
		public $mysql;
		public $dimensions;


		function __construct() {
			$this->core = new Core(); //Load core
			$this->mysql = new MySQL(); //Load MySQL
			if( defined( 'config::DEBUG_LOGGER' ) ) {
				$this->debug = new DebugLogger(); //Load Debugging Logger
			}
		}


		/**
		 *  Get the list of dimensions to capture as defined in the settings page
		 *
		 *  @returns    String   Comma separated list of dimensions
		 */
		public function getDimensions() {
			if( !$this->dimensions ) {
				$siteSettings = $this->mysql->getSettings("settings");
				$dimensions = array();
				foreach( $siteSettings as $key => $value ) {
					if( substr( $key, 0, 32 ) == "google_search_console_dimensions" && $value == "On" ) {
						$dimensions[] = substr($key, 33 );
					}
				}

				uasort($dimensions, function($a, $b) {
					static $sizes = array('query' => 0, 'page' => 1, 'device' => 2, 'country' => 3);
					return $sizes[strtolower($a)] - $sizes[strtolower($b)];
				});

				$this->dimensions = array_values($dimensions);
			}
			return $this->dimensions;
		}


		/**
		 *  Default settings for Google Search Analytics
		 */
		private function defaultGoogleSearchAnalyticsSettings() {
			return array(
			'mode' => 'import', /* What to do with the data.  Valid options: import, return */
			'dimensions' => $this->getDimensions(),
			'row_limit' => 5000 /* Number of rows to capture from Google.  Valid options: 1-5000 */
			);
		}


		/**
		 *  Get authorized sites from Google Search Console
		 *
		 *  @returns   Array   Site URL and permission level
		 */
		public function getSitesGoogleSearchConsole() {
			/* Authorize Google via oAuth 2.0 */
			$gapiOauth = new GAPIoAuth();
			try {
				$client = $gapiOauth->LogIn();

				/* Load Google Webmasters API */
				$webmasters = new Google_Service_Webmasters($client);

				/* Load sites functions */
				$siteServices = $webmasters->sites;

				/* Get list of sites */
				$gSites = $siteServices ->listSites();

				$return = array();
				foreach( $gSites->getSiteEntry() as $site ) {
					$return[] = array( 'url' => $site['siteUrl'] );
				}
			} catch (Exception $e) {
				$return['googleapi'] = array( 'warn' => 'Couldn\'t connect to the Google API.' );
				$this->debug->debugLog("Couldn't connect to the Google API: ".$e->getMessage(), "GoogleApiAuthorization.log");
			}

			return $return;
		}


		/**
		 *  Get authorized sites from Bing Webmaster Tools
		 *
		 *  @param $enabledCheck     Bool   If enabled sites only should be returned.  Default false
		 *
		 *  @returns   Array
		 */
		public function getSitesBingWebmaster($enabledCheck = FALSE) {
			$bing = new BingWebmasters(); // Load Bing Webmasters API

			$bingSites = json_decode( $bing->requestApi( config::CREDENTIALS_BING_API_KEY, 'GetUserSites' ) );

			if( isset( $bingSites->ErrorCode ) ) {
				$return['bingapi'] = array( 'warn' => 'Couldn\'t connect to the Bing API.' );
				if( isset( $bingSites->Message ) ) {
					$this->debug->debugLog("Couldn't connect to the Bing API: ".$bingSites->Message, "BingApiAuthorization.log");				
				}
			} elseif( isset( $bingSites->d ) ) {
				$return = array();
				if( $bingSites && !isset( $bingSites->ErrorCode ) ) {
					foreach( $bingSites->d as $site ) {
						$return[] = array( 'url' => $site->Url );
					}
				}
			}

			return $return;
		}


		/**
		*
		*/
		public function getGoogleAvailableDates() {
			$dateStartOffset = self::GOOGLE_SEARCH_ANALYTICS_MAX_DATE_OFFSET+self::GOOGLE_SEARCH_ANALYTICS_MAX_DAYS;
			$dateStart = date('Y-m-d', strtotime('-'.$dateStartOffset.' days', $this->core->now()));
			$dateEnd = date('Y-m-d', strtotime('-'.self::GOOGLE_SEARCH_ANALYTICS_MAX_DATE_OFFSET.' days', $this->core->now()));
			return array( 'start' => $dateStart, 'end' => $dateEnd );
		}


		/**
		*
		*/
		public function getGoogleDatesWithData($website, $availableToDownload=false) {
			/* Query database for dates with data */
			$query = "SELECT COUNT( DISTINCT date ) AS record, date FROM ".MySQL::DB_TABLE_SEARCH_ANALYTICS." WHERE search_engine = 'google' AND domain LIKE '".$website."'";
			if( $availableToDownload ) {
				/* Identify date range */
				$dateRange = $this->getGoogleAvailableDates();
				$dateStart = $dateRange['start'];
				$dateEnd = $dateRange['end'];
				$query .= " AND date >= '".$dateStart."' AND date <= '".$dateEnd."' GROUP BY date";
			}
			return $this->mysql->query( $query );
		}


		/**
		 *  Query database.  Retrun all values from a table
		 *
		 *  @param $table     String   Table name
		 *
		 *  @returns   Object   Database records.  MySQL object
		 */
		public function checkNeededDataGoogleSearchAnalytics($website) {
			/* Identify date range */
			$dateRange = $this->getGoogleAvailableDates();
			$dateStart = $dateRange['start'];
			$dateEnd = $dateRange['end'];

			/* Query database for dates with data */
			$result = $this->getGoogleDatesWithData($website, true);

			/* Create array from database response */
			$datesWithData = array();
			foreach( $result as $row ) {
				array_push( $datesWithData, $row['date'] );
			}

			/* Get date range */
			$dates = $this->core->getDateRangeArray( $dateStart, $dateEnd );

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


		/**
		 *  Request Google Search Analytics API
		 *
		 *  @param $website     String   Website URL that is enabled in Google Search Console
		 *  @param $date     Date (YYYY-MM-DD)   Date for which to request data
		 *  @param $overrides     Array   Values to override default settings for request
		 *
		 *  @returns   Integer,array   Number of records found or var_dump of returned data from Google depending on mode
		 */
		public function downloadGoogleSearchAnalytics( $website, $date, $overrides = array() ) {
			$params = array_merge( $this->defaultGoogleSearchAnalyticsSettings(), $overrides );

			$importCount = 0;

			/* Authorize Google via oAuth 2.0 */
			$gapiOauth = new GAPIoAuth();
			try {
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
				$searchAnalyticsRequest->setDimensions( $params['dimensions'] );
				$searchAnalyticsRequest->setRowLimit( $params['row_limit'] ); /* Valid options: 1-5000 */

				/* Set date for Search Analytics Request */
				$searchAnalyticsRequest->setStartDate( $date );
				$searchAnalyticsRequest->setEndDate( $date );

				if( isset( $params['filters'] ) || isset( $params['groups'] ) ) {
					$searchAnalyticsDimensionFilterGroup = new Google_Service_Webmasters_ApiDimensionFilterGroup;
				}

				if( isset( $params['filters'] ) ) {
					$filters = array();
					foreach( $params['filters'] as $filter ) {
						$dimensionFilter = new Google_Service_Webmasters_ApiDimensionFilter;
						$dimensionFilter->setDimension( $filter['dimension'] );
						$dimensionFilter->setOperator( $filter['operator'] );
						$dimensionFilter->setExpression( $filter['expression'] );
						$filters[] = $dimensionFilter;
					}
					$searchAnalyticsDimensionFilterGroup->setFilters( $filters );
					$searchAnalyticsRequest->setDimensionFilterGroups( array( $searchAnalyticsDimensionFilterGroup ) );
				}

				if( isset( $params['groups'] ) ) {
					/* TODO */
					// $dimensionFilterGroups['groups'] = $params['groups'];
				}

				if( isset( $params['aggregation_type'] ) ) {
					$searchAnalyticsRequest->setAggregationType( $params['aggregation_type'] );
				}

				$dimensionMap = array_flip( $this->getDimensions() );

				/* Loop through each of the search types */
				foreach( $searchTypes as $searchType ) {
					/* Set search type in Search Analytics Request */
					$searchAnalyticsRequest->setSearchType( $searchType );

					/* Send Search Analytics Request */
					$searchAnalyticsResponse = $searchanalytics->query( $website, $searchAnalyticsRequest);

					/* Import Search Analytics to Database */
					if( is_object( $searchAnalyticsResponse ) ) {
						switch( $params['mode'] ) {
							case 'import':
								$wmtimport = new WMTimport();
								$importCount += $wmtimport->importGoogleSearchAnalytics( $website, $date, $searchType, $searchAnalyticsResponse, $dimensionMap );
								break;
							case 'return':
								var_dump( $searchAnalyticsResponse );
								break;
						}
					}
				}
			} catch (Exception $e) {
				$importCount = -1;
				$this->debug->debugLog("Couldn't authorize with the Google API: ".$e->getMessage(),  "GoogleApiAuthorization.log");
			}
			return $importCount;
		}


		/**
		 *  Request Bing Search Keywords from Bing Webmaster Tools API
		 *
		 *  @param $website     String   Website URL that is enabled in Bing Webmaster Tools
		 *
		 *  @returns   Integer   Number of records imported to the database
		 */
		public function downloadBingSearchKeywords($website) {
			$importCount = 0;

			$bing = new BingWebmasters(); // Load Bing Webmasters API
			$method = 'GetQueryStats';
			$apiData = $bing->requestApi( Config::CREDENTIALS_BING_API_KEY, $method, $website );

			/* Import Search Analytics to Database */
			if( $apiData ) {
				$wmtimport = new WMTimport();
				$importCount += $wmtimport->importBingSearchKeywords( $website, $apiData );
			}

			return $importCount;
		}


	}
?>