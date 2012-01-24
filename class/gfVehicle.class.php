<?php

class Vehicle {
    private $_crud;
    private $_vehicleId;
    private $_vehicleName;
    
    public function __construct(CRUD $crud){
	$this->_crud = $crud;	
    }
    
    public function addVehicle($vehicleName){
	
    }
    
    public function deleteVehicle(){}
    
    public function getAllVehicles(){
	return $this->_crud->dbSelect('gquotevehicle');
    }
    
    public function getInstanceVehicles($instanceId){}
    
    /****************************************************
     *  Getter and Setter
     ****************************************************/
    public function getVehicleId(){
	return $this->_vehicleId;
    }
    public function getVehicleName(){
	return $this->_vehicleName;
    }
    public function setVehicleId($vehicleId){
	$this->_vehicleId = $vehicleId;
    }
    public function setVehicleName($vehicleName){
	$this->_vehicleName = $vehicleName;
    }
}
?>
