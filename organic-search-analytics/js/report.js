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
jQuery("#report-custom input:radio[name=groupBy]").change(function(e){
	if( e.target.id == "groupByQuery" ) {
		jQuery( "#paramGroup_granularity" ).hide();

		var checked_status = jQuery( "#sortByDate" ).prop("checked");

		jQuery( "#sortByDate" ).parent("span").hide();
		jQuery( "#sortByDate" ).attr("disabled",true).prop("checked",false);

		jQuery( "#sortByQuery" ).parent("span").show();
		jQuery( "#sortByQuery" ).attr("disabled",false);
		if( checked_status ) {
			jQuery( "#sortByQuery" ).prop("checked",true);
		}
	} else {
		jQuery( "#paramGroup_granularity" ).show();

		var checked_status = jQuery( "#sortByQuery" ).prop("checked");

		jQuery( "#sortByQuery" ).parent("span").hide();
		jQuery( "#sortByQuery" ).attr("disabled",true).prop("checked",false);

		jQuery( "#sortByDate" ).parent("span").show();
		jQuery( "#sortByDate" ).attr("disabled",false);
		if( checked_status ) {
			jQuery( "#sortByDate" ).prop("checked",true);
		}
	}
});


/**
*  Hides date pickers when date range is selected.
*/
jQuery("#report-custom input:radio[name=date_type]").change(function(e){
	if( e.target.id == "date_type_hard_set" ) {
		jQuery( "#paramGroup_dateStart, #paramGroup_dateEnd" ).show();
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