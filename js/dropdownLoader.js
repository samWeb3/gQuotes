/**
 * This class is for loading minutes and hours value, 
 * 
 * This class populate the <select> HTML tag passed as a parameter
 * with the customed or predefined values of minutes or hours 
 * 
 * Also selects the option element based on the approximate time 
 * retrieved from the system
 * 
 * @ToDo: For month, day, year
 * 
 * @author Sambhu Raj Singh
 * @version 1.0.0 
 * 
 */

function DropdownLoader(){ 
    this.optionElement = "";
    this.currentTime = new Date();       
}
/**
 * @param <string> $elementId   elementId of a select dropdown menu to be populated with options
 * @param <array> $hoursList    User supplied hours array [01, 02, 03, 04 .... and so on]
 */
DropdownLoader.prototype.loadHours = function(elementId, hoursList){   
	
    //If array doesn't exist and if it is less than 1
    if (typeof hoursList == "undefined" || hoursList instanceof Array && hoursList.length < 1){ 	    
	var hoursList = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"];
    }
	
    var sltHours = document.getElementById(elementId);
    var $curHours = this.getHours();       
    for (val in hoursList){
	if (hoursList[val] < 10){
	    //This won't work in IE because of character encoding 
	    //for e.g., < need be be represented as \u003C
	    //sltHours.innerHTML += '<option value = "'+hoursList[val]+'">0'+hoursList[val]+'</option>';
	    
	    //this.optionElement = document.createElement("option");
	    //this.optionElement.setAttribute("value", hoursList[val]);
	    //this.optionElement.innerHTML = "0" + hoursList[val];
	    //sltHours.appendChild(this.optionElement);
	    this.createOptionTag(sltHours, hoursList[val], $curHours, "0");
	} else {	    
	    this.createOptionTag(sltHours, hoursList[val], $curHours);
	}		
    }
}

/**
 * @param <string> $elementId     elementId of a select dropdown menu to be populated with options
 * @param <array> $minutesList    User supplied minutes arraay [0, 1, 2, 3 ... 60]
 */
DropdownLoader.prototype.loadMinutes = function(elementId, minutesList){
    if (typeof minutesList == "undefined" || minutesList instanceof Array && minutesList.length < 1){ 
	var minutesList = ["0", "15", "30", "45"];
    }
	
    var sltMinutesElement = document.getElementById(elementId);
    var $quaterlyMinutes = this.getQuaterlyMinutes();        

    for (val in minutesList){
	if (minutesList[val] < 10){
	    this.createOptionTag(sltMinutesElement, minutesList[val], $quaterlyMinutes, "0");
	} else {
	   this.createOptionTag(sltMinutesElement, minutesList[val], $quaterlyMinutes);
	}		
    }
}

/**
 * Create an option tag
 * <option value="0">00</option>
 * 
 * @param $elementId	   elementId of a select dropdown menu to be populated with options 
 * @param $listElement     Each Array Element to go inside option tag [for e.g. minute {45} or hours {22}]
 * @param $curTime         Get the current time set on the computer
 * @param $prefexVal	   String value to be added incase the optionElement received is single digit 
 */
DropdownLoader.prototype.createOptionTag = function($elementId, $listElement, $curTime, $prefexVal){
    this.optionElement = document.createElement("option");
    this.optionElement.setAttribute("value", $listElement);
    /*
     * If current time is 14 and the value of $listElement equals to it
     * then that options will be set selected     
     */
    
    if ($curTime == $listElement){
	this.optionElement.setAttribute("selected", "selected");
    }
    if (typeof $prefexVal == "undefined"){ //if prefex not passed
	this.optionElement.innerHTML = $listElement;
    } else {
	this.optionElement.innerHTML = $prefexVal + $listElement;
    }    
    $elementId.appendChild(this.optionElement);
}

/**
 * Gets the current time from the system
 */
DropdownLoader.prototype.getHours = function(){
    return this.currentTime.getHours();
}

/**
 * Returns the Quaterly Minutes [0, 15, 30, 45]
 */
DropdownLoader.prototype.getQuaterlyMinutes = function(){
    $curMinutes = this.currentTime.getMinutes();    
    if ($curMinutes > 0 && $curMinutes <= 14){
	return quaterlyMinutes = 0;
    } else if ($curMinutes > 14 && $curMinutes <= 29){
	return quaterlyMinutes = 15;
    } else if ($curMinutes > 29 && $curMinutes <= 44){
	return quaterlyMinutes = 30;
    } else if ($curMinutes > 44 && $curMinutes <= 59){
	return quaterlyMinutes = 45;
    } else {
	return 0;
    }
}