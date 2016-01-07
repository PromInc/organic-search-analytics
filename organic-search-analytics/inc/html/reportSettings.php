<?php $checkedTrue = ' checked'; $checkedFalse = ''; ?>
<?php $selectedTrue = ' selected="selected"'; $selectedFalse = ''; ?>
<?php $hideContent = ' style="display:none;"'; ?>

<form id="report-custom" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="get">
	<p>
		<label>Domain: </label><br>
		<?php
		$sitesList = $dataCapture->getSitesGoogleSearchConsole();
		foreach( $sitesList as $key => $site ) {
			echo '<input type="radio" name="domain" id="'.$site['url'].'" value="'.$site['url'].'" '.( !isset( $reportParams['domain'] ) && $key == 0 || isset( $reportParams['domain'] ) && $reportParams['domain'] == $site['url'] ? $checkedTrue : $checkedFalse ).'><label for="'.$site['url'].'">'.$site['url'].'</label><br>';
		}
		?>
	</p>
	<div class="report-parameter-group">
		<label for="query">Query: </label><input type="text" name="query" id="query" value="<?php echo ( isset( $reportParams['query'] ) ? $reportParams['query'] : '' ) ?>">
	</div>

	<div class="report-parameter-group">
		Query Match Type:
		<span><input type="radio" name="queryMatch" id="queryMatchBroad" value="broad"<?php echo ( !isset( $reportParams['queryMatch'] ) || isset( $reportParams['queryMatch'] ) && $reportParams['queryMatch'] == 'broad' ? $checkedTrue : $checkedFalse ) ?>><label for="queryMatchBroad">Broad</label></span>
		<span><input type="radio" name="queryMatch" id="queryMatchExact" value="exact"<?php echo ( isset( $reportParams['queryMatch'] ) && $reportParams['queryMatch'] == 'exact' ? $checkedTrue : $checkedFalse ) ?>><label for="queryMatchExact">Exact</label></span>
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
		<label for="search_type">Search Type: </label>
		<select name="search_type" id="search_type">
			<option value="ALL"<?php echo ( !isset( $reportParams['search_type'] ) || isset( $reportParams['search_type'] ) && $reportParams['search_type'] == 'ALL' ? $selectedTrue : $selectedFalse ) ?>>ALL</option>
			<option value="web"<?php echo ( isset( $reportParams['search_type'] ) && $reportParams['search_type'] == 'web' ? $selectedTrue : $selectedFalse ) ?>>WEB</option>
			<option value="image"<?php echo ( isset( $reportParams['search_type'] ) && $reportParams['search_type'] == 'image' ? $selectedTrue : $selectedFalse ) ?>>IMAGE</option>
			<option value="video"<?php echo ( isset( $reportParams['search_type'] ) && $reportParams['search_type'] == 'video' ? $selectedTrue : $selectedFalse ) ?>>VIDEO</option>
		</select>
	</div>

	<div class="report-parameter-group">
		<label for="device_type">Device Type: </label>
		<select name="device_type" id="device_type">
			<option value="ALL"<?php echo ( !isset( $reportParams['device_type'] ) || isset( $reportParams['device_type'] ) && $reportParams['device_type'] == 'ALL' ? $selectedTrue : $selectedFalse ) ?>>ALL</option>
			<option value="desktop"<?php echo ( isset( $reportParams['device_type'] ) && $reportParams['device_type'] == 'desktop' ? $selectedTrue : $selectedFalse ) ?>>Desktop</option>
			<option value="mobile"<?php echo ( isset( $reportParams['device_type'] ) && $reportParams['device_type'] == 'mobile' ? $selectedTrue : $selectedFalse ) ?>>MOBILE</option>
			<option value="tablet"<?php echo ( isset( $reportParams['device_type'] ) && $reportParams['device_type'] == 'tablet' ? $selectedTrue : $selectedFalse ) ?>>Tablet</option>
		</select>
	</div>

	<div id="paramGroup_dateType" class="report-parameter-group">
		Date Range:
		<span><input type="radio" name="date_type" id="date_type_recent_7" value="recent_7"<?php echo ( !isset( $reportParams['date_type'] ) || isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'recent_7' ? $checkedTrue : $checkedFalse ) ?>><label for="date_type_recent_7">Past 7 Days</label></span>
		<span><input type="radio" name="date_type" id="date_type_recent_30" value="recent_30"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'recent_30' ? $checkedTrue : $checkedFalse ) ?>><label for="date_type_recent_30">Past 30 Days</label></span>
		<span><input type="radio" name="date_type" id="date_type_recent_90" value="recent_90"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'recent_90' ? $checkedTrue : $checkedFalse ) ?>><label for="date_type_recent_90">Past 90 Days</label></span>
		<span><input type="radio" name="date_type" id="date_type_hard_set" value="hard_set"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'hard_set' ? $checkedTrue : $checkedFalse ) ?>><label for="date_type_hard_set">Specific Dates</label></span>
	</div>

	<div id="paramGroup_dateStart" class="report-parameter-group"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'hard_set' ? $selectedFalse : $hideContent ) ?>>
		<label for="date_start">Date Start: </label>
		<select name="date_start" id="date_start">
			<option value=""></option>
				<?php
				for( $d = $startOffset; $d < $numDays; $d++ ) {
					$dateDisplay = date( 'Y-m-d', $now - ( 86400 * $d ) );
					echo '<option value="' . $dateDisplay . '"' . ( isset( $reportParams['date_start'] ) && $reportParams['date_start'] == $dateDisplay ? $selectedTrue : $selectedFalse ) . '>' . $dateDisplay . '</option>';
				}
				?>
		</select>
	</div>

	<div id="paramGroup_dateEnd" class="report-parameter-group"<?php echo ( isset( $reportParams['date_type'] ) && $reportParams['date_type'] == 'hard_set' ? $selectedFalse : $hideContent ) ?>>
		<label for="date_end">Date End: </label>
		<select name="date_end" id="date_end">
			<option value=""></option>
			<?php
			for( $d = $startOffset; $d < $numDays; $d++ ) {
				$dateDisplay = date( 'Y-m-d', $now - ( 86400 * $d ) );
				echo '<option value="' . $dateDisplay . '"' . ( isset( $reportParams['date_end'] ) && $reportParams['date_end'] == $dateDisplay ? $selectedTrue : $selectedFalse ) . '>' . $dateDisplay . '</option>';
			}
			?>
		</select>
	</div>
