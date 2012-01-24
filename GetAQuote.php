<?php
require_once 'class/gfCRUD.class.php';
require_once 'class/gfLocation.class.php';
require_once 'class/gfVehicle.class.php';
require_once 'FirePHP/firePHP.php';

//Set the Debugging mode to True
Debug::setDebug(true);

$crud = new CRUD();
$location = new Location($crud);
$locResSet = $location->getAllLocations();

$vehicle = new Vehicle($crud);
$vehResSet = $vehicle->getAllVehicles();
?>

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

		$required = array('user_name', 'user_email', 'user_tel', 'quote_message', 'vehicleType', 'departureLoc', 'destinationLoc', 'departureDate');

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
		$val->checkTextLength('departureDate',10);
		$val->removeTags('departureDate');
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
		    }
		    
		    $submitted = "Quotation: Congratulation! Your Form has been submitted!";
		    if (Debug::getDebug()) {
			Fb::info($submitted);
		    }
		    
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
	<form action="GetAQuote.php" method="POST">
	    <ul>
		<li>
		    <?php 
			if (isset($errors['user_name'])) { 
			    echo '<span class="warning">' . $errors['user_name'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">Name:<span class="required">&#42;</span></span>	
		    <input type="text" maxlength="32" size="20" name="user_name"
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
		    <?php 
			if (isset($errors['user_email'])) { 
			    echo '<span class="warning">' . $errors['user_email'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">Email:<span class="required">&#42;</span></span>	
		    <input type="text" maxlength="96" size="20" name="user_email" 
		       <?php				
			    if (isset($missing)) {				    
				echo 'value ="'.htmlentities($_POST['user_email'], ENT_COMPAT, 'UTF-8').'"';
			    }
			?>
		    />
		</li>
		<li>	
		    <?php 
			if (isset($errors['user_tel'])) { 
			    echo '<span class="warning">' . $errors['user_tel'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">Phone:<span class="required">&#42;</span></span>	
		    <input type="text" maxlength="96" size="20" name="user_tel" 
			<?php				
			    if (isset($missing)) {				    
				echo 'value ="'.htmlentities($_POST['user_tel'], ENT_COMPAT, 'UTF-8').'"';
			    }
			?>						 
		    />		    
		</li>
		
		<li>
		    <span class="leftWidth">Travel From:<span class="required">&#42;</span></span>
		    <select name="departureLoc">
			<?php
			    foreach($locResSet as $lrs){
				echo '<option value="'.$lrs[locationId].'">'.$lrs[locationName].'</option>';				
			    }
			?>			
		    </select>		    
		</li>
		
		<li>
		    <span class="leftWidth">Travel To:<span class="required">&#42;</span></span>
		    <select name="destinationLoc">
			<?php
			    foreach($locResSet as $lrs){
				echo '<option value="'.$lrs[locationId].'">'.$lrs[locationName].'</option>';
			    }
			?>			
		    </select>		    
		</li>
		
		<li>
		     <?php 
			if (isset($errors['departureDate'])) { 
			    echo '<span class="warning">' . $errors['departureDate'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">Travel Date:<span class="required">&#42;</span></span>
		    <input type="text" maxlength="96" size="10" id="departureDate" name="departureDate" />
		    <label for="sltHours">at</label>
		    <select id="sltHours" name="sltHours" class="hours"></select>
		    <label for="sltMinutes">:</label>
		    <select id="sltMinutes" name="sltMinutes" class="mins"></select>
		</li>
		
		<li>
		    <span class="leftWidth">Return Date:</span>
		    <input type="text" maxlength="96" size="10" id="returnDate" name="returnDate" />
		    <label for="sltHoursRet">at</label>
		    <select id="sltHoursRet" name="sltHoursRet" class="hours"></select>
		    <label for="sltMinutesRet">:</label>
		    <select id="sltMinutesRet" name="sltMinutesRet" class="mins"></select>
		</li>		    
		
		<li>
		    <span class="leftWidth">Vehicle type:<span class="required">&#42;</span></span>
		    <select name ="vehicleType">
			<?php
			    foreach($vehResSet as $vrs){
				echo '<option value="'.$vrs[vehicleId].'">'.$vrs[vehicleName].'</option>';
			    }
			?>
		    </select>
		</li>
		
		<li>
		    <?php 
			if (isset($errors['quote_message'])) { 
			    echo '<span class="warning">' . $errors['quote_message'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">Additional Request: </span>
		    <textarea maxlength="500" cols="40" name="quote_message" rows="6"><?php 
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
	<script src="js/DropdownLoader.js" type="text/javascript" charset="utf-8"></script>
	
	<script language="javascript" type="text/javascript">
	    
	    /**************************************************
	     * Date Range Picker 
	     **************************************************/	    
	    $(function() {
		var dates = $( "#departureDate, #returnDate" ).datepicker({
		    defaultDate: "+1w",		   
		    changeMonth: true,
		    numberOfMonths: 1,
		    //dateFormat: 'dd/mm/yy',
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
	    
	    
	</script>
    </body>
</html>
