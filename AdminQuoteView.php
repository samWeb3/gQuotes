<?php
//Include the PS_Pagination class
require_once 'class/gfAdminQuotes.class.php';
require_once 'class/gfCallBackStats.class.php';
require_once 'FirePHP/firePHP.php';
require_once 'class/gfDatePicker.class.php';

//Set the Debugging mode to True
Debug::setDebug(true);
?>  
<!DOCTYPE html>
<html>
    <head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="css/dr/jquery.ui.all.css" rel="stylesheet" />
	<link href="css/dr/demos.css" rel="stylesheet" />
	<link href="css/callback.css" rel="stylesheet" />
	<link href="css/hacks.css" rel="stylesheet" />
	<link href="css/bootstrap.css" rel="stylesheet" />	
	<link href="css/easypaginate.css" rel="stylesheet" />
	    
	<script>
	    //need this until php sets the value, Therefore need here
	    var dayRange = new Array();
	    var totalRec = new Array();//To hold all request for Callback
	    var ansRec = new Array();//To hold answered callback records
	</script>
    </head>
    <body>
	<?php
	$instanceId = 151; //instance of partner
	$numLink = 10; //number of link
	
	DatePicker::setNoOfDays(30);
	$datePicker = new DatePicker($fromDate, $toDate, $dateRangeSet);

	$cbStats = new CallBackStats($instanceId, $datePicker);
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
		    $infoMessage = "Displaying Callback Records From <strong>$ukFromDate</strong> to <strong>$ukToDate</strong>";
		    $cbStats->customStats($_GET['fromDate'], $_GET['toDate']);
		} else {		    
		    $infoMessage = "Displaying Callback Records From <strong>".$datePicker->getUkFromDate()."</strong> to <strong>".$datePicker->getUkToDate()."</strong>";
		    $cbStats->monthStats();
		}
	    } else {		
		$infoMessage = "Displaying Callback Records From <strong>".$datePicker->getUkFromDate()."</strong> to <strong>".$datePicker->getUkToDate()."</strong>";
		$cbStats->monthStats();
	    }

	    $adminQuotes = new AdminQuotes($instanceId, $datePicker);

	    //Check if Callback link has been clicked
	    if ((isset($_GET['quoteId']))) {
		$adminQuotes->updateCallBackStatus($_GET['quoteId']);
		if ($fromDate != "" && $toDate != "") {
		    $infoMessage = "Displaying Callback Records From <strong>$ukFromDate</strong> to <strong>$ukToDate</strong>";
		    $cbStats->customStats($_GET['fromDate'], $_GET['toDate']);
		} else {		    
		    $infoMessage = "Displaying Callback Records From <strong>".$datePicker->getUkFromDate()."</strong> to <strong>".$datePicker->getUkToDate()."</strong>";
		    $cbStats->monthStats();
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

	    $TotalCB = $adminQuotes->countTotCB();
	    $AnsCB = $adminQuotes->countAnsCB();
	    $UnAnsCB = $adminQuotes->countUnAnsCB();

	    if ((isset($_GET['quoteStatus']))) {
		$quoteStatus = $_GET['quoteStatus'];
		if ($quoteStatus == 0) {//Display UnAnswered CallBacks	   
		    $resultSet = $adminQuotes->viewPaginateCallBacks($inputNum, $numLink, '0');
		} else if ($quoteStatus == 1) {//Display Answered CallBacks	   
		    $resultSet = $adminQuotes->viewPaginateCallBacks($inputNum, $numLink, '1');
		} else if ($quoteStatus == 2) { // Total CallBacks
		    $resultSet = $adminQuotes->viewPaginateCallBacks($inputNum, $numLink, '2');
		}
	    } else { //Display Total Callbacks
		$resultSet = $adminQuotes->viewPaginateCallBacks($inputNum, $numLink, '2');
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
		<div class='alert-message info fade in clear' data-alert='alert'><a class='close' href='#'>&times;</a>
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
			<h3>Total Callbacks</h3>    	
			<p class="dashboard">
			    <span class="data">
				<a href="<?php echo $_SERVER['PHP_SELF'] . "?quoteStatus=2&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate()."&dateRangeSet=".$datePicker->getDateRangeSet().'"'; ?>" class="dashboardLink" id="totCB">
				    <?php echo $TotalCB ?>
				</a>
			    </span>
			</p>
		    </li>

		    <li>
			<h3>Answered Callbacks</h3>    	
			<p class="dashboard">
			    <span class="data">
				<a href="<?php echo $_SERVER['PHP_SELF'] . "?quoteStatus=1&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate()."&dateRangeSet=".$datePicker->getDateRangeSet().'"'; ?>" class="dashboardLink" id="ansCB">
				<?php echo $AnsCB ?>
				</a>
			    </span>
			</p>
		    </li>

		    <li>
			<h3>Unanswered Callbacks</h3>    	
			<p class="dashboard">
			    <span class="data">
				<a href="<?php echo $_SERVER['PHP_SELF'] . "?quoteStatus=0&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate()."&dateRangeSet=".$datePicker->getDateRangeSet().'"' ?>" class="dashboardLink" id="unAnsCB">
				    <?php echo $UnAnsCB ?>
				</a>
			    </span>
			</p>		  
		    </li>
		</ul>
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
				<input type="hidden" name="quoteStatus" id="quoteStatus" value="<?php echo $adminQuotes->getCbStatus();?>">				
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
			<th>Date: </th>
			<th>Name: </th>
			<th>Email: </th>
			<th>Phone No: </th>
			<th>Additional Request: </th>
			<th>Status</th>
		    </tr>
		    </thead>
		    <tbody>
			<?php					
			if ($resultSet) {			    
			    foreach ($resultSet as $r) {		
				$date = date('M.d.Y', $r[callBackDate])."<br /><span class='small unHighlight'>".date('G:i:s A', $r[callBackDate])."</span>";
				$status = "";
				if ($r[quoteStatus] == 0) {
				    $status = "<a href='".$_SERVER['PHP_SELF']."?quoteId=".$r[quoteId]."&page=".$adminQuotes->getPageNo()."&row_pp=".$adminQuotes->getRecordsPerPage().
					      "&quoteStatus=".$adminQuotes->getCbStatus()."&param1=valu1&param2=value2&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate().
					      "&dateRangeSet=".$datePicker->getDateRangeSet()."' class='btn danger'>Callback</a>";
				} else {
				    $status = "<a href='#' class='btn success disabled'>Answered</button>";
				}
			    echo "<tr><td>".$date."</td>
				    <td>".$r[userName]."</td>
				    <td>".$r[userEmail]."</td>
				    <td>".$r[userTel]."</td>
				    <td>".$r[quoteMessage]."</td>
				    <td>".$status."</td>
				  </tr>";
			    }			    
			} else {
			    echo "<div class='alert-message error fade in' data-alert='alert'><a class='close' href='#'>&times;</a>Records not available!</div>";
			}
			?>
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
	<script language="javascript" type="text/javascript" src="js/callbackStats.js"></script>
	<script language="javascript" type="text/javascript" src="js/adminCallBack.js"></script>	
    </body>
</html>
