<?php
require_once 'FirePHP/firePHP.php';
require_once 'class/gfCRUD.class.php';
require_once 'class/gfLocation.class.php';
require_once 'class/gfVehicle.class.php';
require_once 'class/gfInstances.class.php';
require_once 'class/gfQuotationForm.class.php';

//Set the Debugging mode to True
Debug::setDebug(true);

$crud = new CRUD();
$instance = new gfInstances();

$instanceLocation = array(  "151" => 
				array(			
				    '2'=>'London',
				    '3'=>'Bristol',
				    '4'=>'Manchester'
				    
				),
			    "152" => 
				array(
				    '1'=>'Fleet',			
				    '2'=>'London',			    
				    '4'=>'Manchester',			    
				    '6'=>'Newcastle',
				    '7'=>'Edingburgh',			    			    		    
				    '11'=>'Bournemouth',
				    '12'=>'Gatwick',			    
				    '14'=>'Swansea',
				    '15'=>'Blackpool'
				)
			);

$instanceVehicle = array(   "151" => 
				array(
				    '4' => '14 Seater Mini Bus',
				    '3' => '12 Seater Mini Bus',
				    '5' => '16 Seater Mini Bus',
				    '6' => '29 Seater Coach',
				    '7' => '33 Seater Coach',
				    '8' => '39 Seater Coach',
				    '11' => '52 Seater Coach',
				    '12' => '59 Seater Jambo Coach'			
				),
			    "152" => 
				array(
				    '4' => '14 Seater Mini Bus',
				    '6' => '29 Seater Coach',
				    '7' => '33 Seater Coach',			    
				    '11' => '52 Seater Coach'
				)
		    );

$vehicle = new Vehicle($crud);
$vehResSet = $vehicle->getInstanceVehicles($instance, $instanceVehicle);

$location = new Location($crud);
$locResSet = $location->getInstanceLocations($instance, $instanceLocation);

?>

<!DOCTYPE html>
<html>
    <head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="css/dr/jquery.ui.all.css" rel="stylesheet" />	
	<link href="css/dr/demos.css" rel="stylesheet" />
	
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
	    
	    body {
		font-family: Arial;
		font-size: 12px;
	    }
	    ol, ul {
		list-style: none outside none;
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

	//better than isset as it returns true even in case of an empty string
	if (filter_has_var(INPUT_POST, getAQuote)) {  	  
	    
	    try {
		require_once 'class/gfValidator.class.php';

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
		    
		    //Unset cookie
		    //$cookieArr = array("StickyForm_departureLoc", "StickyForm_destinationLoc", "StickyForm_vehicleType");		    
		    // $quotationForm->unsetCookie($cookieArr);
		    
		    //UnsetPostValue
		    //$fieldnameArr = array('user_name',  'user_email', 'user_tel', 'quote_message', 'vehicleType', 'departureLoc', 'destinationLoc');
		    $fieldnameArr = array('user_name',  'user_email', 'user_tel', 'quote_message');
		    $quotationForm->resetForm($fieldnameArr);
		    
		} else {
		    if (Debug::getDebug()){
			Fb::info("One or More missing fields");
		    }
		}
	    } catch (Exception $e) {
		echo $e;
	    }
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
			<?php if (isset($missing)) { ?>				
			    value ="<?php echo htmlentities($_POST['user_name'], ENT_COMPAT, 'UTF-8') ?>"
			<?php } ?>	
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
			    value ="<?php echo htmlentities($_POST['user_tel'], ENT_COMPAT, 'UTF-8') ?>"
			<?php } ?>						 
		    />		    
		</li>
		
		<li>
		    <span class="leftWidth">Travel From:<span class="required">&#42;</span></span>
		    <select name="departureLoc" id="departureLoc">			
			<?php //asort($locResSet);?>
			<?php foreach($locResSet as $locId => $locName){ ?>
			    <option value="<?php echo $locId ?>"><?php echo $locName ?></option>				
			<?php } ?>
		    </select>		    
		</li>
		
		<li>
		    <span class="leftWidth">Travel To:<span class="required">&#42;</span></span>
		    <select name="destinationLoc" id="destinationLoc">
			<?php asort($locResSet);?>
			<?php foreach($locResSet as $locId => $locName){ ?>
			    <option value="<?php echo $locId ?>"><?php echo $locName ?></option>				
			<?php } ?>
		    </select>		    
		</li>
		
		<li>
		    <?php if (isset($errors['departureDate'])) { ?>
			    <span class="warning"> <?php echo $errors['departureDate'] ?></span><br /> 				
		    <?php } ?>
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
		    <select name ="vehicleType" id="vehicleType">			
			<?php foreach($vehResSet as $vehId => $vehName){ ?>
			    <option value="<?php echo $vehId ?>"><?php echo $vehName ?></option>				
			<?php } ?>
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
	<!--Jquery form validation plugin-->
	<script src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js" type="text/javascript" charset="utf-8"></script>	
	<script src="js/bindDepDest_bak.js" type="text/javascript" charset="utf-8"></script> 
	<script src="js/DropdownLoader.js" type="text/javascript" charset="utf-8"></script>	
	<script src="js/jquery.StickyForms.js" type="text/javascript" charset="utf-8"></script>	
	<script src="js/getAQuote.js" type="text/javascript" charset="utf-8"></script>  
	
    </body>
</html>
