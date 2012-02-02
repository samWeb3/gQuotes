<?php

//require_once 'gfDebug.php';
require_once 'gfCRUD.class.php';
require_once 'gfInstances.class.php';
require_once 'gfOwnersManage.class.php';
require_once 'gfEmailPostmark.class.php';
require_once 'gfUser.class.php';
require_once 'gfVehicle.class.php';

class QuotationForm {

    private $_crud;
    private $_instance;
    private $_user;
    private $_vehicle;   
    private $_quoteMessage;    
    private $_departureLoc;
    private $_departureDateUnix;
    private $_destinationLoc;
    private $_returnDateUnix;

    /**
     *
     * @param Crud $crud
     * @param gfInstances $instance
     * @param User $user
     * @param Vehicle $vehicle
     * @param type $departureLoc
     * @param type $departureDateUnix
     * @param type $destinationLoc
     * @param type $returnDateUnix
     * @param type $quoteMessage 
     */
    public function __construct(Crud $crud, gfInstances $instance, User $user, Vehicle $vehicle, $departureLoc, $departureDateUnix, $destinationLoc, $returnDateUnix, $quoteMessage) {
	
	$this->_quoteMessage = $quoteMessage;
	
	$this->_departureLoc = $departureLoc;
	$this->_departureDateUnix = $departureDateUnix;
	$this->_destinationLoc = $destinationLoc;
	$this->_returnDateUnix = $returnDateUnix;

	$this->_crud = $crud;
	$this->_instance = $instance;
	$this->_user = $user;
	$this->_vehicle = $vehicle;
	
	$this->addQuoteRequest();
	$this->sendEmail();
    }

    /**
     * Add new or Update existing records based on a valued submitted from the Quotation Form
     */
    public function addQuoteRequest() {
	$dbRow = $this->getRow('gquoteuser', 'userEmail', $this->_user->getEmail());
	
	//if email exist
	if ($dbRow[userEmail] == $this->_user->getEmail()) {
	    $this->updateExistingRecord($dbRow[userId], $dbRow[userTel]);
	} else {
	    $this->addNewRecord();
	}
    }
    
    /**
     * Unset the cookies after the form submission
     * Possible cookie name to be reset [print_r($_COOKIE)]:
     * "StickyForm_user_name"
     * "StickyForm_user_email"
     * "StickyForm_user_tel"
     * "StickyForm_departureLoc"
     * "StickyForm_destinationLoc"
     * "StickyForm_departureDate"
     * "StickyForm_sltHours"
     * "StickyForm_sltMinutes"
     * "StickyForm_returnDate"
     * "StickyForm_sltHoursRet"
     * "StickyForm_sltMinutesRet"
     * "StickyForm_vehicleType"
     * "StickyForm_quote_message"
     * 
     * @param type $cookiesArr	Cookie name(s) to be reset
     */
    public function unsetCookie($cookiesArr){	
	foreach ($cookiesArr as $key => $value)	{ 
	    //This unset cookie works only within the open browser. If browser is re-opened cookies is BACK AGAIN 
	    //Therefore, in sticky form [user_name, user_email, user_tel] is excluded from cookies as a security precaution. 	   
	    setcookie($value); 	
	}
    }
    
    /**
     * Reset the form field after the submission
     * This method have to be called after unsetCookie(), Else it won't reset for those values which are cached by sticky form
     * 
     * @param <array> $fieldnameArr    Fieldname(s) to be reset
     */
    public function resetForm($fieldnameArr){	
	foreach ($fieldnameArr as $key => $value){
	    unset($_POST[$value]);
	}
    }

