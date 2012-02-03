<?php
//Include the PS_Pagination class
require_once 'class/gfAdminQuotes.class.php';
require_once 'class/gfQuoteStats.class.php';
require_once 'FirePHP/firePHP.php';
require_once 'class/gfDatePicker.class.php';
require_once 'class/gfLocation.class.php';
require_once 'class/gfVehicle.class.php';
require_once 'class/gfInstances.class.php';

//Set the Debugging mode to True
Debug::setDebug(true);

$crud = new CRUD();
$location = new Location($crud);
$vehicle = new Vehicle($crud);
$instance = new gfInstances();
?>  
<!DOCTYPE html>
<html>
    <head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="css/dr/jquery.ui.all.css" rel="stylesheet" />
	<link href="css/dr/demos.css" rel="stylesheet" />
	<link href="css/quotes.css" rel="stylesheet" />
	<link href="css/hacks.css" rel="stylesheet" />
	<link href="css/bootstrap.css" rel="stylesheet" />	
	<link href="css/easypaginate.css" rel="stylesheet" />
	    
	<script>
	    //need this until php sets the value, Therefore need here
	    var dayRange = new Array();
	    var totalRec = new Array();//To hold all request for Quotes
	    var ansRec = new Array();//To hold answered Quote Records
	    var recType = "Quotes"; //For Label
	</script>
    </head>
    <body>
	<?php	
	$numLink = 10; //number of link
	
	DatePicker::setNoOfDays(31);
	$datePicker = new DatePicker($fromDate, $toDate, $dateRangeSet);
	$qtStats = new QuoteStats($crud, $datePicker, $instance);
	
	try {
	    //Get the From and To Date Range
	    if (isset($_GET['dateRangeSet'])) {
		
		if (isset($_GET['fromDate']) && isset($_GET['toDate'])) {
		    $fromDate = $_GET['fromDate'];
		    $toDate = $_GET['toDate'];

		    $ukFromDate = date("d M Y h:i:s A", strtotime($fromDate));
		    /*
		     * We are adding 86399sec (1day - 1sec) so unixToDate returns 11:59 PM(End of Day)
		     * instead of 12:00 AM(Begining of day)
		     */
		    $ukToDate = date("d M Y h:i:s A", strtotime($toDate) + (86399));
		}

		$dateRangeSet = $_GET['dateRangeSet'];

		if ($fromDate != "" && $toDate != "") {		    		    
		    $infoMessage = $datePicker->displayDateRangeMsg($ukFromDate, $ukToDate);
		    $qtStats->customStats($_GET['fromDate'], $_GET['toDate']);
		} else {
		    $infoMessage = $datePicker->displayDateRangeMsg($datePicker->getUkFromDate(), $datePicker->getUkToDate());
		    $qtStats->monthStats();
		}
	    } else {		
		$infoMessage = $datePicker->displayDateRangeMsg($datePicker->getUkFromDate(), $datePicker->getUkToDate());
		$qtStats->monthStats();
	    }

	    $adminQuotes = new AdminQuotes($crud, $datePicker, $instance);

	    //Check if Quotes link has been clicked
	    if ((isset($_GET['quoteId']))) {
		$adminQuotes->updateQuoteStatus($_GET['quoteId']);
		if ($fromDate != "" && $toDate != "") {		    
		    $infoMessage = $datePicker->displayDateRangeMsg($ukFromDate, $ukToDate);
		    $qtStats->customStats($_GET['fromDate'], $_GET['toDate']);
		} else {		    		    
		    $infoMessage = $datePicker->displayDateRangeMsg($datePicker->getUkFromDate(), $datePicker->getUkToDate());
		    $qtStats->monthStats();
		}
	    }
	    if ((isset($_GET['row_pp']))) {
		if (empty($_GET['row_pp'])) {
		    $errorMessage = "Please enter the number of records to be displayed";
		    $inputNum = 10;
		} else if (!is_numeric($_GET['row_pp'])) {
		    $errorMessage = "Please enter numeric values!";
		    $inputNum = 10;
		} else if ($_GET['row_pp'] <= 0) {
		    $errorMessage = "Record number should be greater than or equal to 1";
		    $inputNum = 10;
		} else {
		    $inputNum = $_GET['row_pp'];
		}
	    } else {
		$inputNum = 10;
	    }

	    $totalQtReq = $adminQuotes->countTotQuote();
	    $ansQtRq = $adminQuotes->countAnsQuote();
	    $unAnsQtReq = $adminQuotes->countUnAnsQuote();

	    if ((isset($_GET['quoteStatus']))) {
		$quoteStatus = $_GET['quoteStatus'];
		if ($quoteStatus == 0) {//Display UnAnswered Quotes	   
		    $resultSet = $adminQuotes->viewPaginateQuoteRequest($inputNum, $numLink, '0');
		} else if ($quoteStatus == 1) {//Display Answered Quotes	   
		    $resultSet = $adminQuotes->viewPaginateQuoteRequest($inputNum, $numLink, '1');
		} else if ($quoteStatus == 2) { // Total Quotes
		    $resultSet = $adminQuotes->viewPaginateQuoteRequest($inputNum, $numLink, '2');
		}
	    } else { //Display Total Quotes
		$resultSet = $adminQuotes->viewPaginateQuoteRequest($inputNum, $numLink, '2');
	    }
	} catch (Exception $ex) {
	    $errorMessage = $ex->getMessage();
	}
	?>	
	<div id="container">
	    <div id="datePickerHolder" class="group">
		<div id="switchDisplay">
		    <button id="viewStatBtn" class="btn default pull-left">View Statistics</button>		    
		    <button id="viewDashboardBtn" class="btn default pull-left">View Dashboard</button>
		</div>

		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get" class="pull-right">
		    <!--Used by JavaScript-->
		    <input class="medium" type="text" id="from" name="from" placeholder="Date From" />
		    <input class="medium" type="text" id="to" name="to" placeholder="Date To" />

		    <!--Used by PHP-->
		    <input type="hidden" id="fromDate" name="fromDate"/>
		    <input type="hidden" id="toDate" name="toDate"/>

		    <input type="submit" id="date" name="dateRangeSet" value="Display" class="btn default"/>
		</form>
	    </div>

	    <?php if (isset($infoMessage)) { ?>
		<div class='alert-message info fade in clear' id="dateRangeMsg" data-alert='alert'><a class='close' href='#'>&times;</a>
		    <?php echo "$infoMessage"; ?>
		</div>
	    <?php } ?>
	    
	    <div id="viewStatPnl">
		<div id="statPlaceholder"></div>
	    </div>	    

	    <p id="tooltipContainer"><input id="enableTooltip" type="checkbox" checked>Enable Tooltip</p>

	    <div id="viewDashboardPnl">
		<ul id="items">
		    <li>
			<h3>Total Requests</h3>    	
			<p class="dashboard">
			    <span class="data">
				<a href="<?php echo $_SERVER['PHP_SELF'] . "?quoteStatus=2&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate()."&dateRangeSet=".$datePicker->getDateRangeSet().'"'; ?>" class="dashboardLink" id="totQtReq">
				    <?php echo $totalQtReq ?>
				</a>
			    </span>
			</p>
		    </li>

		    <li>
			<h3>Answered Requests</h3>    	
			<p class="dashboard">
			    <span class="data">
				<a href="<?php echo $_SERVER['PHP_SELF'] . "?quoteStatus=1&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate()."&dateRangeSet=".$datePicker->getDateRangeSet().'"'; ?>" class="dashboardLink" id="ansQtReq">
				<?php echo $ansQtRq ?>
				</a>
			    </span>
			</p>
		    </li>
		    <li>
			<h3>Unanswered Requests</h3>    	
			<p class="dashboard">
			    <span class="data">
				<a href="<?php echo $_SERVER['PHP_SELF'] . "?quoteStatus=0&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate()."&dateRangeSet=".$datePicker->getDateRangeSet().'"' ?>" class="dashboardLink" id="unAnsQtReq">
				    <?php echo $unAnsQtReq ?>
				</a>
			    </span>
			</p>		  
		    </li>		    
	    </div> 
	    
	    <?php if (isset($errorMessage)) { ?>
		<div class='alert-message warning fade in' data-alert='alert'><a class='close' href='#'>&times;</a>
		    <?php echo "$errorMessage"; ?>
		</div>
	    <?php } ?>
	    
	    <div id="middle">
		<div id="search" class="group">
		    <div class="pull-left">
			<label for="filter">Filter Record: </label> 
			<input type="text" name="filter" value="" id="filter">			
		    </div>
		    <span id="dateFilter" class="pull-right">
			    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get" class="pull-right">
				<input type="hidden" name="quoteStatus" id="quoteStatus" value="<?php echo $adminQuotes->getQuoteStatus();?>">				
				<input type="hidden" name="fromDate" value="<?php echo $datePicker->getFromDate();?>">
				<input type="hidden" name="toDate" value="<?php echo $datePicker->getToDate();?>">
				<input type="hidden" name="dateRangeSet" value="<?php echo $datePicker->getDateRangeSet();?>">	
				<label id="disRecHelpText" class="help-inline">Enter valid number and press &#60;ENTER&#62; !&nbsp;</label>
				<input id="disRecord" class="input-small span2" type="text" size="15" placeholder="Display Record" name="row_pp">				
			    </form>
			</span>
		</div>	    
		
		<table class="zebra-striped tablesorter" id="CallBackTable">
		    <thead>
		    <tr>			
			<th>Date</th>
			<th>Personal Info</th>
			<th>Outward</th>
			<th>Return</th>
			<th>Additional Request</th>
			<th>Status</th>
		    </tr>
		    </thead>
		    <tbody>				
			<?php if ($resultSet) { ?>
			    <?php foreach ($resultSet as $r) { ?>
				<tr>
				    <td>
					<!--use unixtimestamp to sort date properly, then hide it using css-->
					<span class="unixDate"><?php echo $r[quoteDate]; ?></span>
					
					<span class="qDate"><?php echo $datePicker->convertUnixToDMY($r[quoteDate]); ?></span>
					<br /><span class="small unHighlight">
					<span class="qtime"><?php echo $datePicker->convertUnixToTime($r[quoteDate]); ?></span>
				    </td>
				    <td>
					<span class="qUserName"><?php echo $r[userName]; ?></span>
					<span class="qUserEmail"><?php echo $r[userEmail]; ?></span>
					<span class="qUserTel"><?php echo $r[userTel]; ?></span>
				    </td>
				    <td>
					<div class="journeyDetails alert-message block-message info">					    
					    <span class="arrow left outwardArr">&raquo;</span>
					    <div class="journeyDate">
						<?php echo $datePicker->convertUnixToDMY($r[departureDate]); ?>
						<span class="unHighlight">&nbsp;at&nbsp;</span>
						<?php echo $datePicker->convertUnixToTime($r[departureDate]); ?>
					    </div>
					    <div class="journeyLocations">
						<ul>
						    <li>
							<span class="floatLeft">From:</span>
							<span class="fromLoc"><?php echo $location->selectLocationName($r[departureLoc]); ?></span>
							<span></span>
						    </li>
						    <li>
							<span class="floatLeft">To:</span>
							<span class="toLoc"><?php echo $location->selectLocationName($r[destinationLoc]) ?></span>
						    </li>
						</ul>
					    </div>
					    <div class="block-message-footer">
						<?php echo $vehicle->selectVehicleName($r[vehicleId]); ?>
					    </div>
					</div>	
					
				    </td>
				    <td>
					<?php if ($r[returnDate] != "" || $r[returnDate] != null) { ?>
					<div class="journeyDetails alert-message block-message info">
					    <span class="arrow right returnArr">&laquo;</span>
					    <div class="journeyDate">
						<?php echo $datePicker->convertUnixToDMY($r[returnDate]); ?>
						<span class="unHighlight">&nbsp;at&nbsp;</span>
						<?php echo $datePicker->convertUnixToTime($r[returnDate]); ?>
					    </div>
					    <div class="journeyLocations">
						<ul>
						    <li>
							<span class="floatLeft">From:</span>
							<span class="fromLoc"><?php echo $location->selectLocationName($r[destinationLoc]) ?></span>
						    </li>
						    <li>
							<span class="floatLeft">To:</span>
							<span class="toLoc"><?php echo $location->selectLocationName($r[departureLoc]); ?></span>
						    </li>
						</ul>
					    </div>
					    <div class="block-message-footer">
						<?php echo $vehicle->selectVehicleName($r[vehicleId]); ?>
					    </div>					    
					</div>	
					<?php } else {?>
					    <div class="journeyDetails alert-message block-message error">						
						<span class="arrow right returnArr">&laquo;</span>
						<span class="noReturn">No Return ...</span>
					    </div>					    
					<?php } ?>
				    </td>				    
				    <td><?php echo $r[quoteMessage]; ?></td>
				    <td>
					<?php if ($r[quoteStatus] == 0) { ?>
					    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?quoteId=<?php echo $r[quoteId]; ?>&page=<?php echo $adminQuotes->getPageNo();?>&row_pp=<?php echo $adminQuotes->getRecordsPerPage(); ?>&quoteStatus=<?php echo $adminQuotes->getQuoteStatus(); ?>&param1=value1&param2=value2&fromDate=<?php echo $datePicker->getFromDate(); ?>&toDate=<?php echo $datePicker->getToDate(); ?>&dateRangeSet=<?php echo $datePicker->getDateRangeSet() ?>" class="btn danger">Pending...</a>
					<?php } else { ?>
					   <a href="#" class="btn success disabled">Answered</a>
					<?php } ?> 
				    </td>
				</tr>
			
			<?php } } else { ?>
				<tr>
				    <td colspan="6">
					<div class='alert-message block-message error' data-alert='alert'>
					    <div class="block-message-header">Oops! Records not available!</div>
					    <div class="block-message-body">Please enter valid date range and try again...</div>					    
					</div>
				    </td>
				</tr>			    
			<?php } ?>
		    </tbody>
		</table>
		
		<div class="cPaginator">
		    <?php echo $adminQuotes->getPaginatorNav(); ?>
		</div>
	    </div>
	    
	</div>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script><!--For Date Range Picker-->
	<script src="js/jquery.tablesorter.min.js"></script>	
	<script type="text/javascript" src="js/jquery.cookies.2.2.0.js"></script>
	<script type="text/javascript" src="js/easypaginate.js"></script>
	<script type="text/javascript" src="js/bootstrap-alerts.js"></script>		
	<script type="text/javascript" src="js/recordFilter.js"></script>	
	
	<!--for Date Range Picker-->
	<script src="js/dr/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/dr/jquery.ui.datepicker.js" type="text/javascript" charset="utf-8"></script>

	<!--For Generating stats-->
	<!--for the support on IE7 and IE8-->
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/stat/excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="js/stat/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="js/stat/jquery.flot.symbol.js"></script>
	<script language="javascript" type="text/javascript" src="js/stat/jquery.flot.stack.js"></script>
	<script language="javascript" type="text/javascript" src="js/graphicalStats.js"></script>
	<script language="javascript" type="text/javascript" src="js/adminQuoteView.js"></script>	
    </body>
</html>
