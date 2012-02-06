/**
 * Bind departureLoc and destinationLoc <select> dropdown Menu
 * 
 * So, When location from one dropdown menu is selected, the same
 * is hidden in another 
 * 
 */

function BindDepDest(){
    this.aDestinationLoc;
    this.aDestinationLocText;
    this.aDepartureLoc;
    this.aDepartureLocText;
}

BindDepDest.prototype.getSaveDestDDOptions = function(){
    return this.saveDestDDOptions;
}

BindDepDest.prototype.getSaveDeprDDOptions = function(){
    return this.saveDeprDDOptions;
}

/**
 *
 * @bindObject <object>	    Object of the same class needed inside $("#departureLoc option:selected") 
 *			    and $("#destinationLoc option") function
 */
BindDepDest.prototype.onDepartureSelect = function(bindObject){   
    
    if (this.getADestinationLoc()){//if element exist      	
	$("#destinationLoc option").show();
    } 

    /*********************************************************************************
     * Note: 
     * 
     * ON $("#departureLoc option:selected") and $("#destinationLoc option")
     * 
     * Need object of BindDepDest [bindObject] received as parameter to call function
     * of the object: 
     * 
     * for e.g: bindObject.setADestinationLocText(getTextOfDestOpt);  
     *			    [instead of]
     *          this.setADestinationLocText(getTextOfDestOpt);    
     *      
     *********************************************************************************/
    
    $("#departureLoc option:selected").each(function(){
	var getDeprOptTxt = $(this).text();
	bindObject.setADepartureLocText(getDeprOptTxt);	
    });  
    
        //Iterate through every option item on destinationLoc
    $("#destinationLoc option").each(function(){
	var getDestOptTxt = $(this).text()
	//alert(getDestOptTxt);
	bindObject.setADestinationLocText(getDestOptTxt);
		    
	//$(this): is an option element
	var getDestOpt = $(this);
	bindObject.setADestinationLoc(getDestOpt);
	
	/*
	 * If selected departure Location is found in destination Location 
	 * HIDE IT	
	*/
	if (bindObject.getADepartureLocText() == bindObject.getADestinationLocText()){	    	    
	    //alert(bindObject.getADestinationLoc());
	    bindObject.getADestinationLoc().hide();
	}
		    
	//console.log("Destination Option: " + bindObject.getADestinationLoc());
    });   
}

BindDepDest.prototype.onDestinationSelect = function(bindObject){
   if (this.getADepartureLoc()){
	$("#departureLoc option").show();
   } 
   
   $("#destinationLoc option:selected").each(function(){
       var getDestOptTxt = $(this).text();
       bindObject.setADestinationLocText(getDestOptTxt);       
   });
   
   //Iterate through every option item on departureLoc
   $("#departureLoc option").each(function(){
       var getDeprOptTxt = $(this).text();
       bindObject.setADepartureLocText(getDeprOptTxt);
       
       var getDeprOpt = $(this);
       bindObject.setADepartureLoc(getDeprOpt);
       
       /*
	 * If selected destination Location is found in departure Location 
	 * HIDE IT	
	*/
	if (bindObject.getADestinationLocText() == bindObject.getADepartureLocText()){	   
	    bindObject.getADepartureLoc().hide();
	}
	
	//console.log("Departure Option: " + bindObject.getADepartureLoc());
       
   });
}

BindDepDest.prototype.getADestinationLocText = function(){
    return this.aDestinationLocText;
}

BindDepDest.prototype.getADepartureLocText = function(){
    return this.aDepartureLocText;
}

BindDepDest.prototype.getADestinationLoc = function(){
    return this.aDestinationLoc;
}

BindDepDest.prototype.getADepartureLoc = function(){
    return this.aDepartureLoc;
}


BindDepDest.prototype.setADepartureLocText = function(aDepartureLocText){
    this.aDepartureLocText = aDepartureLocText;
}

BindDepDest.prototype.setADestinationLocText = function(aDestinationLocText){
    this.aDestinationLocText = aDestinationLocText;
}

BindDepDest.prototype.setADepartureLoc = function(aDepartureLoc){
    this.aDepartureLoc = aDepartureLoc;
}

BindDepDest.prototype.setADestinationLoc = function(aDestinationLoc){
    this.aDestinationLoc = aDestinationLoc;
}