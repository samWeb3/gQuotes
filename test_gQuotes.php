<?php
  
   require_once 'FirePHP/firePHP.php';
   require_once 'class/gfDebug.php';
   Debug::setDebug(true);
   
   require_once 'class/gfCRUD.class.php';
   require_once 'class/gfLocation.class.php';
   require_once 'class/gfInstances.class.php';
   require_once 'class/gfQuotationForm.php';
   require_once 'class/gfUser.class.php';
   
   
    $crud = new CRUD();
    $instance = new gfInstances();
    /*$location = new Location($crud);
    $rs = $location->getAllLocations();
    
    foreach($rs as $loc){	
	echo $loc[locationName]."<br>";	
    }*/
    
    $userName = 'Jabin';
    $userEmail = 'jabin@hotmail.com';
    $userTel = '07726535695';
    $user = new User($userName, $userEmail, $userTel);
    
    $vehicle = new Vehicle($crud);
    $vehicle->setVehicleId("6");
    
    $quotationForm = new QuotationForm($crud, $instance, $user, $vehicle, "2", time(), "4", time(), "Quotes needed for Jabin to book minibus");
	    
    
?>
