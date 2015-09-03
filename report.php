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
				echo '<input type="radio" name="domain" id="'.$site['url'].'" value="'.$site['url'].'"><label for="'.$site['url'].'">'.$site['url'].'</label><br>';
			}
			?>
		</p>
		<p>
			<label for="query">Query: </label><input type="text" name="query" id="query" value="">
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
		<p>
			<input type="submit" value="Generate Report">
		</p>
	</form>

	<?php include ('inc/html/reportQuickLinks.php'); ?>

<?php include_once('inc/html/_foot.php'); ?>