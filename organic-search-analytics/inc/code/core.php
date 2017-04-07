<?php
	/**
	 *  PHP class for core functions
	 *
	 *  Copyright 2017 PromInc Productions. All Rights Reserved.
	 *
	 *  Licensed under the Apache License, Version 2.0 (the "License");
	 *  you may not use this file except in compliance with the License.
	 *  You may obtain a copy of the License at
	 *
	 *     http://www.apache.org/licenses/LICENSE-2.0
	 *
	 *  Unless required by applicable law or agreed to in writing, software
	 *  distributed under the License is distributed on an "AS IS" BASIS,
	 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 *  See the License for the specific language governing permissions and
	 *  limitations under the License.
	 *
	 *  @author: Brian Prom <prombrian@gmail.com>
	 *  @link:   http://promincproductions.com/blog/brian/
	 */

	class Core
	{


		const ENABLED = 1;
		const DISABLED = 0;

		const WARNING = "WARNING";
		const ERROR = "ERROR";
		const INFO = "INFO";
		const DEBUG = "DEBUG";

		const NEWLINE = "\n";
		const TAB = "\t";

		const DS = "/";


		/**
		 *  Connect to MySQL
		 *
		 *  @returns   Mixed   null on success,
		 *             else error(string) upon failure
		 */
		public function mysql_connect_db()
		{
			$db = new mysqli( Config::DB_CONNECTION_DOMAIN, Config::DB_CONNECTION_USER, Config::DB_CONNECTION_PASSWORD, Config::DB_CONNECTION_DATABASE );
			if ($db->connect_errno) {
					return "Database Error: (" . $db->connect_errno . ") " . $db->connect_error;
			}
			return $db;
		}


		/*
		 *  Adapted from:
		 *  http://boonedocks.net/mike/archives/137-Creating-a-Date-Range-Array-with-PHP.html
		 *
		 *  @param $strDateFrom  String   From Date   Accepted formats: YYYY-MM-DD, YYYYMMDD
		 *  @param $strDateTo  String   To Date   Accepted formats: YYYY-MM-DD, YYYYMMDD
		 *  @return Array  Returns Dates between the two dates
		 *                 else false.
		 */
		public function getDateRangeArray( $strDateFrom, $strDateTo ) {
			$dateFromValid = $dateToValid = false;
			if( strpos( $strDateFrom, "-" ) !== false ) {
				$dateFromValid = true;
			} else {
				if( strlen( $strDateFrom ) == 8 ) {
					$strDateFrom = substr( $strDateFrom, 0, 4 ) . "-" . substr( $strDateFrom, 4, 2 ) . "-" . substr( $strDateFrom, 6, 2 );
					$dateFromValid = true;
				}
			}
			if( strpos( $strDateTo, "-" ) !== false ) {
				$dateToValid = true;
			} else {
				if( strlen( $strDateTo ) == 8 ) {
					$strDateTo = substr( $strDateTo, 0, 4 ) . "-" . substr( $strDateTo, 4, 2 ) . "-" . substr( $strDateTo, 6, 2 );
					$dateToValid = true;
				}
			}

			if( $dateFromValid === true && $dateToValid === true ) {
				$aryRange = array();
				$iDateFrom = mktime( 1, 0, 0, substr( $strDateFrom, 5, 2 ), substr( $strDateFrom, 8, 2 ), substr( $strDateFrom, 0, 4 ) );
				$iDateTo = mktime( 1, 0, 0, substr( $strDateTo, 5, 2), substr( $strDateTo, 8, 2 ), substr( $strDateTo, 0, 4 ) );

				if( $iDateTo >= $iDateFrom ) {
						while( $iDateFrom<$iDateTo ) {
							array_push( $aryRange, date( 'Y-m-d',$iDateFrom ) );
							$iDateFrom += 86400; // add 24 hours
						}
				}
				return $aryRange;
			} else {
				return false;
			}
		}


		/**
		 *  Calculate the number of days between two dates
		 *
		 *  @param $start     String   Start date
		 *  @param $end     String   End Date
		 *  @param $include_end_day     Bool   Whether or not to include the end date in calculation
		 *                              Default: true
		 *
		 *  @returns   Int   Number of days between the two dates
		 */
		public function getNumDays( $start, $end, $include_end_day = true) {
			$num_days = floor( ( strtotime( $end ) - strtotime( $start ) ) / ( 60*60*24 ) );
			if( $include_end_day ) { $num_days = $num_days + 1; }
			return $num_days;
		}


		/**
		 *  Get the current date/time
		 *
		 *  @param $format(optional)     String   Format to be returned
		 *                Defualt: NULL
		 *
		 *  @returns   Mixed   Depeneds on the format requested
		 */
		public function now($format = null) {
			$now = time();
			return $now;
		}


		/**
		 *  Send CURL request
		 *
		 *  @param $url     String   URL to request
		 *
		 *  @returns   Mixed   Response from CURL request
		 */
		public function curlRequest($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_ENCODING, true);
			$result = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);

			return $result;
		}


		/**
		 *  Parse Query String into Array
		 *
		 *  @param $queryString     String   Query string to be parsed
		 *
		 *  @returns   Array   Key = query parameter.  Value = parameter value.
		 */
		public function parseQueryString($queryString) {
			if( $queryString ) {
				$queryParams = array();
				$pairs = explode( "&", $queryString );
				foreach( $pairs as $pair ) {
					$keyValue = explode( "=", $pair );
					$queryParams[ $keyValue[0] ] = $keyValue[1];
				}
			}
			return $queryParams;
		}


		/**
		 *  Redirect page to specified URL with specified HTTP response header
		 *
		 *  @param $url    String   URL to redirect to
		 *  $param $code    int   Response code
		 */
		public function redirect($url, $code) {
			switch( $code ) {
				case 301:
					$msg = "HTTP/1.1 301 Moved Permanently";
					break;
				case 302:
					$msg = "HTTP/1.1 302 Found";
					break;
				default:
					$msg = "HTTP/1.1 301 Moved Permanently";
					break;
			}
			header( $msg );
			header("Location: ". $url );
			exit;
		}


	}
?>