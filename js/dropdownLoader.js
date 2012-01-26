/**To do 
 * 1. Add loadMonths
 * 2. Add year
 * 3. Select nearest hours for e.g if 10:15 then 10 should be selected
 */

function DropdownLoader(){ 
    this.optionElement = "";
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
   
    for (val in hoursList){
	if (hoursList[val] < 10){
	    //This won't work in IE because of character encoding 
	    //for e.g., < need be be represented as \u003C
	    //sltHours.innerHTML += '<option value = "'+hoursList[val]+'">0'+hoursList[val]+'</option>';
	    
	    //this.optionElement = document.createElement("option");
	    //this.optionElement.setAttribute("value", hoursList[val]);
	    //this.optionElement.innerHTML = "0" + hoursList[val];
	    //sltHours.appendChild(this.optionElement);
	    this.createOptionTag(sltHours, hoursList[val], "0");
	} else {	    
	    this.createOptionTag(sltHours, hoursList[val]);
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

    for (val in minutesList){
	if (minutesList[val] < 10){
	    this.createOptionTag(sltMinutesElement, minutesList[val], "0");
	} else {
	   this.createOptionTag(sltMinutesElement, minutesList[val]);
	}		
    }
}

/**
 * Create an option tag
 * <option value="0">00</option>
 * 
 * @param $elementId	   elementId of a select dropdown menu to be populated with options 
 * @param $listElement     Each Array Element to go inside option tag [for e.g. minute {45} or hours {22}]
 * @param $prefexVal	   String value to be added incase the optionElement received is single digit 
 */
DropdownLoader.prototype.createOptionTag = function($elementId, $listElement, $prefexVal){
    this.optionElement = document.createElement("option");
    this.optionElement.setAttribute("value", $listElement);
    if (typeof $prefexVal == "undefined"){ //if prefex not passed
	this.optionElement.innerHTML = $listElement;
    } else {
	this.optionElement.innerHTML = $prefexVal + $listElement;
    }    
    $elementId.appendChild(this.optionElement);
}