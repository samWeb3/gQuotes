<?php
include_once 'gfCRUD.class.php';

class Location {
    private $_crud;
    private $_locationName;
    
    public function __construct(CRUD $crud){
	$this->_crud = $crud;	
    }
    
    /**
     * Return all the Location 
     * 
     * @return type	Database Resource
     */
    public function selectAllLocations(){
	return $this->_crud->dbSelect('gquotelocation');
    }
    
    /**
     *	Returns the Location Name 
     * 
     * @param type $locationId	Id of an location
     * @return type		Database Resource
     */
    public function selectLocationName($locationId){	
	$locName = $this->_crud->dbSelect('gquotelocation', 'locationId', $locationId);	
	return $locName[0][locationName];
    }
    
    /*************************************************************
     * Getters and Setters
     *************************************************************/
    public function setLocationName($locationName){
	$this->_locationName = $locationName;
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
