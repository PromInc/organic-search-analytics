<?php $checkedTrue = ' checked'; $checkedFalse = ''; ?>
<?php $selectedTrue = ' selected="selected"'; $selectedFalse = ''; ?>
<?php $hideContent = ' style="display:none;"'; ?>

<form id="report-custom" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="get">
	<div class="report-parameter-group">
		<label class="groupLabel">Domain:</label><br>
		<?php
		$sitesList = $dataCapture->getSitesGoogleSearchConsole();

		if( is_array( $sitesList ) && count( $sitesList ) > 0 && isset( $sitesList[0] ) ) {
			foreach( $sitesList as $key => $site ) {
				echo '<input type="radio" name="domain" id="'.$site['url'].'" value="'.$site['url'].'" '.( !isset( $reportParams['domain'] ) && $key == 0 || isset( $reportParams['domain'] ) && $reportParams['domain'] == $site['url'] ? $checkedTrue : $checkedFalse ).'><label for="'.$site['url'].'">'.$site['url'].'</label><br>';
			}
		} else {
			echo 'No domains found';
		}
		?>
	</div>

	<div class="report-parameter-group">
		<label for="query" class="groupLabel">Query: </label><input type="text" name="query" id="query" value="<?php echo ( isset( $reportParams['query'] ) ? $reportParams['query'] : '' ) ?>">
	</div>

	<div class="report-parameter-group">
		<span class="groupLabel">Query Match Type:</span>
		<span><input type="radio" name="queryMatch" id="queryMatchBroad" value="broad"<?php echo ( !isset( $reportParams['queryMatch'] ) || isset( $reportParams['queryMatch'] ) && $reportParams['queryMatch'] == 'broad' ? $checkedTrue : $checkedFalse ) ?>><label for="queryMatchBroad">Broad</label></span>
		<span><input type="radio" name="queryMatch" id="queryMatchExact" value="exact"<?php echo ( isset( $reportParams['queryMatch'] ) && $reportParams['queryMatch'] == 'exact' ? $checkedTrue : $checkedFalse ) ?>><label for="queryMatchExact">Exact</label></span>
	</div>

	<div class="report-parameter-group">
		<label for="page" class="groupLabel">Page: </label><input type="text" name="page" id="page" value="<?php echo ( isset( $reportParams['page'] ) ? $reportParams['page'] : '' ) ?>">
	</div>

	<div class="report-parameter-group">
		<span class="groupLabel">Page Match Type:</span>
		<span><input type="radio" name="pageMatch" id="pageMatchBroad" value="broad"<?php echo ( !isset( $reportParams['pageMatch'] ) || isset( $reportParams['pageMatch'] ) && $reportParams['pageMatch'] == 'broad' ? $checkedTrue : $checkedFalse ) ?>><label for="pageMatchBroad">Broad</label></span>
		<span><input type="radio" name="pageMatch" id="pageMatchExact" value="exact"<?php echo ( isset( $reportParams['pageMatch'] ) && $reportParams['pageMatch'] == 'exact' ? $checkedTrue : $checkedFalse ) ?>><label for="pageMatchExact">Exact</label></span>
	</div>

	<?php
	$now = time();
	$queryDateRange = "SELECT max(date) as 'max', min(date) as 'min' FROM `".$mysql::DB_TABLE_SEARCH_ANALYTICS."` WHERE 1";
	if( $result = $GLOBALS['db']->query($queryDateRange) ) {
		$row = $result->fetch_assoc();
		$diff = strtotime( $row["max"] ) - strtotime( $row["min"] );
		$numDays = floor( $diff / (60*60*24) );
		$row["max"] - $row["min"];
		$startOffset = $now - strtotime( $row["max"] );
		$startOffset = floor( $startOffset / (60*60*24) );
		$numDays = $numDays + $startOffset + 2;
	}
	?>

	<div class="report-parameter-group">
		<label for="search_type" class="groupLabel">Search Type: </label>
		<select name="search_type" id="search_type">
			<option value="ALL"<?php echo ( !isset( $reportParams['search_type'] ) || isset( $reportParams['search_type'] ) && $reportParams['search_type'] == 'ALL' ? $selectedTrue : $selectedFalse ) ?>>ALL</option>
			<option value="web"<?php echo ( isset( $reportParams['search_type'] ) && $reportParams['search_type'] == 'web' ? $selectedTrue : $selectedFalse ) ?>>WEB</option>
			<option value="image"<?php echo ( isset( $reportParams['search_type'] ) && $reportParams['search_type'] == 'image' ? $selectedTrue : $selectedFalse ) ?>>IMAGE</option>
			<option value="video"<?php echo ( isset( $reportParams['search_type'] ) && $reportParams['search_type'] == 'video' ? $selectedTrue : $selectedFalse ) ?>>VIDEO</option>
		</select>
	</div>

	<div class="report-parameter-group">
		<label for="device_type" class="groupLabel">Device Type: </label>
		<select name="device_type" id="device_type">
			<option value="ALL"<?php echo ( !isset( $reportParams['device_type'] ) || isset( $reportParams['device_type'] ) && $reportParams['device_type'] == 'ALL' ? $selectedTrue : $selectedFalse ) ?>>ALL</option>
			<option value="desktop"<?php echo ( isset( $reportParams['device_type'] ) && $reportParams['device_type'] == 'desktop' ? $selectedTrue : $selectedFalse ) ?>>Desktop</option>
			<option value="mobile"<?php echo ( isset( $reportParams['device_type'] ) && $reportParams['device_type'] == 'mobile' ? $selectedTrue : $selectedFalse ) ?>>MOBILE</option>
			<option value="tablet"<?php echo ( isset( $reportParams['device_type'] ) && $reportParams['device_type'] == 'tablet' ? $selectedTrue : $selectedFalse ) ?>>Tablet</option>
		</select>
	</div>

	<div class="report-parameter-group">
		<label for="country" class="groupLabel">Country: </label>
		<select name="country" id="country">
			<option value="ALL"<?php echo ( !isset( $reportParams['country'] ) || isset( $reportParams['country'] ) && $reportParams['country'] == 'ALL' ? $selectedTrue : $selectedFalse ) ?>>ALL</option>
			<?php
				$countriesWhereClauses = array(); $countriesWhereClause = "";

				if( isset( $reportParams['domain'] ) ) {
					$countriesWhereClauses[] = 'domain = "' . addslashes( $reportParams['domain'] ) . '"';
				}

				if( isset( $reportParams['query'] ) ) {
					$countriesWhereClauses[] = 'query LIKE "%' . addslashes( $reportParams['query'] ) . '%"';
				}

				if( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'hard_set' ) {
					$countriesWhereClauses[] = 'date >= "' . $reportParams['date_start'] . '" AND date <= "' . $reportParams['date_end'] . '"';
				} elseif( isset( $reportParams['date_type'] ) ) {
					$maxDate = $result->fetch_row();
					$dateEnd = $row['max'];
					$dateStartOffset = preg_replace("/[^0-9,.]/", "", $reportParams['date_type'] );
					$dateStart = date('Y-m-d', strtotime('-'.($dateStartOffset-1).' days', strtotime( $row['max'] ) ) );
					$countriesWhereClauses[] = 'date >= "' . $dateStart . '" AND date <= "' . $row['max'] . '"';
				}

				if( isset( $reportParams['search_type'] ) && strtolower( $reportParams['search_type'] ) != "all" ) {
					$countriesWhereClauses[] = 'search_type = "' . addslashes( $reportParams['search_type'] ) . '"';
				}

				if( isset( $reportParams['device_type'] ) && strtolower( $reportParams['device_type'] ) != "all" ) {
					$countriesWhereClauses[] = 'device_type = "' . addslashes( $reportParams['device_type'] ) . '"';
				}

				if( count( $countriesWhereClauses ) > 0 ) {
					$countriesWhereClause = ' WHERE ' . implode( ' AND ', $countriesWhereClauses );
				}

				$queryCountries = "SELECT DISTINCT(country) as countries FROM `".$mysql::DB_TABLE_SEARCH_ANALYTICS."`".$countriesWhereClause." ORDER BY countries ASC";
			?>
			<?php if( $resultCountries = $GLOBALS['db']->query($queryCountries) ) { ?>
				<?php while ( $rowsCountries = $resultCountries->fetch_assoc() ) { ?>
					<?php if( ! is_null( $rowsCountries['countries'] ) ) { ?>
						<option value="<?php echo $rowsCountries['countries'] ?>"<?php echo ( isset( $reportParams['country'] ) && $reportParams['country'] == $rowsCountries['countries'] ? $selectedTrue : $selectedFalse ) ?>><?php echo strtoupper( $rowsCountries['countries'] ) ?></option>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</select>
	</div>

	<div id="paramGroup_dateType" class="report-parameter-group">
		<span class="groupLabel">Date Range:</span>
		<?php $tooltip = date( "D M jS, Y", strtotime( $row["max"] . ' -6 days' ) ) . ' to ' . date( "D M jS, Y", strtotime( $row["max"] ) ) ?>
		<span>
			<input type="radio" name="date_type" id="date_type_recent_7" value="recent_7"<?php echo ( !isset( $reportParams['date_type'] ) || isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'recent_7' ? $checkedTrue : $checkedFalse ) ?>>
			<label for="date_type_recent_7" tooltip="<?php echo $tooltip ?>">Past 7 Days</label>
		</span>
		<?php $tooltip = date( "D M jS, Y", strtotime( $row["max"] . ' -29 days' ) ) . ' to ' . date( "D M jS, Y", strtotime( $row["max"] ) ) ?>
		<span>
			<input type="radio" name="date_type" id="date_type_recent_30" value="recent_30"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'recent_30' ? $checkedTrue : $checkedFalse ) ?>>
			<label for="date_type_recent_30" tooltip="<?php echo $tooltip ?>">Past 30 Days</label>
		</span>
		<?php $tooltip = date( "D M jS, Y", strtotime( $row["max"] . ' -89 days' ) ) . ' to ' . date( "D M jS, Y", strtotime( $row["max"] ) ) ?>
		<span>
			<input type="radio" name="date_type" id="date_type_recent_90" value="recent_90"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'recent_90' ? $checkedTrue : $checkedFalse ) ?>>
			<label for="date_type_recent_90" tooltip="<?php echo $tooltip ?>">Past 90 Days</label>
		</span>
		<?php $tooltip = "Select to set a date range" ?>
		<span>
			<input type="radio" name="date_type" id="date_type_hard_set" value="hard_set"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'hard_set' ? $checkedTrue : $checkedFalse ) ?>>
			<?php
			$date_range_display = "";
			if( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'hard_set' ) {
				$num_days = $core->getNumDays( $reportParams['date_start'], $reportParams['date_end'] );
				$date_range_display = " (" . $num_days . " day" . ( $num_days > 1 ? "s" : "" ) . ")";
			}
			?>
			<label for="date_type_hard_set" tooltip="<?php echo $tooltip ?>">Specific Dates<span id="date_range_count"><?php echo $date_range_display ?></span></label>
		</span>
	</div>

	<div id="paramGroup_dateStart" class="floatL report-parameter-group"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'hard_set' ? $selectedFalse : $hideContent ) ?>>
		<?php
		/* Get default date */
		$defaultDate = date( "Y-m-d", strtotime( $row["max"] . ' -6 days' ) );
		if( $defaultDate < $row["min"] ) { $defaultDate = $row["min"]; }
		?>
		<label for="date_start" class="groupLabel">Date Start: </label>
		<div class="mT1p">
			<input type="text" name="date_start" id="date_start" style="width: 100%;">
			<div id="date_start_inline"></div>
		</div>
	</div>

	<div id="paramGroup_dateEnd" class="floatL report-parameter-group"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'hard_set' ? $selectedFalse : $hideContent ) ?>>
		<label for="date_end" class="groupLabel">Date End: </label>
		<div class="mT1p">
			<input type="text" name="date_end" id="date_end" style="width: 100%;">
			<div id="date_end_inline"></div>
		</div>
	</div>

	<?php
	if( isset( $reportParams['date_start'] ) && $reportParams['date_start'] > $row["min"] && $reportParams['date_start'] < $row["max"] ) {
		$datePicker_start = $reportParams['date_start'];
	} else {
		$datePicker_start = $defaultDate;
	}

	if( isset( $reportParams['date_end'] ) && $reportParams['date_end'] > $row["min"] && $reportParams['date_end'] < $row["max"] ) {
		$datePicker_end = $reportParams['date_end'];
	}
	?>

	<script>
		$(function() {
			/* Date Picker - Start */
			$( "#date_start_inline" ).datepicker({
				changeMonth: true,
				changeYear: true,
				altField: "#date_start",
				defaultDate: "<?php echo $datePicker_start ?>",
				dateFormat: "yy-mm-dd",
				minDate: "<?php echo $row["min"] ?>",
				maxDate: "<?php echo $row["max"] ?>",
				onSelect: function( selectedDate ) {
					updateDateRange();
				}
			});
			/* Date Picker - End */
			$( "#date_end_inline" ).datepicker({
				changeMonth: true,
				changeYear: true,
				altField: "#date_end",
				<?php if( isset( $datePicker_end ) ) { ?>
				defaultDate: "<?php echo $datePicker_end ?> ",
				<?php } ?>
				dateFormat: "yy-mm-dd",
				minDate: "<?php echo $row["min"] ?>",
				maxDate: "<?php echo $row["max"] ?>",
				onSelect: function( selectedDate ) {
					updateStartDate( '#date_start_inline', selectedDate );
					updateDateRange();
				}
			});
		});
	</script>
	<div class="clear"></div>

	<div id="paramGroup_groupBy" class="report-parameter-group">
		<span class="groupLabel">Group By:</span>
		<span><input type="radio" name="groupBy" id="groupByDate" value="date"<?php echo ( !isset( $reportParams['groupBy'] ) || isset( $reportParams['groupBy'] ) && $reportParams['groupBy'] == 'date' ? $checkedTrue : $checkedFalse ) ?>><label for="groupByDate">Date</label></span>
		<span><input type="radio" name="groupBy" id="groupByQuery" value="query"<?php echo ( isset( $reportParams['groupBy'] ) && $reportParams['groupBy'] == 'query' ? $checkedTrue : $checkedFalse ) ?>><label for="groupByQuery">Query</label></span>
		<span><input type="radio" name="groupBy" id="groupByPage" value="page"<?php echo ( isset( $reportParams['groupBy'] ) && $reportParams['groupBy'] == 'page' ? $checkedTrue : $checkedFalse ) ?>><label for="groupByPage">Page</label></span>
	</div>

	<div id="paramGroup_granularity" class="report-parameter-group"<?php echo ( isset( $reportParams['groupBy'] ) && $reportParams['groupBy'] != 'date' ? $hideContent : $selectedFalse ) ?>>
		<span class="groupLabel">Granularity:</span>
		<span><input type="radio" name="granularity" id="granularityDay" value="day"<?php echo ( !isset( $reportParams['granularity'] ) || isset( $reportParams['granularity'] ) && $reportParams['granularity'] == 'day' ? $checkedTrue : $checkedFalse ) ?>><label for="granularityDay">Day</label></span>
		<span><input type="radio" name="granularity" id="granularityWeek" value="week"<?php echo ( isset( $reportParams['granularity'] ) && $reportParams['granularity'] == 'week' ? $checkedTrue : $checkedFalse ) ?>><label for="granularityWeek">Week</label></span>
		<span><input type="radio" name="granularity" id="granularityMonth" value="month"<?php echo ( isset( $reportParams['granularity'] ) && $reportParams['granularity'] == 'month' ? $checkedTrue : $checkedFalse ) ?>><label for="granularityMonth">Month</label></span>
		<span><input type="radio" name="granularity" id="granularityYear" value="year"<?php echo ( isset( $reportParams['granularity'] ) && $reportParams['granularity'] == 'year' ? $checkedTrue : $checkedFalse ) ?>><label for="granularityYear">Year</label></span>
	</div>

	<div id="paramGroup_sortBy" class="report-parameter-group">
		<span class="groupLabel">Sort By:</span>
		<?php $displayCheck = true;  if( isset( $reportParams['groupBy'] ) && in_array( strtolower( $reportParams['groupBy'] ), array('query','page') ) ) { $displayCheck = false; } ?>
		<span class="sortByOption"<?php if( ! $displayCheck ) { echo ' style="display:none;"'; } ?>><input type="radio" name="sortBy" id="sortByDate" value="date"<?php echo ( !isset( $reportParams['sortBy'] ) || isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'date' ? $checkedTrue : $checkedFalse ) ?><?php if( ! $displayCheck ) { echo ' disabled'; } ?>><label for="sortByDate">Date</label></span>

		<?php $displayCheck = true;  if( ! $reportParams || isset( $reportParams['groupBy'] ) && in_array( strtolower( $reportParams['groupBy'] ), array('date','page') ) ) { $displayCheck = false; } ?>
		<span class="sortByOption"<?php if( ! $displayCheck ) { echo ' style="display:none;"'; } ?>><input type="radio" name="sortBy" id="sortByQuery" value="query"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'query' ? $checkedTrue : $checkedFalse ) ?><?php if( ! $displayCheck ) { echo ' disabled'; } ?>><label for="sortByQuery">Query</label></span>

		<?php $displayCheck = true;  if( ! $reportParams || isset( $reportParams['groupBy'] ) && in_array( strtolower( $reportParams['groupBy'] ), array('date','query') ) ) { $displayCheck = false; } ?>
		<span class="sortByOption"<?php if( ! $displayCheck ) { echo ' style="display:none;"'; } ?>><input type="radio" name="sortBy" id="sortByPage" value="page"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'page' ? $checkedTrue : $checkedFalse ) ?><?php if( ! $displayCheck ) { echo ' disabled'; } ?>><label for="sortByQuery">Page</label></span>

		<?php $displayCheck = true;  if( ! $reportParams || isset( $reportParams['groupBy'] ) && in_array( strtolower( $reportParams['groupBy'] ), array('query') ) ) { $displayCheck = false; } ?>		
		<span class="sortByOption"<?php if( ! $displayCheck ) { echo ' style="display:none;"'; } ?>><input type="radio" name="sortBy" id="sortByQueries" value="queries"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'queries' ? $checkedTrue : $checkedFalse ) ?><?php if( ! $displayCheck ) { echo ' disabled'; } ?>><label for="sortByQueries"><?php echo $colHeadingSecondary ?></label></span>

		<?php $displayCheck = true;  if( ! $reportParams || isset( $reportParams['groupBy'] ) && in_array( strtolower( $reportParams['groupBy'] ), array('page') ) ) { $displayCheck = false; } ?>
		<span class="sortByOption"<?php if( ! $displayCheck ) { echo ' style="display:none;"'; } ?>><input type="radio" name="sortBy" id="sortByPages" value="pages"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'pages' ? $checkedTrue : $checkedFalse ) ?><?php if( ! $displayCheck ) { echo ' disabled'; } ?>><label for="sortByPages">Pages</label></span>

		<span class="sortByOption"><input type="radio" name="sortBy" id="sortByImpressions" value="impressions"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'impressions' ? $checkedTrue : $checkedFalse ) ?>><label for="sortByImpressions">Impressions</label></span>
		<span class="sortByOption"><input type="radio" name="sortBy" id="sortByClicks" value="clicks"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'clicks' ? $checkedTrue : $checkedFalse ) ?>><label for="sortByClicks">Clicks</label></span>
		<span class="sortByOption"><input type="radio" name="sortBy" id="sortByAvgPos" value="avg_position"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'avg_position' ? $checkedTrue : $checkedFalse ) ?>><label for="sortByAvgPos">Avg Position</label></span>
		<span class="sortByOption"><input type="radio" name="sortBy" id="sortByCtr" value="ctr"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'ctr' ? $checkedTrue : $checkedFalse ) ?>><label for="sortByCtr">Click Through Rate</label></span>
	</div>

	<div id="paramGroup_sortDir" class="report-parameter-group">
		<span class="groupLabel">Sort Direction:</span>
		<span><input type="radio" name="sortDir" id="sortDirAsc" value="asc"<?php echo ( !isset( $reportParams['sortDir'] ) || isset( $reportParams['sortDir'] ) && $reportParams['sortDir'] == 'asc' ? $checkedTrue : $checkedFalse ) ?>><label for="sortDirAsc">Ascending</label></span>
		<span><input type="radio" name="sortDir" id="sortDirDesc" value="desc"<?php echo ( isset( $reportParams['sortDir'] ) && $reportParams['sortDir'] == 'desc' ? $checkedTrue : $checkedFalse ) ?>><label for="sortDirDesc">Descending</label></span>
	</div>

	<div id="paramGroup_submit" class="report-parameter-group">
		<input type="submit" value="Generate Report" class="button">
	</div>
</form>