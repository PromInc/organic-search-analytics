<?php ini_set('max_execution_time', 600);  //300 seconds = 5 minutes ?>

<?php $titleTag = "Reporting | Custom Report"; ?>

<?php include_once('inc/html/_head.php'); ?>

	<?php
	if( isset( $_GET['savedReport'] ) ) {
		/* Used save report parameters */
		/* Load Reporting Class */
		$reports = new Reports();
		/* Get parameters for report */
		$reportParams = $reports->getSavedReport($_GET['savedReport']);
	} else {
		/* Use query parameters */
		$reportParams = $_GET;
	}

	$useQuery = false; if( isset( $reportParams['query'] ) && $reportParams['query'] > "" ) { $useQuery = true; }
	$whereClause = $chartLabel = "";
	if( $reportParams ) {
		$whereClauseItemsTable = $whereClauseItemsChart = $pageHeadingItems = [];
		if( isset( $reportParams['domain'] ) && $reportParams['domain'] > "" ) {
			$whereClauseItemsTable[] = $whereClauseItemsChart[] = "domain = '" . $reportParams['domain'] . "'";
			$pageHeadingItems[] = "Domain: " . $reportParams['domain'];
		}
		if( isset( $reportParams['query'] ) && $reportParams['query'] > "" ) {
			switch( $reportParams['queryMatch'] ) {
				case "broad":
				default:
					$whereClauseItemsTable[] = $whereClauseItemsChart[] = "query LIKE '%" . $reportParams['query'] . "%'";
					break;
				case "exact":
					$whereClauseItemsTable[] = $whereClauseItemsChart[] = "query = '" . $reportParams['query'] . "'";
					break;
			}
			$pageHeadingItems[] = "Query: " . $reportParams['query'] . (isset($reportParams['queryMatch'])?" (".$reportParams['queryMatch'].")":"");
			$chartLabel = $reportParams['query'] . (isset($reportParams['queryMatch'])?" (".$reportParams['queryMatch'].")":"");
		}
		if( isset( $reportParams['search_type'] ) && $reportParams['search_type'] > "" ) {
			if( $reportParams['search_type'] != "ALL" ) {
				$whereClauseItemsTable[] = $whereClauseItemsChart[] = "search_type = '" . $reportParams['search_type'] . "'";
			}
			$pageHeadingItems[] = "Search Type: " . $reportParams['search_type'];
		}
		if( isset( $reportParams['device_type'] ) && $reportParams['device_type'] > "" ) {
			if( $reportParams['device_type'] != "ALL" ) {
				$whereClauseItemsTable[] = $whereClauseItemsChart[] = "device_type = '" . $reportParams['device_type'] . "'";
			}
			$pageHeadingItems[] = "Device Type: " . $reportParams['device_type'];
		}
		if( isset( $reportParams['date_start'] ) && $reportParams['date_start'] > 0 && $reportParams['date_type'] == 'hard_set' ) {
			if( isset( $reportParams['date_end'] ) && $reportParams['date_end'] > 0 ) {
				$whereClauseItemsTable[] = "date >= '" . $reportParams['date_start'] . "' AND date <= '" . $reportParams['date_end'] . "'";
				$whereClauseItemsChart[] = "date >= '" . $reportParams['date_start'] . "' AND date <= '" . $reportParams['date_end'] . "'";
				$pageHeadingItems[] = "Dates: " . $reportParams['date_start'] . " to " . $reportParams['date_end'];
			} else {
				$whereClauseItemsTable[] = "date = '" . $reportParams['date_start'] . "'";
				$whereClauseItemsChart[] = "date = '" . $reportParams['date_start'] . "'";
				$pageHeadingItems[] = "Date: " . $reportParams['date_start'];
			}
		} elseif( $reportParams['date_type'] != 'hard_set' ) {
			$queryMaxDate = "SELECT max(date) as 'max' FROM `".$mysql::DB_TABLE_SEARCH_ANALYTICS."` WHERE 1";
			if( $result = $GLOBALS['db']->query($queryMaxDate) ) {
				$maxDate = $result->fetch_row();
				$dateEnd = $maxDate[0];
				$dateStartOffset = preg_replace("/[^0-9,.]/", "", $reportParams['date_type'] );
				$dateStart = date('Y-m-d', strtotime('-'.$dateStartOffset.' days', strtotime( $dateEnd ) ) );
				$whereClauseItemsTable[] = "date >= '" . $dateStart . "' AND date <= '" . $dateEnd . "'";
				$pageHeadingItems[] = "Dates: Past " . $dateStartOffset . " days (" . $dateStart . " to " . $dateEnd . ")";
			}
		}
		$whereClauseTable = " WHERE " . implode( " AND ", $whereClauseItemsTable ) . " ";
		$whereClauseChart = " WHERE " . implode( " AND ", $whereClauseItemsChart ) . " ";

		if( isset( $reportParams['sortDir'] ) ) { $sortDir = $reportParams['sortDir']; } else { $sortDir = 'asc'; }
		if( isset( $reportParams['sortBy'] ) ) { $sortBy = $reportParams['sortBy']; } else { $sortBy = 'date'; }
		//if( isset( $reportParams['groupBy'] ) ) { $groupBy = $reportParams['groupBy']; } else { $groupBy = 'date'; }

		$groupByDate = 'date';
		if( isset( $reportParams['granularity'] ) && $reportParams['granularity'] != 'day' ) {
			$groupBy = strtoupper( $reportParams['granularity'] ) . '(' . $groupByDate . ')';
			$pageHeadingItems[] = "Granularity: " . $reportParams['granularity'];
		} else {
			$groupBy = $groupByDate;
		}

	}
	?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Search Analytics Reporting | Results</h1>
	<h2><?php echo implode( ", ", $pageHeadingItems ); ?></h2>

	<?php
	$reports = new Reports(); //Load Reporting Class

	/* Get saved report categories */
	$reportCategories = '<select name="reportCatExisting">';
	foreach( $reports->getSavedReportCategories() as $key => $category ) {
		$reportCategories .= '<option value="' . $key . '">'. $category['name'] . '</option>';
	}
	$reportCategories .= '</select>';

	/* Get save report form and insert dynamic values */
	$saveReportContent = file_get_contents( $GLOBALS['basedir'] . "/inc/html/_saveReport.php" );
	$saveReportContent = preg_replace( '/{{report_params}}/', urlencode( json_encode( $reportParams ) ), $saveReportContent );
	$saveReportContent = preg_replace( '/{{report_categories}}/', $reportCategories, $saveReportContent );
	?>

	<?php if( ! isset( $_GET['savedReport'] ) ) { ?>
		<?php echo $saveReportContent; ?>
	<?php } ?>

	<?php
		if( !$useQuery ) {
			$reportQueryTable = "SELECT date, count(DISTINCT query) as 'queries', avg(avg_position) as 'avg_position' FROM ".$mysql::DB_TABLE_SEARCH_ANALYTICS." " . $whereClauseTable . "GROUP BY " . $groupBy . " ORDER BY " . $sortBy . " ASC";
			$reportQueryChart = "SELECT date, sum(impressions) as 'impressions', sum(clicks) as 'clicks' FROM ".$mysql::DB_TABLE_SEARCH_ANALYTICS." " . $whereClauseChart . "GROUP BY " . $groupBy . " ORDER BY " . $sortBy . " ASC";
		} else {
			$reportQueryTable = "SELECT date, count(DISTINCT query) as 'queries', sum(impressions) as 'impressions', sum(clicks) as 'clicks', avg(avg_position) as 'avg_position' FROM ".$mysql::DB_TABLE_SEARCH_ANALYTICS." " . $whereClauseTable . "GROUP BY " . $groupBy . " ORDER BY " . $sortBy . " ASC";
		}
	?>

	<?php
		/* Get MySQL Results */
		$outputTable = $outputChart = array();
		if( $resultTable = $GLOBALS['db']->query($reportQueryTable) ) {
			while ( $rowsTable = $resultTable->fetch_assoc() ) {
				$outputTable[] = $rowsTable;
			}
		}
		/* Get MySQL Results */
		if( !$useQuery ) {
			if( $resultChart = $GLOBALS['db']->query($reportQueryChart) ) {
				while ( $rowsChart = $resultChart->fetch_assoc() ) {
					$outputChart[] = $rowsChart;
				}
			}
		}

		/* If Results */
		if( $useQuery && count($outputTable) > 0 || count($outputTable) === count($outputChart) && count($outputTable) > 0 ) {
			/* Merge MySQL Results */
			$rows = array();
			if( !$useQuery ) {
				for( $r=0; $r < count($outputTable); $r++ ) {
					$rows[ $outputTable[$r]["date"] ] = array( "queries" => $outputTable[$r]["queries"], "impressions" => $outputChart[$r]["impressions"], "clicks" => $outputChart[$r]["clicks"], "avg_position" => $outputTable[$r]["avg_position"] );
				}
			} else {
				for( $r=0; $r < count($outputTable); $r++ ) {
					$rows[ $outputTable[$r]["date"] ] = array( "queries" => $outputTable[$r]["queries"], "impressions" => $outputTable[$r]["impressions"], "clicks" => $outputTable[$r]["clicks"], "avg_position" => $outputTable[$r]["avg_position"] );
				}
			}

			$jqData = array( "date" => array(), "impressions" => array(), "clicks" => array(), "ctr" => array(), "avg_position" => array() );

			foreach ( $rows as $index => $values ) {
				$jqData['date'][] = $index;
				$jqData['impressions'][] = $values["impressions"];
				$jqData['clicks'][] = $values["clicks"];
				$jqData['ctr'][] = ( $values["clicks"] / $values["impressions"] ) * 100;
				$jqData['avg_position'][] = $values["avg_position"];
			}

			$num = count( $jqData['date'] );
			$posString = "";
			$posMax = 0;
			for( $c=0; $c<$num; $c++ ) {
				if( $c != 0 ) { $posString .= ","; }
				$posString .= "['".$jqData['date'][$c]."',".$jqData['avg_position'][$c]."]";
				if( $jqData['avg_position'][$c] > $posMax ) { $posMax = $jqData['avg_position'][$c]; }
			}
			?>

			<div id="reportchart"></div>
			<div class="button" id="zoomReset">Reset Zoom</div>
			<div class="clear"></div>

			<script type="text/javascript">
			$(document).ready(function(){
				var line1=[<?php echo $posString ?>];
				var plot2 = $.jqplot('reportchart', [line1], {
						title:'Average Position<?php echo (strlen($chartLabel)>0?" | ".$chartLabel."":"") ?>',
						axes:{
							xaxis:{
								renderer:$.jqplot.DateAxisRenderer, 
								tickRenderer: $.jqplot.CanvasAxisTickRenderer,
								tickOptions:{
									formatString:'%m-%d-%y',
									angle: -30
								},
							},
							yaxis:{
								max: 1,
								min: <?php echo $posMax ?>,
								tickOptions:{
									formatString:'%i'
								},
								label:'SERP Position',
								labelRenderer: $.jqplot.CanvasAxisLabelRenderer
							}
						},
						highlighter: {
							show: true,
							tooltipAxes: 'xy',
							useAxesFormatters: true,
							showTooltip: true
						},
						series:[{lineWidth:4, markerOptions:{style:'square'}}],
						cursor:{
							show: true,
							zoom: true,
						}
				});
				$('#zoomReset').click(function() { plot2.resetZoom() });
			});
			</script>


			<?php if( $sortDir == 'desc' ) { $rows = array_reverse( $rows ); } ?>

			<table class="sidebysidetable">
				<tr>
					<td>Date</td>
				</tr>
				<?php
				foreach ( $rows as $index => $values ) {
					echo '<tr><td>' . $index . '</td></tr>';
				}
				?>
			</table>

			<table class="sidebysidetable">
				<tr>
					<td>Queries</td><td>Impressions</td><td>Clicks</td><td>Avg Position</td>
				</tr>
				<?php
				foreach ( $rows as $index => $values ) {
					echo '<tr><td>' . number_format( $values["queries"] ) . '</td><td>' . number_format( $values["impressions"] ) . '</td><td>' . number_format( $values["clicks"] ) . '</td><td>' . number_format( $values["avg_position"], 2 ) . '</td></tr>';
				}
				?>
			</table>

			<table class="sidebysidetable">
				<tr>
					<td>CTR</td>
				</tr>
				<?php
				foreach ( $rows as $index => $values ) {
					echo '<tr><td>' . number_format( ( $values["clicks"] / $values["impressions"] ) * 100, 2 ) . '%</td></tr>';
				}
				?>
			</table>
		<?php
		}
	?>
	<div class="clear"></div>

<?php include_once('inc/html/_foot.php'); ?>