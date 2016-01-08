<?php $titleTag = "Reporting | Organic Search Analytics"; ?>

<?php $displayReport = ( count( $_GET ) > 0 ); ?>
<?php $displayReportToggleHide = ( $displayReport ? ' style="display:none;"' : '' ); ?>

<?php include_once('inc/html/_head.php'); ?>

<?php $reports = new Reports(); ?>

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
?>

<?php
$colHeadingSecondary = "Queries";
if( $reportParams ) {
	$reportDetails = $reports->getReportQueryAndHeading( $reportParams );
	$groupBy = $reportDetails['groupBy'];
}
?>

<?php
if( isset( $reportDetails ) ) {
	ini_set('max_execution_time', 600);  //300 seconds = 5 minutes
}
?>

<?php
/* Set labels */
if( isset( $groupBy ) ) {
	if( preg_match( '/\(date\)/', $groupBy ) ) {
		$colHeadingPrimary = substr( $groupBy, 0, strpos( $groupBy, '(' ) );
	} else {
		$colHeadingPrimary = $groupBy;
	}
}
?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics Reporting</h1>

	<div id="reportSettings" class="expandable col col49 mR1p">
		<h2>Report Settings</h2>
		<div class="expandingBox"<?php echo $displayReportToggleHide ?>>
			<?php include_once( 'inc/html/reportSettings.php' ); ?>
		</div>
	</div>

	<div id="reportQuickLinks" class="expandable col col49 mL1p">
		<h2>Report Custom Links</h2>
		<div class="expandingBox"<?php echo $displayReportToggleHide ?>>
			<p>To add a report to Quick Links, generate a report using the parameters above and choose the <i>Save this Report to Quick Links</i> link.</p>
			<?php echo $reports->getSavedReportsByCategoryHtml( $reports->getSavedReportsByCategory() ); ?>
		</div>
	</div>
	<div class="clear"></div>

	<?php if( isset( $reportDetails ) ) { ?>
		<h2><?php echo implode( ", ", $reportDetails['pageHeadingItems'] ); ?></h2>

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
			$reportQuery = "SELECT " . $groupBy . ", count(" . ( $groupBy != "query" ? 'DISTINCT ' : '' ) . "query) as 'queries', sum(impressions) as 'impressions', sum(clicks) as 'clicks', avg(avg_position) as 'avg_position' FROM ".$mysql::DB_TABLE_SEARCH_ANALYTICS." " . $reportDetails['whereClauseTable'] . "GROUP BY " . $groupBy . " ORDER BY " . $reportDetails['sortBy'] . " ASC";

			/* Get MySQL Results */
			$outputTable = $outputChart = array();
			if( $resultTable = $GLOBALS['db']->query($reportQuery) ) {
				while ( $rowsTable = $resultTable->fetch_assoc() ) {
					$outputTable[] = $rowsTable;
				}
			}

			/* If Results */
			if( count($outputTable) > 0 ) {
				/* Put MySQL Results into an array */
				$totals = array( 'rows' => 0, 'queries' => 0, 'impressions' => 0, 'clicks' => 0, 'avg_position' => 0, 'avg_ctr' => 0 );
				$rows = array();
				for( $r=0; $r < count($outputTable); $r++ ) {
					$rows[ $outputTable[$r][$groupBy] ] = array( "queries" => $outputTable[$r]["queries"], "impressions" => $outputTable[$r]["impressions"], "clicks" => $outputTable[$r]["clicks"], "avg_position" => $outputTable[$r]["avg_position"] );
					/* Add to totals */
					$totals['queries'] += $outputTable[$r]["queries"];
					$totals['impressions'] += $outputTable[$r]["impressions"];
					$totals['clicks'] += $outputTable[$r]["clicks"];
					$totals['avg_position'] += $outputTable[$r]["avg_position"];
				}
				/* Calculate averages */
				$totals['avg_position'] = number_format( $totals['avg_position'] / count($outputTable), 2 );
				$totals['avg_ctr'] = number_format( ( $totals["clicks"] / $totals["impressions"] ) * 100, 2 );
				/* Format numbers */
				$totals['rows'] = number_format( count($outputTable), 0 );
				$totals['queries'] = number_format( $totals['queries'], 0 );
				$totals['impressions'] = number_format( $totals['impressions'], 0 );
				$totals['clicks'] = number_format( $totals['clicks'], 0 );

				/* Build an array for chart data */
				$jqData = array( $groupBy => array(), "impressions" => array(), "clicks" => array(), "ctr" => array(), "avg_position" => array() );

				foreach ( $rows as $index => $values ) {
					$jqData[$groupBy][] = $index;
					$jqData['impressions'][] = $values["impressions"];
					$jqData['clicks'][] = $values["clicks"];
					$jqData['ctr'][] = ( $values["clicks"] / $values["impressions"] ) * 100;
					$jqData['avg_position'][] = $values["avg_position"];
				}

				$num = count( $jqData[$groupBy] );
				$posString = "";
				$posMax = 0;
				for( $c=0; $c<$num; $c++ ) {
					if( $c != 0 ) {
						$posString .= ",";
					}
					$posString .= "['".$jqData[$groupBy][$c]."',".$jqData['avg_position'][$c]."]";
					if( $jqData['avg_position'][$c] > $posMax ) { $posMax = $jqData['avg_position'][$c]; }
				}
				?>

				<div id="reportchart"></div>
				<div id="reportChartContainer">
					<div id="zoomReset" class="button floatR">Reset Zoom</div>
					<div id="chartDataCallout"></div>
				</div>
				<div class="clear"></div>

				<script type="text/javascript">
				$(document).ready(function(){
					<?php if( preg_match( '/date/', $groupBy ) ) { ?>
							var line1=[<?php echo $posString ?>];
							var plot2 = $.jqplot('reportchart', [line1], {
									title:'Average Position<?php echo ( strlen( $reportDetails['chartLabel'] ) > 0 ?" | " . $reportDetails['chartLabel'] . "":"") ?>',
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
							
					<?php } elseif( $groupBy ==  "query" ) { ?>
							var plot2 = $.jqplot('reportchart', [[<?php echo $posString ?>]], {
									title:'Default Bar Chart',
									// animate: !$.jqplot.use_excanvas,
									seriesDefaults:{
											renderer:$.jqplot.BarRenderer,
											rendererOptions: {
												varyBarColor: true,
												showDataLabels: true
											},
									},
									legent: {
										show: true
									},
									axesDefaults: {
											tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
											tickOptions: {
												angle: 30,
												fontSize: '10pt'
											}
									},
									axes:{
										xaxis:{
											renderer: $.jqplot.CategoryAxisRenderer,
											pointLabels: { show: true }
										}
									},
									cursor:{
										show: true,
										zoom: true,
									},
									highlighter: {
										show: true,
										tooltipAxes: 'xy',
										useAxesFormatters: false,
										showTooltip: true,
										tooltipFormatString: '%s'
									}
							});

							/* Click displays information on HTML page */
							$('#reportchart').bind('jqplotDataClick', 
									function (ev, seriesIndex, pointIndex, data) {
											$('#chartDataCallout').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
									}
							);
					<?php } ?>

					/* Zoom Reset */
					$('#zoomReset').click(function() { plot2.resetZoom() });
				});
				</script>


				<?php if( $reportDetails['sortDir'] == 'desc' ) { $rows = array_reverse( $rows ); } ?>

				<table class="sidebysidetable sidebysidetable_col mT2p mB2p">
					<tr>
						<?php foreach ( $totals as $index => $values ) { ?>
							<td><?php echo ucfirst( strtolower( $index ) ) ?></td>
						<?php } ?>
					</tr>
					<tr>
						<?php foreach ( $totals as $index => $values ) { ?>
							<td><?php echo $values ?></td>
						<?php } ?>
					</tr>
				</table>
				<div class="clear"></div>

				<table class="sidebysidetable sidebysidetable_col">
					<tr id="data_headings">
						<td id="data_heading_<?php echo strtolower( $colHeadingPrimary ) ?>" class="taL">
							<span class="data_heading" datatype="<?php echo strtolower( $colHeadingPrimary ) ?>"><?php echo ucfirst( strtolower( $colHeadingPrimary ) ) ?></span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
						</td>
						<td id="data_heading_queries">
							<span class="data_heading" datatype="queries"><?php echo $colHeadingSecondary ?></span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
						</td>
						<td id="data_heading_impressions">
							<span class="data_heading" datatype="impressions">Impressions</span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
						</td>
						<td id="data_heading_clicks">
							<span class="data_heading" datatype="clicks">Clicks</span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
						</td>
						<td id="data_heading_avg_position">
							<span class="data_heading" datatype="avg_position">Avg Position</span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
						</td>
						<td id="data_heading_ctr">
							<span class="data_heading" datatype="ctr">CTR</span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
						</td>
					</tr>
					<?php foreach ( $rows as $index => $values ) { ?>
						<tr>
							<td class="taL"><?php echo $index ?></td>
							<td><?php echo number_format( $values["queries"] ) ?></td>
							<td><?php echo number_format( $values["impressions"] ) ?></td>
							<td><?php echo number_format( $values["clicks"] ) ?></td>
							<td><?php echo number_format( $values["avg_position"], 2 ) ?></td>
							<td><?php echo number_format( ( $values["clicks"] / $values["impressions"] ) * 100, 2 ) ?>%</td>
						</tr>
					<?php } ?>
				</table>

				<script type="text/javascript">
					/* Get current sort settings */
					var curSortBy = getCurrentSelection('sortBy');
					var curSortDir = getCurrentSelection('sortDir');
					/* Set appropriate sort icon */
					jQuery("#data_headings #data_heading_"+curSortBy+" .sort.sort_"+curSortDir).addClass("sort_active");

					/* Sort icon click */
					jQuery("#data_headings span.sort").click(function(){
						var sortBy = jQuery(this).siblings(".data_heading").attr("datatype");
						var sortDir = "";
						var regexpattern = /sort_/g;
						/* Get selected sort direction */
						this.classList.forEach(function(eaClass){
							if( regexpattern.exec(eaClass) ) {
								sortDir = eaClass.replace( regexpattern, '' );
							}
						});
						/* Perform the sort */
						setCurrentSelection('sortBy', sortBy);
						setCurrentSelection('sortDir', sortDir);
						submitForm();
					});

					/* Heading title click */
					jQuery("#data_headings span.data_heading").click(function(){
						/* Get current settings */
						var curSortBy = getCurrentSelection('sortBy');
						var curSortDir = getCurrentSelection('sortDir');
						/* Get new sort by */
						var sortBy = jQuery(this).attr("datatype");
						/* Determine and set sort direction */
						var sortDir = "";
						if( sortBy == curSortBy ) {
							if( curSortDir == "asc" ) { sortDir = "desc"; } else { sortDir = "asc"; }
						} else {
							sortDir = curSortDir;
						}
						/* Perform sort */
						setCurrentSelection('sortBy', sortBy);
						setCurrentSelection('sortDir', sortDir);
						submitForm();
					});

					function getCurrentSelection(type) {
						return jQuery("input[type='radio'][name='"+type+"']:checked").val();
					}

					function setCurrentSelection(type, value) {
						return jQuery("input[type='radio'][name='"+type+"'][value='"+value+"']").prop("checked", true);
					}

					function submitForm() {
						jQuery("#report-custom").submit();
					}
				</script>

		<?php } ?>
	<?php } ?>
	<div class="clear"></div>

<?php include_once('inc/html/_foot.php'); ?>