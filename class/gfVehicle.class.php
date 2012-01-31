<?php

class Vehicle {
    private $_crud;
    private $_vehicleId;
    private $_vehicleName;
    
    public function __construct(CRUD $crud){
	$this->_crud = $crud;	
    }
    
    /**
     * Returns all the vehicle informations from database
     * 
     * @return type  Database Resource
     */
    public function selectAllVehicles(){
	return $this->_crud->dbSelect('gquotevehicle');
    }
    
    /**
     * Returns Vehicle Name of the given vehicle Id
     * 
     * @param type $vehicleId	Id of an Vehicle    
     * @return type		Database Resource
     */
    public function selectVehicleName($vehicleId){	
	$vehName = $this->_crud->dbSelect('gquotevehicle', 'vehicleId', $vehicleId);	
	return $vehName[0][vehicleName];
    }
    
    /*************************************************************
     * FUNCTIONS TO BE ADDED 
     *************************************************************/
    
    /**
     * Get all types of vehicles provided by the partner
     * 
     * @param type $instanceId	Instance Id of the partner
     */
    public function getInstanceVehicles($instanceId){}
    
    public function addVehicle($vehicleName){}
    
    public function deleteVehicle(){}
    
    /****************************************************
     *  Getter and Setter
     ****************************************************/
    public function getVehicleId(){
	return $this->_vehicleId;
    }
   
    public function setVehicleId($vehicleId){
	$this->_vehicleId = $vehicleId;
    }
    public function setVehicleName($vehicleName){
	$this->_vehicleName = $vehicleName;
    }
    
    
}
?>
