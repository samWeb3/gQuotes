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
    
    public function deleteLocation(){}
    
    public function getAllLocations(){
	return $this->_crud->dbSelect('gquotelocation');
    }
    
    public function getInstanceLocations($instanceId){}
}

?>
