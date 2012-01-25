/**To do 
 * 1. Add loadMonths
 * 2. Add year
 * 3. Select nearest hours for e.g if 10:15 then 10 should be selected
 */

function DropdownLoader(){    
    
    /**
     * @elementId <string>  elementId of a select dropdown menu to be populated with hours
     * @hoursList <array>   User Defined hoursList
     */
    DropdownLoader.prototype.loadHours = function(elementId, hoursList){
	//If array doesn't exist and if it is less than 1
	if (typeof hoursList == "undefined" || hoursList instanceof Array && hoursList.length < 1){ 	    
	    var hoursList = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"];
	}
	
	var sltHours = document.getElementById(elementId);
	
	//var selected = <?php if (isset($missing)){echo 'selected="selected"'};?>;
	
	for (val in hoursList){
	    if (hoursList[val] < 10){
		sltHours.innerHTML += '<option value = "'+hoursList[val]+'">0'+hoursList[val]+'</option>';
	    } else {
		sltHours.innerHTML += '<option value = "'+hoursList[val]+'">'+hoursList[val]+'</option>';
	    }		
	}
    }
    DropdownLoader.prototype.loadMinutes = function(elementId, minutesList){
	if (typeof minutesList == "undefined" || minutesList instanceof Array && minutesList.length < 1){ 
	    var minutesList = ["0", "15", "30", "45"];
	}
	
	var sltminutesList = document.getElementById(elementId);

	for (val in minutesList){
	    if (minutesList[val] < 10){
		sltminutesList.innerHTML += '<option value = "'+minutesList[val]+'">0'+minutesList[val]+'</option>';
	    } else {
		sltminutesList.innerHTML += '<option value = "'+minutesList[val]+'">'+minutesList[val]+'</option>';
	    }		
	}
    }
}