<!--
	<div id="paramGroup_metricPrimary" class="report-parameter-group">
		Primary Metric:
		<span><input type="radio" name="metricPrimary" id="metricPrimaryDate" value="date" checked><label for="metricPrimaryDate">Date</label></span>
		<span><input type="radio" name="metricPrimary" id="metricPrimaryQuery" value="query"><label for="metricPrimaryQuery">Query</label></span>
	</div>
-->

	<div id="paramGroup_groupBy" class="report-parameter-group">
		Group By:
		<span><input type="radio" name="groupBy" id="groupByDate" value="date"<?php echo ( !isset( $reportParams['groupBy'] ) || isset( $reportParams['groupBy'] ) && $reportParams['groupBy'] == 'date' ? $checkedTrue : $checkedFalse ) ?>><label for="groupByDate">Date</label></span>
		<span><input type="radio" name="groupBy" id="groupByQuery" value="query"<?php echo ( isset( $reportParams['groupBy'] ) && $reportParams['groupBy'] == 'query' ? $checkedTrue : $checkedFalse ) ?>><label for="groupByQuery">Query</label></span>
	</div>

	<div id="paramGroup_granularity" class="report-parameter-group"<?php echo ( isset( $reportParams['groupBy'] ) && $reportParams['groupBy'] != 'date' ? $hideContent : $selectedFalse ) ?>>
		Granularity:
		<span><input type="radio" name="granularity" id="granularityDay" value="day"<?php echo ( !isset( $reportParams['granularity'] ) || isset( $reportParams['granularity'] ) && $reportParams['granularity'] == 'day' ? $checkedTrue : $checkedFalse ) ?>><label for="granularityDay">Day</label></span>
		<span><input type="radio" name="granularity" id="granularityWeek" value="week"<?php echo ( isset( $reportParams['granularity'] ) && $reportParams['granularity'] == 'week' ? $checkedTrue : $checkedFalse ) ?>><label for="granularityWeek">Week</label></span>
		<span><input type="radio" name="granularity" id="granularityMonth" value="month"<?php echo ( isset( $reportParams['granularity'] ) && $reportParams['granularity'] == 'month' ? $checkedTrue : $checkedFalse ) ?>><label for="granularityMonth">Month</label></span>
		<span><input type="radio" name="granularity" id="granularityYear" value="year"<?php echo ( isset( $reportParams['granularity'] ) && $reportParams['granularity'] == 'year' ? $checkedTrue : $checkedFalse ) ?>><label for="granularityYear">Year</label></span>
	</div>

	<div id="paramGroup_sortBy" class="report-parameter-group">
		Sort By:
		<?php $displayCheck = true;  if( isset( $reportParams['groupBy'] ) && strtolower( $reportParams['groupBy'] )  == 'query' ) { $displayCheck = false; } ?>
		<span<?php if( ! $displayCheck ) { echo ' style="display:none;"'; } ?>><input type="radio" name="sortBy" id="sortByDate" value="date"<?php echo ( !isset( $reportParams['sortBy'] ) || isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'date' ? $checkedTrue : $checkedFalse ) ?><?php if( ! $displayCheck ) { echo ' disabled'; } ?>><label for="sortByDate">Date</label></span>
		<?php $displayCheck = true;  if( ! $reportParams || isset( $reportParams['groupBy'] ) && strtolower( $reportParams['groupBy'] )  == 'date' ) { $displayCheck = false; } ?>
		<span<?php if( ! $displayCheck ) { echo ' style="display:none;"'; } ?>><input type="radio" name="sortBy" id="sortByQuery" value="query"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'query' ? $checkedTrue : $checkedFalse ) ?><?php if( ! $displayCheck ) { echo ' disabled'; } ?>><label for="sortByQuery">Query</label></span>
		<span><input type="radio" name="sortBy" id="sortByQueries" value="queries"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'queries' ? $checkedTrue : $checkedFalse ) ?>><label for="sortByQueries"><?php echo $colHeadingSecondary ?></label></span>
		<span><input type="radio" name="sortBy" id="sortByImpressions" value="impressions"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'impressions' ? $checkedTrue : $checkedFalse ) ?>><label for="sortByImpressions">Impressions</label></span>
		<span><input type="radio" name="sortBy" id="sortByClicks" value="clicks"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'clicks' ? $checkedTrue : $checkedFalse ) ?>><label for="sortByClicks">Clicks</label></span>
		<span><input type="radio" name="sortBy" id="sortByAvgPos" value="avg_position"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'avg_position' ? $checkedTrue : $checkedFalse ) ?>><label for="sortByAvgPos">Avg Position</label></span>
		<span><input type="radio" name="sortBy" id="sortByCtr" value="ctr"<?php echo ( isset( $reportParams['sortBy'] ) && $reportParams['sortBy'] == 'ctr' ? $checkedTrue : $checkedFalse ) ?>><label for="sortByCtr">Click Through Rate</label></span>
	</div>

	<div id="paramGroup_sortDir" class="report-parameter-group">
		Sort Direction:
		<span><input type="radio" name="sortDir" id="sortDirAsc" value="asc"<?php echo ( !isset( $reportParams['sortDir'] ) || isset( $reportParams['sortDir'] ) && $reportParams['sortDir'] == 'asc' ? $checkedTrue : $checkedFalse ) ?>><label for="sortDirAsc">Ascending</label></span>
		<span><input type="radio" name="sortDir" id="sortDirDesc" value="desc"<?php echo ( isset( $reportParams['sortDir'] ) && $reportParams['sortDir'] == 'desc' ? $checkedTrue : $checkedFalse ) ?>><label for="sortDirDesc">Descending</label></span>
	</div>

	<div id="paramGroup_submit" class="report-parameter-group">
		<input type="submit" value="Generate Report" class="button">
	</div>
</form>