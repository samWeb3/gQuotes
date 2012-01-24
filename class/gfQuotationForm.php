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
    //private $_instId;
    //private $this->_user->getName();
    //private $_userEmail;
    //private $_user->getTel();
    private $_quoteMessage;
    
    
    
    private $_departureLoc;
    private $_departureDate;
    private $_destinationLoc;
    private $_returnDate;

    public function __construct(Crud $crud, gfInstances $instances, User $user, Vehicle $vehicle, $departureLoc, $departureDate, $destinationLoc, $returnDate, $quoteMessage) {
	//$this->_userName = $userName;
	//$this->_userEmail = $userEmail;
	//$this->_userTel = $userTel;
	$this->_quoteMessage = $quoteMessage;
	
	$this->_departureLoc = $departureLoc;
	$this->_departureDate = $departureDate;
	$this->_destinationLoc = $destinationLoc;
	$this->_returnDate = $returnDate;

/*>>>>>>>>>>*/	
	$this->_crud = $crud;
	$this->_instance = $instances;
	$this->_user = $user;
	$this->_vehicle = $vehicle;
	
	//$this->setInstId();
	$this->addQuoteRequest();
	$this->sendEmail();
    }

    public function addQuoteRequest() {
	$dbRow = $this->getRow('gquoteuser', 'userEmail', $this->_user->getEmail());
	echo "user Id: ".$dbRow[userId]."<br />";
	//if email exist
	if ($dbRow[userEmail] == $this->_user->getEmail()) {
	    $this->updateExistingRecord($dbRow[userId], $dbRow[userTel]);
	} else {
	    $this->updateNewRecord();
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
		'departureDate'=>$this->_departureDate,
		'destinationLoc'=>$this->_destinationLoc,
		'returnDate'=>$this->_returnDate,
		'quoteMessage' => $this->_quoteMessage, 
		'quoteDate' => time()
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
     * 
     */
    private function updateNewRecord() {
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
		'departureDate'=>$this->_departureDate,
		'destinationLoc'=>$this->_destinationLoc,
		'returnDate'=>$this->_returnDate,
		'quoteMessage' => $this->_quoteMessage, 
		'quoteDate' => time()
	    )
	);
	
	
	/*$quoteRequest = array(
	    array('user_id' => $dbRow[user_id], 'instanceId' => $this->getInstId(), 'enquiry' => $this->_quoteMessage, 'callBackDate' => time())
	);*/

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
     * 
     */
    private function setInstId() {//don't need
	//$gfInt = new gfInstances();
	//$this->_instId = $gfInt->getInstanceId();
    }
    
    private function getInstId(){
	return $this->_instance->getInstanceId();
    }
    
    /**
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
     * 
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

}

?>

