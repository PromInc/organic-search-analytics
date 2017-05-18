<?php
	/**
	 *  PHP class for report functions
	 *
	 *  Copyright 2015 PromInc Productions. All Rights Reserved.
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

	class Reports
	{


		const EMPTY_RESULT_PLACEHOLDER = "not set";


		public $core;


		function __construct() {
			$this->core = new Core(); //Load core
		}


		/**
		 *  Import array of Bing Search Keywords to database
		 *
		 *  @param $domain     String   Domain name for record
		 *  @param $searchKeywords     Object   Search Keywords Results
		 *
		 *  @returns   Int   Count of records imported
		 */
		public function getSavedReportCategories() {
			$query = "SELECT id, name, description FROM ".MySQL::DB_TABLE_SAVED_REPORT_CATEGORIES;
			if( $reportCategories = $GLOBALS['db']->query($query) ) {
				/* Put categories into an array */
				$categories = array();
				foreach( $reportCategories as $category ) {
					$categories[ $category['id'] ] = array(
						'name' => $category['name'],
						'description' => $category['description']
					);
				}

				return $categories;
			} else {
				return array( 0, 'Uncategorized' );
			}
			
		}


		/**
		 *  Save Report settings to database
		 *
		 *  @param $domain     String   Domain name for record
		 *  @param $name     String   Name of the report
		 *  @param $category     Integer or String   Integer - category ID, String - name of a new category
		 *  @param $params     Array or Object   Parameters that make up the report
		 *
		 *  @returns   bool
		 */
		public function saveReport($domain, $name, $category, $params) {
			/* Handle category */
			if( gettype( $category ) === "string" ) {
				/* New Category */
				$category = self::saveReportCategory("", $category, "", true);
			} elseif(gettype( $category ) === "integer" ) {
				/* Existing Category */
			}

			/* Prepare paramters */
			if( gettype( $params ) === "object" ) {
				$params = get_object_vars( $params );
			}
			$params = serialize($params);

			$name = addslashes($name);
			$category = addslashes($category);

			/* Prepare request */
			$valueString = "NULL, '{$domain}', '{$name}', '{$category}', '{$params}'";

			/* Send Query */
			$saveReport = MySQL::qryDBinsert( MySQL::DB_TABLE_SAVED_REPORTS, $valueString );

			/* Return query request status */
			return $saveReport;
		}


		/**
		 *  Save Report Category to database
		 *
		 *  @param $id     Integer   ID of the report category
		 *  @param $name     String   Name of the report category
		 *  @param $new     Bool   Default: false.  If false, overwrite existing category.  If true, create new category.
		 *
		 *  @returns   Integer, Bool(True)   New Categories return integer.  Existing categories return true(bool).
		 */
		public function saveReportCategory($id = NULL, $name, $description, $new = false) {
			$name = addslashes($name);
			$description = addslashes($description);

			if( $new ) {
				/* Format values */
				$valueString = "NULL, '{$name}', '{$description}'";
				/* Send Query */
				$mysqli = MySQL::qryDBinsert( MySQL::DB_TABLE_SAVED_REPORT_CATEGORIES, $valueString, true );
				/* Return new category ID */
				return $mysqli->insert_id;
			} else {
				/* Format values */
				$matchParams = array( 'id' => $id );
				$updateParams = array( 'name' => $name, 'description' => $description );
				/* Send Query */
				$mysqli = MySQL::qryDBupdate( MySQL::DB_TABLE_SAVED_REPORT_CATEGORIES, $matchParams, $updateParams );
				/* Return true */
				return true;
			}
		}


		/**
		 *  Get Saved Report by Id
		 *
		 *  @returns   array
		 */
		public function getSavedReport($id) {
			if( $id ) {
				$query = "SELECT paramaters FROM ".MySQL::DB_TABLE_SAVED_REPORTS." WHERE id = '".$id."'";

				if( $reportParams = $GLOBALS['db']->query($query) ) {
					/* Get result from DB object */
					$parameters = $reportParams->fetch_row();
					/* Unserialize data */
					$parameters = unserialize( $parameters[0] );
					/* Return array */
					return $parameters;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}


		/**
		 *  Get Saved Reports by category
		 *
		 *  @returns   array
		 */
		public function getSavedReportsByCategory() {
			/* Declare return array */
			$return = array();

			/* Get Categories */
			$reportCategories = self::getSavedReportCategories();
			/* Add categories to return array */
			$return['categories'] = $reportCategories;

			/* Loop through categories */
			foreach( $reportCategories as $id => $data ) {
				/* Get reports by category */
				$query = "SELECT id, domain, name FROM ".MySQL::DB_TABLE_SAVED_REPORTS." WHERE category = ".$id;

				if( $reports = $GLOBALS['db']->query($query) ) {
					foreach( $reports as $report ) {
						/* Add report to return array listed under category */
						$return['categories'][$id]['reports'][] = $report;
					}
				}
			}
			/* Return values */
			return $return;
		}


		/**
		 *  Get HTML display of Saved Reports by Category
		 *
		 *  @param $reportsByCategory     Array    Array of reports saved by category as returned by getSavedReportsByCategory
		 *
		 *  @returns   Html
		 */
		public function getSavedReportsByCategoryHtml( $reportsByCategory ) {
			$return = "";
			if( is_array( $reportsByCategory ) ) {
				/* Loop through categories */
				foreach( $reportsByCategory['categories'] as $cat => $catData ) {
					/* If category has reports */
					if( isset( $catData['reports'] ) ) {
						/* Display category listing */
						$return .= '<h3>'.$catData['name'].'</h3>';
						$return .= '<ul>';
						/* Loop through reports */
						foreach( $catData['reports'] as $report ) {
							/* Display report link */
							$return .= '<li><a href="report.php?savedReport='.$report['id'].'">'.$report['name'].'</a></li>';
						}
						$return .= '</ul>';
					}
				}
			}
			return $return;
		}


		/**
		 *  Get HTML display of Saved Reports by Category
		 *
		 *  @param $reportParams     Array    Array of reports saved by category as returned by getSavedReportsByCategory
		 *
		 *  @returns   Html
		 */
		public function getReportQueryAndHeading( $reportParams ) {
			$return = array();
			if( is_array( $reportParams ) ) {

				$whereClause = $return['chartLabel'] = "";
				$return['whereClauseItemsTable'] = $return['pageHeadingItems'] = [];

				if( isset( $reportParams['groupBy'] ) ) {
					$return['pageHeadingItems'][] = "<span>Report Type:</span> " . ucfirst($reportParams['groupBy']);
				}
				
				if( isset( $reportParams['domain'] ) && $reportParams['domain'] > "" ) {
					$return['whereClauseItemsTable'][] = "domain = '" . $reportParams['domain'] . "'";
					$return['pageHeadingItems'][] = "<span>Domain:</span> <a href=\"" . $reportParams['domain'] . "\" target=\"_blank\">" . $reportParams['domain'] . "<i class=\"fa fa-external-link reportLinkExt\" aria-hidden=\"true\"></i></a>";
				}
				if( isset( $reportParams['query'] ) && $reportParams['query'] > "" ) {
					switch( $reportParams['queryMatch'] ) {
						case "broad":
						default:
							$return['whereClauseItemsTable'][] = "query LIKE '%" . $reportParams['query'] . "%'";
							break;
						case "exact":
							$return['whereClauseItemsTable'][] = "query = '" . $reportParams['query'] . "'";
							break;
					}
					$return['pageHeadingItems'][] = "<span>Query:</span> <a href=\"https://www.google.com/search?q=" . urlencode( $reportParams['query'] ) . "\" target=\"_blank\">" . $reportParams['query'] . "<i class=\"fa fa-external-link reportLinkExt\" aria-hidden=\"true\"></i></a>" . (isset($reportParams['queryMatch'])?" (".$reportParams['queryMatch'].")":"");
					$return['chartLabel'] = $reportParams['query'] . (isset($reportParams['queryMatch'])?" (".$reportParams['queryMatch'].")":"");
				}
				if( isset( $reportParams['page'] ) && $reportParams['page'] > "" ) {
					switch( $reportParams['pageMatch'] ) {
						case "broad":
						default:
							$return['whereClauseItemsTable'][] = "page LIKE '%" . $reportParams['page'] . "%'";
							break;
						case "exact":
							$return['whereClauseItemsTable'][] = "page = '" . $reportParams['page'] . "'";
							break;
					}
					
					$pageHeading_page = "<span>Page:</span> ";
					if( isset($reportParams['pageMatch']) && $reportParams['pageMatch'] == "exact" ) {
						$pageHeading_page .= '<a href="' . $reportParams['page'] . '" target="_blank">';
					}
					$pageHeading_page .= $reportParams['page'];
					if( isset($reportParams['pageMatch']) && $reportParams['pageMatch'] == "exact" ) {
						$pageHeading_page .= '<i class="fa fa-external-link reportLinkExt" aria-hidden="true"></i></a>';
					}
					$pageHeading_page .= (isset($reportParams['pageMatch'])?" (".$reportParams['pageMatch'].")":"");
					$return['pageHeadingItems'][] = $pageHeading_page;
					$return['chartLabel'] = $reportParams['page'] . (isset($reportParams['pageMatch'])?" (".$reportParams['pageMatch'].")":"");
				}
				if( isset( $reportParams['search_type'] ) && $reportParams['search_type'] > "" ) {
					if( $reportParams['search_type'] != "ALL" ) {
						$return['whereClauseItemsTable'][] = "search_type = '" . $reportParams['search_type'] . "'";
					}
					$return['pageHeadingItems'][] = "<span>Search Type:</span> " . $reportParams['search_type'];
				}
				if( isset( $reportParams['device_type'] ) && $reportParams['device_type'] > "" ) {
					if( $reportParams['device_type'] != "ALL" ) {
						$return['whereClauseItemsTable'][] = "device_type = '" . $reportParams['device_type'] . "'";
					}
					$return['pageHeadingItems'][] = "<span>Device Type:</span> " . $reportParams['device_type'];
				}

				/* Country */
				if( isset( $reportParams['country'] ) && $reportParams['country'] > "" ) {
					if( $reportParams['country'] != "ALL" ) {
						$return['whereClauseItemsTable'][] = "country = '" . $reportParams['country'] . "'";
					}
					$return['pageHeadingItems'][] = "<span>Country:</span> " . strtoupper( $reportParams['country'] );
				}

				if( isset( $reportParams['date_start'] ) && $reportParams['date_start'] > 0 && $reportParams['date_type'] == 'hard_set' ) {
					if( isset( $reportParams['date_end'] ) && $reportParams['date_end'] > 0 ) {
						$return['whereClauseItemsTable'][] = "date >= '" . $reportParams['date_start'] . "' AND date <= '" . $reportParams['date_end'] . "'";
						$num_days = $this->core->getNumDays( $reportParams['date_start'], $reportParams['date_end'] );
						$return['pageHeadingItems'][] = "<span>Dates:</span> " . $reportParams['date_start'] . " to " . $reportParams['date_end'] . " (" . $num_days . " day" . ( $num_days > 1 ? "s" : "" ) . ")";
					} else {
						$return['whereClauseItemsTable'][] = "date = '" . $reportParams['date_start'] . "'";
						$return['pageHeadingItems'][] = "<span>Date:</span> " . $reportParams['date_start'];
					}
				} elseif( isset( $reportParams['date_type'] ) && $reportParams['date_type'] != 'hard_set' ) {
					$queryMaxDate = "SELECT max(date) as 'max' FROM `".MySQL::DB_TABLE_SEARCH_ANALYTICS."` WHERE 1";
					if( $result = $GLOBALS['db']->query($queryMaxDate) ) {
						$maxDate = $result->fetch_row();
						$dateEnd = $maxDate[0];
						$dateStartOffset = preg_replace("/[^0-9,.]/", "", $reportParams['date_type'] );

						$dateStart = date('Y-m-d', strtotime('-'.($dateStartOffset-1).' days', strtotime( $dateEnd ) ) );
						$return['whereClauseItemsTable'][] = "date >= '" . $dateStart . "' AND date <= '" . $dateEnd . "'";
						$return['pageHeadingItems'][] = "<span>Dates:</span> Past " . $dateStartOffset . " days (" . $dateStart . " to " . $dateEnd . ")";
					}
				}
				$return['whereClauseTable'] = " WHERE " . implode( " AND ", $return['whereClauseItemsTable'] ) . " ";

				if( isset( $reportParams['sortDir'] ) ) {
					$return['sortDir'] = $reportParams['sortDir'];
				} else {
					$return['sortDir'] = 'asc';
				}
				if( isset( $reportParams['sortBy'] ) ) {
					$return['sortBy'] = $reportParams['sortBy'];
				} else {
					$return['sortBy'] = 'date';
				}

				if( isset( $reportParams['groupBy'] ) ) {
					if( $reportParams['groupBy'] == "date" ) {
						if( isset( $reportParams['granularity'] ) && $reportParams['granularity'] != 'day' ) {
							$return['granularity'] = $reportParams['granularity'];
							$return['groupByAlias'] = 'date';
							if( $reportParams['granularity'] == 'month' ) {
								$return['groupBy'] = 'DATE_FORMAT(date, "%Y-%m")';
							} elseif( $reportParams['granularity'] == 'week' ) {
								$return['groupBy'] = 'DATE_FORMAT(DATE_ADD(date, INTERVAL(1-DAYOFWEEK(date)) DAY),"%Y-%m-%d")';
							} else {
								$return['groupBy'] = strtoupper( $reportParams['granularity'] ) . '(date)';
							}
							$return['pageHeadingItems'][] = "<span>Granularity:</span> " . ucfirst( $reportParams['granularity'] );
						} else {
							$return['groupBy'] = $return['groupByAlias'] = 'date';
						}
					} elseif( $reportParams['groupBy'] == "query" ) {
						$return['groupBy'] = $return['groupByAlias'] = "query";
					} elseif( $reportParams['groupBy'] == "page" ) {
						$return['groupBy'] = $return['groupByAlias'] = "page";
					}
				}
			}
			return $return;
		}


	}
?>