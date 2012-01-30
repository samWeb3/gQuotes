<?php

require_once 'gfCRUD.class.php';
require_once 'gfPagination.php';
require_once 'class/gfDatePicker.class.php';

class AdminQuotes {

    private $_crud;
    private $_datePicker;
    private $_instanceId;
    private $_quoteStatus;    
    private $_pager;   

    /**
     * 
     * @param type $instanceId		Instance Id of Partner
     * @param DatePicker $datePicker	datePicker
     */
    public function __construct($instanceId, DatePicker $datePicker) {
	if (empty($instanceId)) {
	    throw new Exception("Partner ID Not provided");
	}
	if (empty($datePicker)) {
	    throw new Exception("Date Object Not provided");
	}
	$this->_datePicker = $datePicker;	
	$this->_instanceId = $instanceId;
	$this->_crud = new CRUD();
    }

    /**
     * Populates table with Quotes records and paginates
     * 
     * @param int    $rowNum	    Number of rows per page
     * @param int    $numLink	    Number of links     
     * @param string $quoteStatus   Status of Quotes [Answered, Unanswered]
     */
    public function viewPaginateCallBacks($rowNum, $numLink, $quoteStatus="") {
	
	$this->_quoteStatus = $quoteStatus;

	if ($quoteStatus == "" || $quoteStatus == '2') {//Total CallBack	    
	    $sql = $this->callBackQuery($quoteStatus);
	} else if ($quoteStatus == '0' || $quoteStatus == '1') {//Answered or Unanswered CB	    
	    $sql = $this->callBackQuery($quoteStatus);
	}	
	$this->_pager = new PS_Pagination($this->_crud, $sql, $rowNum, $numLink, "&cbStatus=$quoteStatus&param1=valu1&param2=value2&fromDate=".$this->_datePicker->getFromDate()."&toDate=".$this->_datePicker->getToDate()."&dateRangeSet=".$this->_datePicker->getDateRangeSet().'"');
	
	//returns resultset or false
	$reqResultSet = $this->_pager->paginate();
	
	if (!$reqResultSet) {
	    return false;
	} else {
	    //Updates the Answered and Unanswered callbacks
	    $this->countAnsCB();
	    $this->countUnAnsCB();
	    
	    return $reqResultSet;	    
	}
    }

    /**
     * Construct the Query for retrieving Quotes result
     * 
     * @param type $quoteStatus	Total Quotes [" " || 2]; Answered [1], Unanswered [0]
     * @return string		SQL query
     */
    private function callBackQuery($quoteStatus="") {
	$sql = "SELECT gquoteuser.userId, userName, userEmail, userTel, 
		    quoteId, instanceId, vehicleId, departureLoc, destinationLoc, 
		    departureDate, returnDate, quoteMessage, quoteDate, quoteStatus
		FROM gquoterequest, gquoteuser
		WHERE gquoteuser.userId = gquoterequest.userId
		AND gquoterequest.instanceId = $this->_instanceId";
	if ($this->_datePicker->getUnixFromDate() != "" && $this->_datePicker->getUnixToDate() != "") {	    
	     $sql .= " AND quoteDate > ".$this->_datePicker->getUnixFromDate()." AND quoteDate < ". $this->_datePicker->getUnixToDate();
	}
	if ($quoteStatus == '0' || $quoteStatus == '1') {
	    $sql .= " AND quoteStatus = '$quoteStatus'";
	}
	$sql .= " ORDER BY gquoterequest.quoteDate DESC";
	if (Debug::getDebug()) {
	    fb($sql, "SQL: ", FirePHP::WARN);
	}
	return $sql;
    }
    
    public function getPaginatorNav(){
	return $this->_pager->renderFullNav();
    }

    /**
     * Updates the status of the gquoterequest table
     * 
     * @param string $quoteId Quote Id of the gquoterequest table
     */
    public function updateCallBackStatus($quoteId) {
	$this->_crud->dbUpdate('gquoterequest', 'quoteStatus', 1, 'quoteId', $quoteId);
    }

    /**
     * Displays Total Number of Answered Call Back
     * 
     * @return int Number of answered Call Back 
     */
    public function countAnsCB() {
	Fb::info("Answered:");
	$rs = $this->_crud->dbSelectFromTo('gquoterequest', $this->_instanceId, 'quoteStatus', '1', 'quoteDate', $this->_datePicker->getUnixFromDate(), $this->_datePicker->getUnixToDate());
	return count($rs);
    }

    /**
     * Displays Total Number of Unanswered Call Back
     * 
     * @return int Number of Unanswered Call Back
     */
    public function countUnAnsCB() {
	Fb::info("Un Answered:");
	$rs = $this->_crud->dbSelectFromTo('gquoterequest', $this->_instanceId, 'quoteStatus', '0', 'quoteDate', $this->_datePicker->getUnixFromDate(), $this->_datePicker->getUnixToDate());
	return count($rs);
    }

    /**
     * Displays Total Number of Call Back
     * 
     * @return int Number all Call Back
     */
    public function countTotCB() {
	Fb::info("Total Quotes");

	$rs = $this->_crud->dbSelectFromTo('gquoterequest', $this->_instanceId, null, null, 'quoteDate', $this->_datePicker->getUnixFromDate(), $this->_datePicker->getUnixToDate());
	return count($rs);
    }

    
    /********************************************************
     * Getters
     ********************************************************/
    public function getCbStatus(){
	return $this->_quoteStatus;
    }
    
    public function getPageNo(){
	return $this->_pager->getPage();
    }
    
    public function getRecordsPerPage(){
	return $this->_pager->getRowsPerPage();
    }
    

}

?>