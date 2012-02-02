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
     * Return all vehicles information belonging to instance
     * 
     * @param gfInstances $instance	Reference to instance object
     * @param type $instVeh		Array holding vehicles of Instances
     * @return type			Array holding vehicle of particular instance
     */    
    public function getInstanceVehicles(gfInstances $instance, $instVeh){
	$instId = $instance->getInstanceId();	
	if (array_key_exists($instId, $instVeh)){	    
	    return $instVeh[$instId];	    
	} else {
	    echo "Sorry $instId key doesn't exit in $instVeh array! <br>";
	}  
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
