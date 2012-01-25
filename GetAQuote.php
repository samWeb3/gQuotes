<?php
require_once 'FirePHP/firePHP.php';
require_once 'class/gfCRUD.class.php';
require_once 'class/gfLocation.class.php';
require_once 'class/gfVehicle.class.php';
require_once 'class/gfInstances.class.php';
require_once 'class/gfQuotationForm.php';

//Set the Debugging mode to True
Debug::setDebug(true);

$crud = new CRUD();
$location = new Location($crud);
$locResSet = $location->getAllLocations();

$vehicle = new Vehicle($crud);
$vehResSet = $vehicle->getAllVehicles();

$instance = new gfInstances();
$vehicle = new Vehicle($crud);
?>

<!--For Javascript validation see
http://randomactsofcoding.blogspot.com/2008/09/starting-with-jquery-validation-plug-in.html
-->

<!DOCTYPE html>
<html>
    <head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="css/dr/jquery.ui.all.css" rel="stylesheet" />
	<style type="text/css">
	    html, body, div, ol, ul, dl, dd, dt, fieldset, p, form, h1, h2, h3, h4, h5, iframe, blockquote, pre, img, label, legend, strong, span, em, table, caption, tbody, tfoot, thead, tr, th, td {
		font-family: inherit;
		font-style: inherit;
		font-weight: inherit;
		margin: 0;
		padding: 0;
	    }
	    fieldset, input, select, textarea, button, table, th, td, pre {
		font: 75%/1.6em "Lucida Grande",Verdana,Geneva,Helvetica,Arial,sans-serif;
	    }
	    ol, ul {
		list-style: none outside none;
	    }
	    body {
		font-family: Arial;
		font-size: 12px;
	    }
	    ul li span.leftWidth{
		float: left;
		width: 125px !important;
		list-style: none;		
	    }
	    li, h1 { padding-bottom: 20px;}
	    .warning {
		color: #f00;
		font-weight: bold;
	    }
	    .error{
		border: 1px solid red;
		background: #F9F9DE;
	    }
	    label.error{
		display: none !important;
		border: none !important;
	    }	    
	</style>
    </head>

    <body>
	<?php
	$missing = null;
	$errors = null;
	$success = null;

	if (filter_has_var(INPUT_POST, getAQuote)) { //better than isset as it returns true even in case of an empty string 	  
	    
	    try {
		require_once 'class/gfValidator.php';

		$required = array('user_name', 'user_email', 'user_tel', 'quote_message', 'vehicleType', 'departureLoc', 'destinationLoc');

		//retrieve the input and check whether any fields are missing	    
		$val = new Validator($required);

		//Validate each field and generate errror
		$val->checkTextLength('user_name', 3, 30);
		$val->removeTags('user_name');
		$val->isEmail('user_email');
		$val->matches('user_tel', '/[0-9]{3,11}/');
		$val->checkTextLength('quote_message', 5, 500);
		$val->useEntities('quote_message');
		
		$val->isInt('departureLoc');
		$val->isInt('destinationLoc');
		
		//need better ways to validate departureDate		
		//$val->noFilter('departureDate');
		
		//need better ways to validate departureDate
		
		$val->isInt('vehicleType');		

		//check the validation test has been set for each required field
		$filtered = $val->validateInput();

		$userName = $filtered['user_name'];
		$userEmail = $filtered['user_email'];
		$userTel = $filtered['user_tel'];
		$quoteMessage = $filtered['quote_message'];
		$vehicleId = $filtered['vehicleType'];
		$departureLoc = $filtered['departureLoc'];
		$destinationLoc = $filtered['destinationLoc'];

		$missing = $val->getMissing();
		$errors = $val->getErrors();
		
		//if nothing is mission or no errors is thrown
		if (!$missing && !$errors) {
		    
		    $min = 60;
		    $sec = 60;
		    
		    $departureDateUnix = strtotime($_POST['departureDate']) + ($_POST['sltHours'] * $min * $sec) + ($_POST['sltMinutes'] * $sec);		  
		    if (isset($_POST['returnDate']) && $_POST['returnDate'] != ""){			
			$returnDateUnix = strtotime($_POST['returnDate']) + ($_POST['sltHoursRet'] * $min * $sec) + ($_POST['sltMinutesRet'] * $sec);			
		    } 	    
		    
		    $user = new User($userName, $userEmail, $userTel);
		    $vehicle->setVehicleId($vehicleId);
		    
		    $quotationForm = new QuotationForm($crud, $instance, $user, $vehicle, $departureLoc, $departureDateUnix, $destinationLoc, $returnDateUnix, $quoteMessage);
		    
		    if (Debug::getDebug()) {
			fb($userName, "Fname", FirePHP::INFO);
			fb($userEmail, "Email", FirePHP::INFO);
			fb($userTel, "Tel", FirePHP::INFO);
			fb($quoteMessage, "Message", FirePHP::INFO);
			fb($vehicleId, "Vehicle ID", FirePHP::INFO);
			fb($departureLoc, "DepartureLoc", FirePHP::INFO);
			fb($destinationLoc, "DestinationLoc", FirePHP::INFO);
			fb($_POST['departureDate'], "Departure Date", FirePHP::INFO);
			fb($_POST['sltHours'], "Hours", FirePHP::INFO);
			fb($_POST['sltMinutes'], "Minutes", FirePHP::INFO);
			fb($_POST['returnDate'], "Return Date", FirePHP::INFO);
			fb($_POST['sltHoursRet'], "Hours Ret", FirePHP::INFO);
			fb($departureDateUnix, "Departure Date in Unix", FirePHP::INFO);
			fb($returnDateUnix, "Return Date in Unix", FirePHP::INFO);
		    }
		   
		    $submitted = "Quotation: Congratulation! Your Form has been submitted!";
		    if (Debug::getDebug()) {
			Fb::info($submitted);
		    }
		    print_r($_COOKIE);
		    setcookie("StickyForm_user_name");
		    setcookie("StickyForm_departureDate");
		    print_r($_COOKIE);
		    unset($_POST['user_name'], $_POST['user_email'], $_POST['user_tel'], $_POST['quote_message']);
		    
		} else {
		    if (Debug::getDebug()){
			Fb::info("One or More missing fields");
		    }
		}
	    } catch (Exception $e) {
		echo $e;
	    }
	    /*try {
		require_once 'class/gfValidator.php';

		$required = array('user_name', 'user_email', 'user_tel', 'quote_message');

		//retrieve the input and check whether any fields are missing	    
		$val = new Validator($required);

		//Validate each field and generate errror
		$val->checkTextLength('user_name', 3, 30);
		$val->removeTags('user_name');
		$val->isEmail('user_email');
		$val->matches('user_tel', '/[0-9]{3,11}/');
		$val->checkTextLength('quote_message', 5, 500);
		$val->useEntities('quote_message');

		//check the validation test has been set for each required field
		$filtered = $val->validateInput();

		$fname = $filtered['user_name'];
		$email = $filtered['user_email'];
		$tel = $filtered['user_tel'];
		$enquiry = $filtered['quote_message'];

		$missing = $val->getMissing();
		$errors = $val->getErrors();

		//if nothing is mission or no errors is thrown
		if (!$missing && !$errors) {
		    try {
			/*$cbf = new CallBackForm($fname, $email, $tel, $enquiry);

			$submitted = "CallBack: Congratulation! Your Form has been submitted!";
			if (Debug::getDebug()) {
			    Fb::info($submitted);
			}
			unset($_POST['user_name'], $_POST['user_email'], $_POST['user_tel'], $_POST['quote_message']);*/
			
			/*echo "form submitted!";
		    } catch (Exception $e) {
			echo $e->getMessage();
		    }
		} else {
		    if (Debug::getDebug()) {
			fb($filtered, "All Values not set", FirePHP::INFO);
		    }
		}
	    } catch (Exception $e) {
		echo $e;
	    }*/
	}
	?>
	
	
	<h1>Quotation</h1>
	<form action="GetAQuote.php" id="quotationForm" method="POST">
	    <ul>
		<li>
		    <?php if (isset($errors['user_name'])) { ?>
			    <span class="warning"> <?php echo $errors['user_name'] ?></span><br /> 				
		    <?php } ?>
		    <span class="leftWidth">Name:<span class="required">&#42;</span></span>	
		    <input type="text" maxlength="32" size="20" id="user_name" name="user_name"
		       <?php
			    //Sticky Form: The Essential Guide to Dreamweaver CS4 with CSS, Ajax, and PHP
			    if (isset($missing)) { //if any field a are missing retain the info				
				//ENT_COMPAT: converts double quote to $quote; but lives single quote alone
				echo 'value ="'.htmlentities($_POST['user_name'], ENT_COMPAT, 'UTF-8').'"';
			    }				
			?>
		    />
		</li>		
		<li>
		    <?php if (isset($errors['user_email'])) { ?>
			    <span class="warning"> <?php echo $errors['user_email'] ?></span><br /> 				
		    <?php } ?>
		    <span class="leftWidth">Email:<span class="required">&#42;</span></span>	
		    <input type="text" maxlength="96" size="20" id="user_email" name="user_email" 
			<?php if (isset($missing)) { ?>
				value ="<?php echo htmlentities($_POST['user_email'], ENT_COMPAT, 'UTF-8')?>"
			<?php } ?>
		    />
		    
		</li>
		<li>	
		    <?php if (isset($errors['user_tel'])) { ?>
			    <span class="warning"><?php echo $errors['user_tel'] ?></span><br /> 				
		    <?php } ?>
		    <span class="leftWidth">Phone:<span class="required">&#42;</span></span>	
		    <input type="text" maxlength="96" size="20" id="user_tel" name="user_tel" 
			<?php if (isset($missing)) { ?>
				value ="<?php echo htmlentities($_POST['user_tel'], ENT_COMPAT, 'UTF-8')?>"
			<?php } ?>						 
		    />		    
		</li>
		
		<li>
		    <span class="leftWidth">Travel From:<span class="required">&#42;</span></span>
		    <select name="departureLoc" id="departureLoc">
			<?php foreach($locResSet as $lrs){ ?>
				<option value="<?php echo $lrs[locationId] ?>"><?php echo $lrs[locationName] ?></option>				
			<?php } ?>			
		    </select>		    
		</li>
		
		<li>
		    <span class="leftWidth">Travel To:<span class="required">&#42;</span></span>
		    <select name="destinationLoc" id="destinationLoc">
			<?php foreach($locResSet as $lrs){ ?>
				<option value="<?php echo $lrs[locationId] ?>"><?php echo $lrs[locationName] ?></option>				
			<?php } ?>			
		    </select>		    
		</li>
		
		<li>
		    <?php if (isset($errors['departureDate'])) { ?>
			    <span class="warning"> <?php echo $errors['departureDate'] ?></span><br /> 				
		    <?php } ?>
		    <span class="leftWidth">Travel Date:<span class="required">&#42;</span></span>
		    <input type="text" maxlength="96" size="10" id="departureDate" name="departureDate" 		
			<?php if (isset($missing)) { ?>
				value ="<?php echo htmlentities($_POST['departureDate'], ENT_COMPAT, 'UTF-8')?>"
			<?php } ?>			   
		    />
		    <label for="sltHours">at</label>
		    <select id="sltHours" name="sltHours" class="hours"></select>
		    <label for="sltMinutes">:</label>
		    <select id="sltMinutes" name="sltMinutes" class="mins"></select>
		</li>
		
		<li>
		    <span class="leftWidth">Return Date:</span>
		    <input type="text" maxlength="96" size="10" id="returnDate" name="returnDate" 

		    <?php if (isset($missing)) { ?>
			value ="<?php echo htmlentities($_POST['returnDate'], ENT_COMPAT, 'UTF-8')?>"
		    <?php } ?>
			   
		    />
		    <label for="sltHoursRet">at</label>
		    <select id="sltHoursRet" name="sltHoursRet" class="hours"></select>
		    <label for="sltMinutesRet">:</label>
		    <select id="sltMinutesRet" name="sltMinutesRet" class="mins"></select>
		</li>		    
		
		<li>
		    <span class="leftWidth">Vehicle type:<span class="required">&#42;</span></span>
		    <select name ="vehicleType" id="vehicleType">
			<?php
			    foreach($vehResSet as $vrs){
				echo '<option value="'.$vrs[vehicleId].'">'.$vrs[vehicleName].'</option>';
			    }
			?>
		    </select>
		</li>
		
		<li>
		    <?php if (isset($errors['quote_message'])) { ?>
			    <span class="warning"><?php echo $errors['quote_message'] ?></span><br /> 				
		    <?php } ?>
		    <span class="leftWidth">Additional Request: </span>
		    <textarea maxlength="500" cols="40" id="quote_message" name="quote_message" rows="6"><?php 
			if (isset($missing)) {			    
			    echo htmlentities($_POST['quote_message'], ENT_COMPAT, 'UTF-8');
			} //It's important to position the opening and closing PHP tags right up agains the <textarea> tags. Else you get unwanted whitespace in the text area.
		    ?></textarea>
		</li>
		<li><span class="leftWidth">&nbsp; </span>
		    <input class="buttonBackground" type="submit" value="Get A Quote" name="getAQuote" />
		    <input class="buttonBackground" type="reset" value="Reset" id="reset" name="reset">
		</li>
	    </ul>
	</form>
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script><!--For Date Range Picker-->
	<script src="js/dr/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/dr/jquery.ui.datepicker.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script><!--Jquery form validation plugin-->
	
	<script src="js/DropdownLoader.js" type="text/javascript" charset="utf-8"></script>
	
	<script type="text/javascript" language="javascript" src="js/jquery.StickyForms.js"></script>
	
	<script language="javascript" type="text/javascript">
	    
	    /**************************************************
	     * Date Range Picker 
	     **************************************************/	    
	    $(function() {
		var dates = $( "#departureDate, #returnDate" ).datepicker({
		    defaultDate: "+1w",		   
		    changeMonth: true,
		    numberOfMonths: 1,
		    dateFormat: 'dd-mm-yy',
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
	    var dropdownLoader = new DropdownLoader();	    
	    dropdownLoader.loadHours("sltHours");
	    dropdownLoader.loadMinutes("sltMinutes");	    
	    dropdownLoader.loadHours("sltHoursRet");
	    dropdownLoader.loadMinutes("sltMinutesRet");

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
			    required: "*",
			    email: "Please enter a valid email address, example: you@yourdomain.com"
			},
			user_tel: {
			    required: "",
			    minlength: "11 charecter req"
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
	    
	    $(function() {
		$('#quotationForm').StickyForm({
		    'debug': 'false', // [true/false] Enable debugging
		    'elementTypes': 'all', // [text,password,checkbox,radio,textarea,select-one,all] separate element types with comma separated values (default is all)
		    'cookieLifetime': '30', // [integer] number of days of cookie lifetime
		    'disableOnSubmit': 'true', // [true/false] disable submitting the form while the form is processing
		    'excludeElementIDs': 'sf_password', // [ID1,ID2] exclude element IDs with comma separated values
		    'scope' : 'global', // [single/global] should the values be sticky only on this form (single) or across all forms on site (default is global)
		    'disableIfGetSet' : 'elq' // ['',$_GET var] set to the $_GET var.  If this $_GET var is present, it will automatically disable the plugin. (default is '')
		});
	    });
	   
	    
	</script>
    </body>
</html>