    /**
     *
     * @param type $dbUserId
     * @param type $dbTel 
     */
    private function updateExistingRecord($dbUserId, $dbTel) { 
	if (Debug::getDebug()) {
	    Fb::info("QuotationForm: Email exist!");
	}

	if (Debug::getDebug()) {
	    $message = "QuotationForm: User Id is " . $dbUserId . " and Tel is: " . $dbTel;
	    Fb::info($message);
	}

	//use the id of existing user to insert into the $enquiry database
	$quoteRequest = array(
	    array(
		'userId' => $dbUserId, 
		'instanceId' => $this->getInstId(), 
		'vehicleId' => $this->_vehicle->getVehicleId(), 
		'departureLoc'=>$this->_departureLoc,
		'departureDate'=>$this->_departureDateUnix,
		'destinationLoc'=>$this->_destinationLoc,
		'returnDate'=>$this->_returnDateUnix,
		'quoteMessage' => $this->_quoteMessage, 
		'quoteDate' => time(), 
		'quoteStatus' => 0
	    )
	);

	$this->insertRow('gquoterequest', $quoteRequest);

	//if telephone retrived from database is not equal to one passed on the form
	if ($dbTel != $this->_user->getTel()) {
	    if (Debug::getDebug()) {
		Fb::warn("Need to update the database!");
		fb($dbUserId, "User ID");
		fb($this->_user->getTel(), "New Telephone");
		fb($dbTel, "Old Telephone");
	    }

	    $this->updateRow('gquoteuser', 'userTel', $this->_user->getTel(), 'userId', $dbUserId);
	} else {
	    if (Debug::getDebug()) {
		Fb::info("Telephone no is same");
	    }
	}
    }

    /**
     * Add new Record into a database
     * 
     */
    private function addNewRecord() {
	if (Debug::getDebug()) {
	    Fb::info("QuotationForm: Email doesn't exist:");
	}

	//Insert new user into the gquoteuser table
	$user = array(
	    array('userName' => $this->_user->getName(), 'userEmail' => $this->_user->getEmail(), 'userTel' => $this->_user->getTel(), 'userRegistered' => time())
	);
	
	$this->insertRow('gquoteuser', $user);

	//Get the user Id of the inserted user	
	$dbRow = $this->getRow('gquoteuser', 'userEmail', $this->_user->getEmail());
	
	//Used the retrieved user id to insert rest of info in enquiry table
	$quoteRequest = array(
	    array(
		'userId' => $dbRow[userId], 
		'instanceId' => $this->getInstId(), 
		'vehicleId' => $this->_vehicle->getVehicleId(), 
		'departureLoc'=>$this->_departureLoc,
		'departureDate'=>$this->_departureDateUnix,
		'destinationLoc'=>$this->_destinationLoc,
		'returnDate'=>$this->_returnDateUnix,
		'quoteMessage' => $this->_quoteMessage, 
		'quoteDate' => time(), 
		'quoteStatus' => 0
	    )
	);

	$this->insertRow('gquoterequest', $quoteRequest);
    }  
   
    /**
     * Insert ta row on a given table
     * 
     * @param type $table
     * @param type $values 
     */
    private function insertRow($table, $values) {
	$this->_crud->dbInsert($table, $values);
    }

    /**
     * Return row(s) of the given fieldname
     * 
     * @param string $table	    Tablename
     * @param string $fieldname	    Fieldname of table
     * @param string $id	    Value of a Fieldname
     * @return array		    Success or throw PDOExcepton on failure 
     */
    private function getRow($table, $fieldname, $id) {
	$dbRow = $this->_crud->dbSelect($table, $fieldname, $id);
	return $dbRow[0];
    }

    /**
     *
     * @param type $table
     * @param type $fieldname
     * @param type $value
     * @param type $pk
     * @param type $id 
     */
    private function updateRow($table, $fieldname, $value, $pk, $id) {
	$this->_crud->dbUpdate($table, $fieldname, $value, $pk, $id);
    }
         
    /**
     * Return email address of an instance
     * 
     * @return type 
     */
    private function getOwnerEmail() {
	$ow = new gfOwnersManage();
	$owEmail = $ow->getDetailsByInstance($this->getInstId());
	foreach ($owEmail as $oe) {
	    return $oe[0];
	}
    }

    /**
     * Sends an email to the instance owner
     */
    private function sendEmail() {
	$ow_email = $this->getOwnerEmail();

	if (Debug::getDebug()) {
	    FB::info("CallBackForm: Email to be sent to : $ow_email using gfEmailPostmark class with follwoing information: ");
	    $message = "Name: " . $this->_user->getName() . "<br /> Email: " . $this->_user->getEmail() . "<br /> Telephone: " . $this->_user->getTel() . "<br /> Enquiry: " . $this->_quoteMessage . "<br />";
	    FB::info($message);
	}
	/* $message = "Did you receive my message";
	  $email = new gfEmailPostmark();
	  $email->to($ow_email)->subject($subject)->messagePlain($message)->send(); */
    }
    
    /**************************************************
     * DELEGATION METHOD
     **************************************************/
    private function getInstId(){
	return $this->_instance->getInstanceId();
    }

}

?>

