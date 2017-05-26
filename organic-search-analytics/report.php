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
$colHeadingPrimary = "Query";
if( $reportParams ) {
	$reportDetails = $reports->getReportQueryAndHeading( $reportParams );
	$groupBy = $reportDetails['groupBy'];
	$groupByAlias = $reportDetails['groupByAlias'];
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
	if( $groupBy == "query" ) {
		$colHeadingPrimary = "Query";
	} elseif( $groupByAlias == "date" ) {
		if( isset( $reportDetails['granularity'] ) ) {
			$colHeadingPrimary = ucfirst( $reportDetails['granularity'] );
		} else {
			$colHeadingPrimary = ucfirst( $groupByAlias );
		}
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
		<div id="reportParameters">
			<h2 id="reportParametersHeading">Report Parameters</h2>
			<div><?php echo implode( "</div><div>", $reportDetails['pageHeadingItems'] ); ?></div>
		</div>
		<div class="clear"></div>

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
			if( $groupByAlias == "date" && isset( $reportDetails['granularity'] ) ) {
				$sortByForQuery = $groupBy;
			} else {
				$sortByForQuery = $reportDetails['sortBy'];
			}

			$reportQuery = "SELECT ".
							$groupBy. ",".
							" count(" . ( $groupBy != "query" ? 'DISTINCT ' : '' ) . "query) as 'queries',".
							" count(DISTINCT page) as 'pages',".
							" sum(impressions) as 'impressions',".
							" sum(clicks) as 'clicks',".
							" sum(avg_position*impressions)/sum(impressions) as 'avg_position',".
							" avg(ctr) as 'ctr'".
							" FROM ".$mysql::DB_TABLE_SEARCH_ANALYTICS.
							" " . $reportDetails['whereClauseTable'] .
							" GROUP BY " . $groupBy .
							" ORDER BY " . $sortByForQuery . " ASC"
							;

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
				$totals = array( 'rows' => 0, 'queries' => 0, 'pages' => 0, 'impressions' => 0, 'clicks' => 0, 'avg_position' => 0, 'avg_ctr' => 0 );
				$rows = array();
				for( $r=0; $r < count($outputTable); $r++ ) {
					$rows[ $outputTable[$r][$groupBy] ] = array( "queries" => $outputTable[$r]["queries"], "pages" => $outputTable[$r]["pages"], "impressions" => $outputTable[$r]["impressions"], "clicks" => $outputTable[$r]["clicks"], "avg_position" => $outputTable[$r]["avg_position"] );
					/* Add to totals */
					$totals['queries'] += $outputTable[$r]["queries"];
					$totals['pages'] += $outputTable[$r]["pages"];
					$totals['impressions'] += $outputTable[$r]["impressions"];
					$totals['clicks'] += $outputTable[$r]["clicks"];
					$totals['avg_position'] += $outputTable[$r]["avg_position"];
				}

				/* Calculate averages */
				$totals['avg_position'] = number_format( $totals['avg_position'] / count($outputTable), 2 );
				$totals['avg_ctr'] = number_format( ( $totals["clicks"] / $totals["impressions"] ) * 100, 2 )."%";

				/* Format numbers */
				/*
				TODO: The totals really should be a separate query for more accurate/useful data.
					  i.e. On a Date report with Year granularity, you might get 50 queries from year 1 and 47 from year 2.  These get added together to be 97.  But this doesn't account for any overlap in the two years.  There may really only be 52 unique queries as an example.
				*/
				$totals['rows'] = number_format( count($outputTable), 0 );
				$totals['queries'] = number_format( $totals['queries'], 0 );
				$totals['pages'] = number_format( $totals['pages'], 0 );
				$totals['impressions'] = number_format( $totals['impressions'], 0 );
				$totals['clicks'] = number_format( $totals['clicks'], 0 );

				/* Build an array for chart data */
				$jqData = array( $groupByAlias => array(), "impressions" => array(), "clicks" => array(), "ctr" => array(), "avg_position" => array() );

				foreach ( $rows as $index => $values ) {
					if( $groupByAlias == "page" ) {
						$indexDomain = substr( $index, 0, strlen( $reportParams['domain'] ) );
						if( strlen( $index ) > strlen( $indexDomain ) && $indexDomain == $reportParams['domain'] ) {
							$jqData[$groupByAlias][] = substr( $index, strlen( $reportParams['domain'] ) );
						} else {
							$jqData[$groupByAlias][] = ( strlen( $index ) > 0 ? $index : Reports::EMPTY_RESULT_PLACEHOLDER );
						}
					} else {
						$jqData[$groupByAlias][] = $index;
					}

					if( $groupByAlias == "date" ) {
						$jqData['impressions'][$index][] = $values["impressions"];
						$jqData['clicks'][$index][] = $values["clicks"];
						$jqData['ctr'][$index][] = ( $values["clicks"] / $values["impressions"] ) * 100;
						$jqData['avg_position'][$index][] = $values["avg_position"];
					} else {
						$jqData['impressions'][] = $values["impressions"];
						$jqData['clicks'][] = $values["clicks"];
						$jqData['ctr'][] = ( $values["clicks"] / $values["impressions"] ) * 100;
						$jqData['avg_position'][] = $values["avg_position"];

					}
				}

				$posString = "";
				$posMax = 0;
				$labelMax = 50;
				$labelMaxSuffix = "...";

				if( $groupByAlias == "date" ) {
					asort( $jqData[$groupByAlias] );
					$jqData[$groupByAlias] = array_values( $jqData[$groupByAlias] );

					$c = 0;
					foreach( $jqData[$groupByAlias] as $key ) {
						if( $c != 0 ) {
							$posString .= ",";
						}
						$posString .= "['".addslashes($key)."',".$jqData['avg_position'][$key][0]."]";
						if( $jqData['avg_position'][$key][0] > $posMax ) { $posMax = $jqData['avg_position'][$key][0]; }
						$c+=1;
					}
				} else {
					$num = count( $jqData[$groupByAlias] );
					for( $c=0; $c<$num; $c++ ) {
						if( $c != 0 ) {
							$posString .= ",";
						}
						$posString .= "['".htmlspecialchars($jqData[$groupByAlias][$c], ENT_QUOTES)."',".round( $jqData['avg_position'][$c], 2 )."]";
						if( $jqData['avg_position'][$c] > $posMax ) { $posMax = $jqData['avg_position'][$c]; }
					}
				}
				?>

				<?php
				if( in_array( $groupByAlias, array('query','page') ) ) {
					$chartType = "bar";
				} else {
					$chartType = "line";
				}
				?>

				<div id="reportchart" class="<?php echo $chartType ?>"></div>
				<div id="reportChartContainer">
					<?php if( in_array( $groupByAlias, array('date') ) ) { ?>
					<div id="zoomReset" class="button floatR">Reset Zoom</div>
					<?php } ?>
					<div id="chartDataCallout"></div>
				</div>
				<div class="clear"></div>

				<script type="text/javascript">
				truncateLongTick = function (formatString, value) {
					var maxLen = 50;
					var suffix = '...';
				
					var allowedLen = maxLen - suffix.length;
				
					if( value.length > allowedLen ) {
						value = value.substring( 0, allowedLen ) + suffix;
					}
				
					return value;
				}

				$(document).ready(function(){
					<?php if( preg_match( '/date/', $groupByAlias ) ) { ?>
							var line1=[<?php echo $posString ?>];
							var plot2 = $.jqplot('reportchart', [line1], {
									title: 'Average Position<?php echo ( strlen( $reportDetails['chartLabel'] ) > 0 ?" | " . $reportDetails['chartLabel'] . "":"") ?>',
									axes: {
										xaxis: {
											renderer: $.jqplot.DateAxisRenderer,
											tickRenderer: $.jqplot.CanvasAxisTickRenderer,
											numberTicks: <?php echo count( $rows ) ?>,
											min: '<?php echo date( 'n/j/Y', strtotime( $jqData[$groupByAlias][0] ) ) ?>',
											max: '<?php echo date( 'n/j/Y', strtotime( $jqData[$groupByAlias][ count( $rows ) - 1 ] ) ) ?>',
											tickOptions: {
												<?php
												if( isset( $reportDetails['granularity'] ) ) {
													switch( $reportDetails['granularity'] ) {
														case "year":
															echo "formatString: '%y',";
															break;
														case "month":
															echo "formatString: '%m-%y',";
															break;
														case "week":
														default:
															echo "formatString: '%m-%d-%y',";
													}
												} else {
													echo "formatString:'%m-%d-%y',";
												}
												?>
												angle: -30
											},
										},
										yaxis: {
											max: 1,
											min: <?php echo $posMax ?> + (<?php echo $posMax ?> * .1),
											tickOptions: {
												formatString: '%i'
											},
										}
									},
									highlighter: {
										show: true,
										tooltipAxes: 'xy',
										useAxesFormatters: true,
										showTooltip: true
									},
									series: [{lineWidth:4, markerOptions:{style:'square'}}],
									cursor: {
										show: true,
										zoom: true,
										showTooltip: false
									}
							});
					<?php } elseif( $groupByAlias == "query" ) { ?>
							var plot2 = $.jqplot('reportchart', [[<?php echo $posString ?>]], {
									title:'Average Position | Query',
									seriesDefaults: {
										renderer:$.jqplot.BarRenderer,
										rendererOptions: {
											varyBarColor: true,
											showDataLabels: true
										},
									},
									axesDefaults: {
										tickRenderer: $.jqplot.CanvasAxisTickRenderer,
										tickOptions: {
											angle: 30,
											fontSize: '10pt'
										}
									},
									axes: {
										xaxis: {
											renderer: $.jqplot.CategoryAxisRenderer,
											tickOptions: {
												formatter: truncateLongTick
											}
										},
										yaxis: {
											max: 1,
											min: <?php echo $posMax ?> + (<?php echo $posMax ?> * .1),
											tickOptions: {
												formatString:'%i'
											}
										}
									},
									cursor: {
										showTooltip: false
									},
									highlighter: {
										show: true,
										tooltipAxes: 'y',
										useAxesFormatters: false,
										showTooltip: true,
										tooltipFormatString: '%s'
									}
							});
							/* Hover displays information on HTML page */
							$('#reportchart').bind('jqplotDataHighlight', 
								function (ev, seriesIndex, pointIndex, data) {
									$('#chartDataCallout').html('URL: '+plot2._plotData[seriesIndex][pointIndex][0]+'<br>Average SERP Position: '+plot2._plotData[seriesIndex][pointIndex][1]);
								}
							);
							$('#reportchart').bind('jqplotDataUnhighlight', 
								function (ev) {
									$('#chartDataCallout').html('');
								}
							);
					<?php } elseif( $groupByAlias == "page" ) { ?>
							var plot2 = $.jqplot('reportchart', [[<?php echo $posString ?>]], {
									title:'Average Position | Page',
									seriesDefaults:{
										renderer:$.jqplot.BarRenderer,
										rendererOptions: {
											varyBarColor: true,
											showDataLabels: true
										},
									},
									axesDefaults: {
										tickRenderer: $.jqplot.CanvasAxisTickRenderer,
										tickOptions: {
											angle: 30,
											fontSize: '10pt'
										}
									},
									axes: {
										xaxis: {
											renderer: $.jqplot.CategoryAxisRenderer,
											tickOptions: {
												formatter: truncateLongTick
											}
										},
										yaxis: {
											max: 1,
											min: <?php echo $posMax ?> + (<?php echo $posMax ?> * .1),
											tickOptions: {
												formatString: '%i'
											}
										}
									},
									cursor: {
										showTooltip: false
									},
									highlighter: {
										show: true,
										tooltipAxes: 'y',
										useAxesFormatters: false,
										showTooltip: true,
										tooltipFormatString: '%s'
									}
							});
							/* Hover displays information on HTML page */
							$('#reportchart').bind('jqplotDataHighlight', 
								function (ev, seriesIndex, pointIndex, data) {
									$('#chartDataCallout').html('URL: '+plot2._plotData[seriesIndex][pointIndex][0]+'<br>Average SERP Position: '+plot2._plotData[seriesIndex][pointIndex][1]);
								}
							);
							$('#reportchart').bind('jqplotDataUnhighlight', 
								function (ev) {
									$('#chartDataCallout').html('');
								}
							);
					<?php } ?>

					/* Zoom Reset */
					$('#zoomReset').click(function() { plot2.resetZoom() });
				});
				</script>

				<?php if( $reportDetails['sortDir'] == 'desc' ) { $rows = array_reverse( $rows, true ); } ?>

				<?php
				$totalsExcludes = array(
					"date" => array(),
					"query" => array("queries"),
					"page" => array("pages")
				);
				?>

				<table class="sidebysidetable sidebysidetable_col mT2p mB2p">
					<tr>
						<?php foreach ( $totals as $index => $values ) { ?>
							<?php if( in_array( $index, $totalsExcludes[$groupByAlias] ) ) { continue; } ?>
							<td><?php echo ucwords( strtolower( str_replace( "_", " ", $index ) ) ) ?></td>
						<?php } ?>
					</tr>
					<tr>
						<?php foreach ( $totals as $index => $values ) { ?>
							<?php if( in_array( $index, $totalsExcludes[$groupByAlias] ) ) { continue; } ?>
							<td><?php echo $values ?></td>
						<?php } ?>
					</tr>
				</table>
				<div class="clear"></div>

				<?php $modifyType = array( 'date' => array( "week", "month", "year" ) ); ?>
				
				<table class="sidebysidetable sidebysidetable_col">
					<tr id="data_headings">
						<td id="data_heading_<?php echo ( array_key_exists( strtolower( $groupByAlias ), $modifyType ) && in_array( strtolower( $colHeadingPrimary ), $modifyType[$groupByAlias] ) ? $groupByAlias : strtolower( $colHeadingPrimary ) ) ?>" class="taL">
							<span class="data_heading" datatype="<?php echo ( array_key_exists( strtolower( $colHeadingPrimary ), $modifyType ) && in_array( strtolower( $colHeadingPrimary ), $modifyType[$groupByAlias] ) ? $groupByAlias : strtolower( $colHeadingPrimary ) ) ?>"><?php echo ucfirst( strtolower( $colHeadingPrimary ) ) ?></span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
						</td>

						<?php if( in_array( $groupByAlias, array("date") ) ) { ?>
						<td id="data_heading_queries">
							<span class="data_heading" datatype="queries"><?php echo $colHeadingSecondary ?></span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
							<label class="reportTooltip" tooltip="Number of unique queries displayed in the SERP for this date"><span></span></label>
						</td>
						<?php } ?>

						<?php if( $groupByAlias == "page" ) { ?>
						<td id="data_heading_instances">
							<span class="data_heading" datatype="queries"><?php echo $colHeadingSecondary ?></span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
							<label class="reportTooltip" tooltip="Number of unique queries returned this page in the SERP"><span></span></label>
						</td>
						<?php } ?>

						<?php if( in_array( $groupByAlias, array("query","date") ) ) { ?>
						<td id="data_heading_pages">
							<span class="data_heading" datatype="pages">Pages</span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
							<label class="reportTooltip" tooltip="Number of URLs that were displayed in the SERP for this result"><span></span></label>
						</td>
						<?php } ?>

						<td id="data_heading_impressions">
							<span class="data_heading" datatype="impressions">Impressions</span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
							<label class="reportTooltip" tooltip="Number of times this result was displayed in the SERP"><span></span></label>
						</td>

						<td id="data_heading_clicks">
							<span class="data_heading" datatype="clicks">Clicks</span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
							<label class="reportTooltip" tooltip="Number of times this result was clicked in the SERP"><span></span></label>
						</td>

						<td id="data_heading_avg_position">
							<span class="data_heading" datatype="avg_position">Avg Position</span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
							<label class="reportTooltip" tooltip="Average position in the SERP when this result triggered an impression"><span></span></label>
						</td>

						<td id="data_heading_ctr">
							<span class="data_heading" datatype="ctr">CTR</span>
							<span class="sort sort_asc"></span>
							<span class="sort sort_desc"></span>
							<label class="reportTooltip" tooltip="Percent of clicks for this result from the SERP (Clicks / Impressions)"><span></span></label>
						</td>
					</tr>

					<?php foreach ( $rows as $index => $values ) { ?>
						<?php
						if( $reportParams['groupBy'] == "date" ) {
							switch( strtolower( $colHeadingPrimary ) ) {
								case "week":
									$dateStart = $index;
									$dateEnd = date( "Y-m-d", strtotime( $index . " +6 days" ) );
									break;
								case "month":
									$dateStart = $index . "-01";
									$monthStart = strtotime( $index );
									$addNumDays = cal_days_in_month(CAL_GREGORIAN, date( "m", $monthStart ), date( "Y", $monthStart ) ) - 1;
									$dateEnd = date( "Y-m-d", strtotime( $index . " +" . $addNumDays . " days" ) );
									break;
								case "year":
									$dateStart = $index . "-01-01";
									$dateEnd = $index . "-12-31";
									break;
								default:
									$dateStart = $index;
									$dateEnd = $index;
							}
						}
						?>

						<tr>
							<td class="taL">
								<?php
								$url = false;
								if( $groupByAlias == "query" ) {
									$url = 'https://www.google.com/search?q=' . urlencode($index) . '&start=' . floor($values["avg_position"] / 10) * 10;
								} elseif( $groupByAlias == "page" ) {
									$url = $index;
								}

								if( $url ) {
									echo '<a href="' . $url . '" target="_blank">';
								}
								$indexDomain = substr( $index, 0, strlen( $reportParams['domain'] ) );
								if( strlen( $index ) > strlen( $indexDomain ) && $indexDomain == $reportParams['domain'] ) {
									echo substr( $index, strlen( $reportParams['domain'] ) );
								} else {
									echo ( strlen( $index ) > 0 ? $index : '<i>( ' . Reports::EMPTY_RESULT_PLACEHOLDER . ' )</i>' );
								}

								if( $url ) {
									echo '<i class="fa fa-external-link reportLinkExt" aria-hidden="true"></i>';
									echo '</a>';
								}
								?>
							</td>

							<?php if( in_array( $groupByAlias, array("date","page") ) ) { ?>
							<td>
								<?php
								$urlParams = false;
								switch( $reportParams['groupBy'] ) {
									case "date":
										$urlParams = $reportParams;
										$urlParams['groupBy'] = "query";
										$urlParams['sortBy'] = "query";
										$urlParams['date_start'] = $dateStart;
										$urlParams['date_end'] = $dateEnd;
										break;
									case "page":
										$urlParams = $reportParams;
										$urlParams['groupBy'] = "query";
										$urlParams['sortBy'] = "query";
										$urlParams['page'] = $index;
										$urlParams['pageMatch'] = 'exact';
										break;
									default:
								}
								http_build_query($urlParams);
								?>

								<?php if( $urlParams ) { ?>
								<a href="report.php?<?php echo http_build_query($urlParams) ?>">
								<?php } ?>

								<?php echo number_format( $values["queries"] ) ?>

								<?php if( $urlParams ) { ?>
								</a>
								<?php } ?>
							</td>
							<?php } ?>

							<?php if( !in_array( $groupByAlias, array("page") ) ) { ?>
							<td>
								<?php
								$urlParams = false;
								switch( $reportParams['groupBy'] ) {
									case "date":
										$urlParams = $reportParams;
										$urlParams['groupBy'] = "page";
										$urlParams['sortBy'] = "page";
										$urlParams['date_start'] = $dateStart;
										$urlParams['date_end'] = $dateEnd;
										break;
									case "query":
										$urlParams = $reportParams;
										$urlParams['groupBy'] = "page";
										$urlParams['sortBy'] = "page";
										$urlParams['queryMatch'] = "exact";
										$urlParams['query'] = $index;
										break;
									default:
								}
								http_build_query($urlParams);
								?>

								<?php if( $urlParams ) { ?>
								<a href="report.php?<?php echo http_build_query($urlParams) ?>">
								<?php } ?>

								<?php echo number_format( $values["pages"] ) ?>

								<?php if( $urlParams ) { ?>
								</a>
								<?php } ?>
							</td>
							<?php } ?>
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

		<?php } else { ?>
			<div class="italic mT2p">
				No records found.
			</div>
		<?php } ?>
	<?php } ?>
	<div class="clear"></div>

<?php include_once('inc/html/_foot.php'); ?>
