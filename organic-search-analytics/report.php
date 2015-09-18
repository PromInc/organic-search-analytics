<?php $titleTag = "Reporting | Organic Search Analytics"; ?>

<?php include_once('inc/html/_head.php'); ?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Search Analytics Reporting</h1>

	<h2>Generate Report</h2>
	<form action="report-custom.php" method="get">
		<p>
			<label>Domain: </label><br>
			<?php
			$sitesList = $dataCapture->getSitesGoogleSearchConsole();
			foreach( $sitesList as $key => $site ) {
				echo '<input type="radio" name="domain" id="'.$site['url'].'" value="'.$site['url'].'" '.($key==0?' checked':'').'><label for="'.$site['url'].'">'.$site['url'].'</label><br>';
			}
			?>
		</p>
		<p>
			<label for="query">Query: </label><input type="text" name="query" id="query" value="">
			<div>
				Query Match Type:
				<span style="margin-left: 10px;"><input type="radio" name="queryMatch" id="queryMatchBroad" value="broad" checked><label for="queryMatchBroad">Broad</label></span>
				<span style="margin-left: 10px;"><input type="radio" name="queryMatch" id="queryMatchExact" value="exact"><label for="queryMatchExact">Exact</label></span>
			</div>
		</p>

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

		<p>
			<label for="search_type">Search Type: </label>
			<select name="search_type" id="search_type">
				<option value="ALL">ALL</option>
				<option value="web">WEB</option>
				<option value="image">IMAGE</option>
				<option value="video">VIDEO</option>
			</select>
		</p>

		<p>
			<label for="device_type">Device Type: </label>
			<select name="device_type" id="device_type">
				<option value="ALL">ALL</option>
				<option value="desktop">Desktop</option>
				<option value="mobile">MOBILE</option>
				<option value="tablet">Tablet</option>
			</select>
		</p>

		<p>
			<label for="date_start">Date Start: </label>
			<select name="date_start" id="date_start">
				<option value=""></option>
				<?php for( $d = $startOffset; $d < $numDays; $d++ ) { echo '<option value="' . date( 'Y-m-d', $now - ( 86400 * $d ) ) . '">' . date( 'Y-m-d', $now - ( 86400 * $d ) ) . '</option>'; } ?>
			</select>
		</p>
		<p>
			<label for="date_end">Date End: </label>
			<select name="date_end" id="date_end">
				<option value=""></option>
				<?php for( $d = $startOffset; $d < $numDays; $d++ ) { echo '<option value="' . date( 'Y-m-d', $now - ( 86400 * $d ) ) . '">' . date( 'Y-m-d', $now - ( 86400 * $d ) ) . '</option>'; } ?>
			</select>
		</p>

		<!--
		<p>
			<div>
				Group By:
				<span style="margin-left: 10px;"><input type="radio" name="groupBy" id="groupByDate" value="date" checked><label for="groupByDate">Date</label></span>
				<span style="margin-left: 10px;"><input type="radio" name="groupBy" id="groupByQuery" value="query"><label for="groupByQuery">Query</label></span>
			</div>
		</p>
		-->

		<p>
			<div>
				Sort By:
				<span style="margin-left: 10px;"><input type="radio" name="sortBy" id="sortByDate" value="date" checked><label for="sortByDate">Date</label></span>
				<span style="margin-left: 10px;"><input type="radio" name="sortBy" id="sortByQuery" value="query"><label for="sortByQuery">Query</label></span>
				<span style="margin-left: 10px;"><input type="radio" name="sortBy" id="sortByImpressions" value="impression"><label for="sortByImpressions">Impression</label></span>
				<span style="margin-left: 10px;"><input type="radio" name="sortBy" id="sortByAvgPos" value="avgpos"><label for="sortByAvgPos">Average Position</label></span>
				<span style="margin-left: 10px;"><input type="radio" name="sortBy" id="sortByCtr" value="ctr"><label for="sortByCtr">Click Through Rate</label></span>
			</div>
		</p>

		<p>
			<div>
				Sort Direction:
				<span style="margin-left: 10px;"><input type="radio" name="sortDir" id="sortDirAsc" value="asc" checked><label for="sortDirAsc">Ascending</label></span>
				<span style="margin-left: 10px;"><input type="radio" name="sortDir" id="sortDirDesc" value="desc"><label for="sortDirDesc">Descending</label></span>
			</div>
		</p>

		<p>
			<input type="submit" value="Generate Report">
		</p>
	</form>

	<?php include ('inc/html/reportQuickLinks.php'); ?>

<?php include_once('inc/html/_foot.php'); ?>