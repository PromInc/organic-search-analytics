/**
*  Sends an AJAX request for data to capture
*
*  @param catId     String   Category ID for which data is being captured
*  @param script     String   File name/path to run
*  @param domain     String   Domain name to capture data for
*  @param date     String   Date to search.  YYYY-MM-DD
*  @param callback     String   Function to run upon success
*
*  @returns   Object   Database records.  MySQL object
*/
function ajaxScript(catId, script, domain, date, callback) {
	jQuery.ajax({
		type : "GET",
		url : script,
		data : {
			type : catId,
			domain : domain,
			date : date
		},
		beforeSend: function( xhr ) {
			$("#"+catId+" li[domain='"+domain+"'][date='"+date+"']").append('<span style="margin-left: 5px;">Processing...</span>');
		},
		success: function(data) {
			if( callback ) {
				window[callback](catId, domain, date, data);
			}
		},
		error: function(xhr, status, errorThrown) {
			$("#"+catId+" li[domain='"+domain+"'][date='"+date+"']").append('<span style="margin-left: 5px;">Error, try again.</span>');
		}
	});
}


/**
*  Hides granularity when date is not the group by method
*  Displays the appropriate sortBy features
*/
var groupBySortSettings = {
	groupByDate: {
		defaultSortBy: "sortByDate",
		disable: ["sortByQuery","sortByPage"],
		hideGranularity: false
	},
	groupByQuery: {
		defaultSortBy: "sortByQuery",
		disable: ["sortByDate","sortByQueries","sortByPage"],
		hideGranularity: true
	},
	groupByPage: {
		defaultSortBy: "sortByPage",
		disable: ["sortByDate","sortByQuery","sortByPages"],
		hideGranularity: true
	}
};
jQuery("#report-custom input:radio[name=groupBy]").change(function(e){
	// Toggle granularity
	if( !groupBySortSettings[e.target.id]['hideGranularity'] ) {
		jQuery( "#paramGroup_granularity" ).show();
	} else {
		jQuery( "#paramGroup_granularity" ).hide();
	}

	// Disable non-applicable sort by options
	jQuery("#paramGroup_sortBy .sortByOption").show();
	jQuery("input[name=sortBy]").attr("disabled",false);
	for( disableOption in groupBySortSettings[e.target.id]['disable'] ) {
		jQuery("#"+groupBySortSettings[e.target.id]['disable'][disableOption]).parent("span.sortByOption").hide();
		jQuery("#"+groupBySortSettings[e.target.id]['disable'][disableOption]).attr("disabled",true).prop("checked",false);
	}

	// Set default option if an option is not set
	if( !jQuery('input[name=sortBy]').is(':checked') || jQuery("#"+jQuery('input[name=sortBy]:checked').attr("id")).attr("disabled") == "disabled" ) {
		jQuery( "#"+groupBySortSettings[e.target.id]["defaultSortBy"] ).prop("checked",true);
	}
});


/**
*  Hides date pickers when date range is selected.
*/
jQuery("#report-custom input:radio[name=date_type]").change(function(e){
	if( e.target.id == "date_type_hard_set" ) {
		jQuery( "#paramGroup_dateStart, #paramGroup_dateEnd" ).show();
		updateDateRange();
	} else {
		jQuery( "#paramGroup_dateStart, #paramGroup_dateEnd" ).hide();
	}
});


/**
*  Toggles expanding content
*
*  Expects a parent element with class of "expandable"
*  Clicking on a child H2 element will toggle the display of
*  content with class of "expandingBox"
*/
jQuery(".expandable").each(function(){
	var element = this;
	jQuery( '#' + element.id + ' h2' ).click(function() {
		jQuery( '#' + element.id + ' .expandingBox').toggle();
	});
});


/**
*  Date Picker
*
*  Prevent start date from being later than end date
*
*  Directly modifies the jQuery UI element
*
*  @param element     String   Element to modify
*  @param endDate     Date   Last date avaialble for selection
*/
/* Date Picker - Prevent start date from being later than end date */
function updateStartDate( element, endDate ) {
	$( element ).datepicker( "option", "maxDate", endDate );
}


/**
*  Date Picker
*
*  Update display for number of days in the date range
*/
/* Date Picker - Count number of days */
function updateDateRange() {
	var date_start = $( "#date_start_inline" ).datepicker( "getDate" );
	var date_end = $( "#date_end_inline" ).datepicker( "getDate" );
	var date_range = Math.round( Math.abs( ( date_start.getTime() - date_end.getTime() ) / ( 24*60*60*1000 ) ) ) + 1;
	var date_range_display = " (" + date_range + " day" + ( date_range > 1 ? "s" : "" ) + ")";
	$("#date_range_count").empty().text( date_range_display );
}


/**
*  Tooltips
*
*  jQuery UI tool tips
*
*  Add a tooltip to any <label> element with an attribute setting of "tooltip"
*/
/* Date Picker - Count number of days */
$( "label" ).tooltip({
	items: "label[tooltip]",
	content: function() { return $( this ).attr('tooltip'); },
	tooltipClass: "tooltips"
});