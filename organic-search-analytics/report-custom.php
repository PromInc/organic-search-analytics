<?php $titleTag = "Reporting | Custom Report"; ?>

<?php include_once('inc/html/_head.php'); ?>


	<?php
	$useQuery = false; if( isset( $_GET['query'] ) && $_GET['query'] > "" ) { $useQuery = true; }
	$whereClause = "";
	if( $_GET ) {
		$whereClauseItemsTable = $whereClauseItemsChart = $pageHeadingItems = [];
		if( isset( $_GET['domain'] ) && $_GET['domain'] > "" ) {
			$whereClauseItemsTable[] = $whereClauseItemsChart[] = "domain = '" . $_GET['domain'] . "'";
			$pageHeadingItems[] = "Domain: " . $_GET['domain'];
		}
		if( isset( $_GET['query'] ) && $_GET['query'] > "" ) {
			$whereClauseItemsTable[] = $whereClauseItemsChart[] = "query LIKE '%" . $_GET['query'] . "%'";
			$pageHeadingItems[] = "Query: " . $_GET['query'];
		}
		if( isset( $_GET['search_type'] ) && $_GET['search_type'] > "" ) {
			if( $_GET['search_type'] != "ALL" ) {
				$whereClauseItemsTable[] = $whereClauseItemsChart[] = "search_type = '" . $_GET['search_type'] . "'";
			}
			$pageHeadingItems[] = "Search Type: " . $_GET['search_type'];
		}
		if( isset( $_GET['device_type'] ) && $_GET['device_type'] > "" ) {
			if( $_GET['device_type'] != "ALL" ) {
				$whereClauseItemsTable[] = $whereClauseItemsChart[] = "device_type = '" . $_GET['device_type'] . "'";
			}
			$pageHeadingItems[] = "Device Type: " . $_GET['device_type'];
		}
		if( isset( $_GET['date_start'] ) && $_GET['date_start'] > 0 ) {
			if( isset( $_GET['date_end'] ) && $_GET['date_end'] > 0 ) {
				$whereClauseItemsTable[] = "date >= '" . $_GET['date_start'] . "' AND date <= '" . $_GET['date_end'] . "'";
				$whereClauseItemsChart[] = "date >= '" . $_GET['date_start'] . "' AND date <= '" . $_GET['date_end'] . "'";
				$pageHeadingItems[] = "Dates: " . $_GET['date_start'] . " to " . $_GET['date_end'];
			} else {
				$whereClauseItemsTable[] = "date = '" . $_GET['date_start'] . "'";
				$whereClauseItemsChart[] = "date = '" . $_GET['date_start'] . "'";
				$pageHeadingItems[] = "Date: " . $_GET['date_start'];
			}
		}
		$whereClauseTable = " WHERE " . implode( " AND ", $whereClauseItemsTable ) . " ";
		$whereClauseChart = " WHERE " . implode( " AND ", $whereClauseItemsChart ) . " ";
	}
	?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Search Analytics Reporting | Results</h1>
	<h2><?php echo implode( ", ", $pageHeadingItems ); ?></h2>

	<?php
		if( !$useQuery ) {
			$reportQueryTable = "SELECT date, count(DISTINCT query) as 'queries', avg(avg_position) as 'avg_position' FROM ".$mysql::DB_TABLE_SEARCH_ANALYTICS." " . $whereClauseTable . "GROUP BY date ORDER BY date ASC";
			$reportQueryChart = "SELECT date, sum(impressions) as 'impressions', sum(clicks) as 'clicks' FROM ".$mysql::DB_TABLE_SEARCH_ANALYTICS." " . $whereClauseChart . "GROUP BY date ORDER BY date ASC";
		} else {
			$reportQueryTable = "SELECT date, count(DISTINCT query) as 'queries', sum(impressions) as 'impressions', sum(clicks) as 'clicks', avg(avg_position) as 'avg_position' FROM ".$mysql::DB_TABLE_SEARCH_ANALYTICS." " . $whereClauseTable . "GROUP BY date ORDER BY date ASC";
		}

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
			?>
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