/***********************************************
 * JS Code specific to GetAQuote.php/htm
 ***********************************************/

/**************************************************
 * Date Range Picker 
 **************************************************/	    
$(function() {
    var dates = $( "#departureDate, #returnDate" ).datepicker({
	defaultDate: "+1w",		   
	changeMonth: true,
	numberOfMonths: 1,
	// dateFormat: 'mm-dd-yy',
	onSelect: function( selectedDate ) {
	    var option = this.id == "departureDate" ? "minDate" : "maxDate",
	    instance = $( this ).data( "datepicker" ),
	    date = $.datepicker.parseDate(
		instance.settings.dateFormat ||
		$.datepicker._defaults.dateFormat,
		selectedDate, instance.settings );
	    dates.not( this ).datepicker( "option", option, date );
	}
    });
}); 
	    
/**************************************************
* <select> DropDown Loader
**************************************************/
$(document).ready(function(){		
    var dropdownLoader = new DropdownLoader();	    
    dropdownLoader.loadHours("sltHours");
    dropdownLoader.loadMinutes("sltMinutes");	    
    dropdownLoader.loadHours("sltHoursRet");
    dropdownLoader.loadMinutes("sltMinutesRet");
});

/**************************************************
 * JavaScript Form Validation
 **************************************************/
$(document).ready(function(){
    $("#quotationForm").validate({
	rules: {
	    user_name: "required",			
	    user_email: "required",
	    user_tel: {
		required: true,
		minlength: 11
	    },			
	    departureDate: "required",			
	    vehicleType: "required", 
	    quote_message: "required"
			
	},
	messages: {
	    user_name: {
		required: ""
	    },
	    user_email: {
		required: "*"
	    },
	    user_tel: {
		required: ""
	    }, 			
	    departureDate: {
		required: ""
	    },
	    quote_message: {
		required: ""
	    }
	}
    });
});

/**************************************************
 * Sticky Form via Cookie
 **************************************************/	    
$(function() {
    $('#quotationForm').StickyForm({
	'debug': 'false', // [true/false] Enable debugging
	'elementTypes': 'all', // [text,password,checkbox,radio,textarea,select-one,all] separate element types with comma separated values (default is all)
	'cookieLifetime': '30', // [integer] number of days of cookie lifetime
	'disableOnSubmit': 'true', // [true/false] disable submitting the form while the form is processing
	'excludeElementIDs': 'user_name, user_email, user_tel, quote_message', // [ID1,ID2] exclude element IDs with comma separated values
	'scope' : 'single', // [single/global] should the values be sticky only on this form (single) or across all forms on site (default is global)
	'disableIfGetSet' : 'elq' // ['',$_GET var] set to the $_GET var.  If this $_GET var is present, it will automatically disable the plugin. (default is '')
    });
});	  

