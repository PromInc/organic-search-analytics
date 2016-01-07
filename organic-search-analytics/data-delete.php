<?php $titleTag = "Data Delete | Organic Search Analytics"; ?>

<?php include_once('inc/html/_head.php'); ?>

	<?php
	/* Get domains */
	$query = "SELECT id,value FROM ".mysql::DB_TABLE_SETTINGS." WHERE type='sites_google' AND data='1' ORDER BY value ASC";
	$result = $mysql->query( $query );
	$domains = array();
	foreach( $result as $domain_data ) {
		$domains[ $domain_data['id'] ] = $domain_data['value'];
	}
	?>

	<?php
	/* Process deletion of data */
	if( isset( $_POST ) && isset( $_POST['data_delete_btn'] ) && $_POST['data_delete_btn'] == "Delete Records" ) {
		ini_set('max_execution_time', 600);  //300 seconds = 5 minutes

		/* Remove button from POST variables */
		unset( $_POST['data_delete_btn'] );

		/* Setup counters */
		$processed = 0; $success = 0;
		foreach( $_POST as $idString => $date ) {
			/* Set criteria */
			$params = explode( "_", $idString );
			$settings_id = $params[0];
			$url = $domains[$settings_id];
			$search_engine = $params[1];

			/* Build and run query */
			$query = "DELETE FROM ".mysql::DB_TABLE_SEARCH_ANALYTICS." WHERE search_engine = '".$search_engine."' AND domain = '".$url."' AND date = '".$date."'";
			$result = $mysql->query( $query );

			/* Increment counters */
			$processed += 1;
			if( $result ) { $success += 1; }

			/* Once completed */
			if( $processed == count( $_POST ) ) {
				if( $success == count( $_POST ) ) {
					$_SESSION['alert_success'] = "Data for ".$success." of ".count( $_POST )." days have been deleted";
					$core->redirect( basename( $_SERVER['SCRIPT_NAME'] ), 302 );
				} else {
					$_SESSION['alert_error'] = "Data for only ".$success." of ".count( $_POST )." days was deleted";
					$core->redirect( basename( $_SERVER['SCRIPT_NAME'] ), 302 );
				}
			}
		}
	}
	?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics | Delete Data</h1>

	<h2>Google Search Console</h2>
	<hr>
	<h3>Search Analytics</h3>
	<p>If you feel there is an issue with the data captured for a particular date, you can delete the data for that day from this page.  Once a date has been deleted, you will need to re-capture the data using the <a href="data-capture.php">data capture</a> page.</p>
	<p>Data deletion is not permitted outside the date range of when data is allowed from Google Search Console.</p>
	<ul>
		<li>Google offers data for approximelty <?php echo $dataCapture::GOOGLE_SEARCH_ANALYTICS_MAX_DAYS ?> days</li>
		<li>The most recent data is <?php echo $dataCapture::GOOGLE_SEARCH_ANALYTICS_MAX_DATE_OFFSET ?> days from today.</li>
	</ul>
	<?php $dateRange = $dataCapture->getGoogleAvailableDates(); ?>
	<p>Date range available for deletion: <?php echo $dateRange['start'] ?> to <?php echo $dateRange['end'] ?></p>

	<p>Domains:</p>
		<form id="data_delete" action="<?php echo basename( $_SERVER['SCRIPT_NAME'] ) ?>" method="post">
			<?php $catId = "googleSearchAnalytics"; ?>
			<?php $search_engine = "google"; ?>

			<?php foreach( $domains as $domain_id => $domain_url ) { ?>
				<ul class="searchEngine" id="<?php echo $catId ?>">
					<li><?php echo $domain_url ?></li>
					<?php $googleSearchAnalyticsDates = $dataCapture->getGoogleDatesWithData( $domain_url, true ); ?>
					<?php if( $googleSearchAnalyticsDates->num_rows > 0 ) { ?>
						<p domainid="<?php echo $domain_id ?>" class="button toggler" mode="Select"><span>Select</span> all dates for <?php echo $domain_url ?></p>
						<ul>
							<?php foreach( $googleSearchAnalyticsDates as $date ) { ?>
								<label for="<?php echo $domain_id ?>_<?php echo $search_engine ?>_<?php echo $date['date'] ?>"><?php echo $date['date'] ?></label>
								<input type="checkbox" id="<?php echo $domain_id ?>_<?php echo $search_engine ?>_<?php echo $date['date'] ?>" name="<?php echo $domain_id ?>_<?php echo $search_engine ?>_<?php echo $date['date'] ?>" value="<?php echo $date['date'] ?>" domainid="<?php echo $domain_id ?>">
								<br/>
							<?php } ?>
						</ul>
					<?php } else { ?>
						<p>There is no data for this domain.</p>
					<?php } ?>
				</ul>
			<?php } ?>

		<div id="confirm_msg" style="display:none;">
			Are you sure you want to delete these records?
			<div id="confirm_yes" class="button">Yes</div>
		</div>
		<input type="submit" name="data_delete_btn" id="data_delete_btn" value="Delete Records" style="display: none;">
	</form>

	<hr>

	<script type="text/javascript">
		/* Detect checkbox change */
		jQuery("#data_delete input[type=checkbox]").change(function(){
			toggleProceed();
		});

		/* Process button display */
		function toggleProceed() {
			if( jQuery("#data_delete input:checkbox:checked").length > 0) {
				jQuery("#confirm_msg").show();
			} else {
				jQuery("#confirm_msg").hide();
			}
		}

		/* Confirmation toggle */
		jQuery("#confirm_yes").click(function(){
			jQuery("#data_delete_btn").show();
		});

		/* Select all buttons */
		jQuery(".toggler").click(function(){
			var domain_id = jQuery(this).attr('domainid');
			var mode = jQuery(this).attr('mode');
			if( mode == "Select" ) {
				var newMode = 'Deselect';
				var newSetting = true;
			} else {
				var newMode = 'Select';
				var newSetting = false;
			}

			jQuery("#data_delete").find('input[type=checkbox][domainid=' + domain_id + ']').prop("checked", newSetting);
			jQuery(this).attr('mode',newMode);
			jQuery(this).children('span').text(newMode);
			toggleProceed();
		});
	</script>


<?php include_once('inc/html/_foot.php'); ?>