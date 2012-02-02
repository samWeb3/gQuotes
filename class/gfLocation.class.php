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
     * Return all Locations served by the partner [instance]
     * 
     * @param gfInstances $instance	Reference to instance object
     * @param type $instLoc		Array holding Locations of Instances
     * @return type			Array holding Locations of particular instance
     */    
    public function getInstanceLocations(gfInstances $instance, $instLoc){
	$instId = $instance->getInstanceId();	
	if (array_key_exists($instId, $instLoc)){	    
	    return $instLoc[$instId];	    
	} else {
	    echo "Sorry $instId key doesn't exit in $instLoc array! <br>";
	}  
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
   
    
    public function deleteLocation(){}
    
}

?>
