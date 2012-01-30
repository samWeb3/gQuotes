<?php
include_once 'gfCRUD.class.php';
class Location {
    private $_crud;
    private $_locationName;
    
    public function __construct(CRUD $crud){
	$this->_crud = $crud;	
    }
    
    public function addLocation($locationName){
	$this->_locationName = $locationName;
    }
    public function getAllLocations(){
	return $this->_crud->dbSelect('gquotelocation');
    }
    
    public function getLocationName($locationId){	
	$locName = $this->_crud->dbSelect('gquotelocation', 'locationId', $locationId);	
	return $locName[0][locationName];
    }
    
    /*************************************************************
     * FUNCTIONS TO BE ADDED 
     *************************************************************/
    
    /**
     * Get all the Location served by the partner
     * 
     * @param type $instanceId	Instance Id of the partner
     */
    public function getInstanceLocations($instanceId){}
    
    public function deleteLocation(){}
    
    
}

?>
