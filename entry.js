
function loadJsonDoc(dropBox,thisVal,dropValue)
{

var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
		loadDrop(xmlhttp.responseText,dropBox,dropValue);
    }
  }

  var url = "./json.php?drop=" + dropBox + "&val=" + thisVal;

//   var url = "json.php?drop=" + drop + "&val=" + thisVal;

xmlhttp.open("GET",url,true);
xmlhttp.send();
}
function loadDrop (responseText,dropBox,dropValue) {
	jSonDrop = eval("(" + responseText + ')'); 

	document.getElementById(dropBox).length = 0;
	var option = document.createElement("option");
	option.text = "Select..";	
	document.getElementById(dropBox).add(option);
	for (var ii=0; ii < jSonDrop.length; ii++) {
		var option = document.createElement("option");
			
		option.text = jSonDrop[ii].val;	
		option.value = jSonDrop[ii].id;	
		
		if ( jSonDrop[ii].id == dropValue ) {	
		
			option.selected = true;
		}
		document.getElementById(dropBox).add(option);
	}
}

function showRSS(str) {
  if (str.length==0) { 
    document.getElementById("rssOutput").innerHTML="";
    return;
  }
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("rssOutput").innerHTML=xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET","getrss.php?q="+str,true);
  xmlhttp.send();
}
if ( DYN_WEB.Scroll_Div.isSupported() ) {
    
    DYN_WEB.Event.add( window, 'load', function() {
		var wndo = new DYN_WEB.Scroll_Div('wn', 'lyr');
		
		// see info online at http://www.dyn-web.com/code/scrollers/pausing/documentation.php
		var options = {
			axis:'h',
			bRepeat:true,
			repeatId:'rpt',
			dur:600, // duration of glide-scroll
			bPauseResume:true,
			distance: 242, // distance of glide-scroll
			pauseDelay: 3000, 
			resumeDelay: 300,
			startDelay: 1000
			};
		
		wndo.makePauseAuto( options );
		
	});
}
function sendMail() {
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		var respons = xmlhttp.responseText;
		document.getElementById("outStanding").innerHTML=respons + " Email(s) in queue";
		if ( respons != "0" )
			setTimeout(sendMail, sendMailTimer);
    }
  }
  xmlhttp.open("GET","mail.php",true);
  xmlhttp.send();
}
function searchUser(searchFor) {
	if ( searchFor.length > 3 ) {
	
		if (window.XMLHttpRequest) {
			xmlhttp=new XMLHttpRequest();
		} else {  
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				var respons = xmlhttp.responseText;
				document.getElementById("searchDiv").innerHTML=respons;
			}
		}
		var thisUrl = "search.php=?search=" + searchFor;
		alert(thisUrl);
		xmlhttp.open("GET",thisUrl,true);
		xmlhttp.send();
	}
}

function validateScore() {
	if (false) {
	for (var ii=0; ii < matches.length; ii++) {
	//	alert(matches[ii].game1);
		if ( document.getElementById('reset_' + matches[ii].id.toString()).checked ) { 
			document.getElementById('recordError_' + matches[ii].id.toString()).innerHTML = '';
	//		alert('ss');
		} else {  
			document.getElementById('recordError_' + matches[ii].id.toString()).innerHTML = 'Errror';
	//		alert('aa');
		}
	}
	}
	return true;
}
function updateScore(thisField,thisInputId,thisKey) {
	
	matches[thisKey][thisField]  = document.getElementById(thisInputId).value;
}

function changeLink(aLink,valueObject,variableName){
	var aObj = document.getElementById(aLink);
	var valueObj = document.getElementById(valueObject);
	if ( valueObj.value == '' ) {
		alert('Please select an event first.');
		return false;
	} else {
		aObj.setAttribute('href', aObj.href + '&' + variableName + '=' + valueObj.value);
		return true;
	}
	
}
function eventTotal() {
	//alert(eventIds);
	var eventIdsArray = eventIds.split(",");
	var eventSum= 0;
	for( i = 0; i < eventIdsArray.length; i++) {
		if ( document.getElementById('eventSelect_'+ eventIdsArray[i].toString()).checked ) {
			eventSum += Number(document.getElementById('eventSelectCost_'+ eventIdsArray[i].toString()).value);
		}
	}
	document.getElementById('eventTotal').innerHTML = "Total: £" + eventSum.toFixed(2).toString();
}
function eventEntrySubmit() {
	var errorMessage = "";
	var errorMessage2 = "";
	var errorMessage3 = "";
	var eventIdsArray = eventIds.split(",");
	var eventSelected= false;
	for( i = 0; i < eventIdsArray.length; i++) {
		if ( document.getElementById('eventSelect_'+ eventIdsArray[i].toString()).checked ) {
			eventSelected = true;
		}
	}
	if ( !eventSelected ) {
		errorMessage = "Please select at least one event";
	}
	for( i = 0; i < notEmptyArray.length; i++) {
		var fldName = notEmptyArray[i]["fld"];
		if ( document.getElementById(fldName).value == "" ) {
			var errorAddMessage2 = notEmptyArray[i]["description"];
			if  ( errorMessage2 == "" ) {
				errorMessage2 = errorAddMessage2
			} else {
				errorMessage2 = errorMessage2 + ", " + errorAddMessage2 ;
			}
		} else {
			if ( fldName == "preField_email" ) {
				var emailAddress = document.getElementById(fldName).value;
				var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
				if ( !filter.test(emailAddress)) {
					errorMessage3 = "Email address is Not valid ";
				}
			}
		}
	}
	if ( errorMessage2 != "" ) {
		errorMessage2 = "Fields that can not be empty: " + errorMessage2 + " ";
		errorMessage = errorMessage + "###" + errorMessage2;
	}
	if ( errorMessage3 != "" ) {	
		errorMessage = errorMessage + "###" + errorMessage3;
	}
	if ( errorMessage == "" ) {
		return true;
	} else {
		document.getElementById('evententererror').innerHTML = errorMessage.replace(/###/g,"<br>");
		alert(errorMessage.replace(/###/g,"\r"));
		return false;
	}
}
function eventEntrySubmit2() {
	var errorMessage = "";
	var errorMessage2 = "";
	var errorMessage3 = "";
	var eventIdsArray = eventIds.split(",");
	var eventSelected= false;
	for( i = 0; i < eventIdsArray.length; i++) {
		if ( document.getElementById('eventSelect_'+ eventIdsArray[i].toString()).checked ) {
			eventSelected = true;
		}
	}
	if ( !eventSelected ) {
		errorMessage = "Please select at least one event";
	}
	for( i = 0; i < notEmptyArray.length; i++) {
		var fldName = notEmptyArray[i]["fld"];
		if ( document.getElementById(fldName).value == "" ) {
			var errorAddMessage2 = notEmptyArray[i]["description"];
			if  ( errorMessage2 == "" ) {
				errorMessage2 = errorAddMessage2
			} else {
				errorMessage2 = errorMessage2 + ", " + errorAddMessage2 ;
			}
		} else {
			if ( fldName == "preField_email" ) {
				var emailAddress = document.getElementById(fldName).value;
				var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
				if ( !filter.test(emailAddress)) {
					errorMessage3 = "Email address is Not valid ";
				}
			}
		}
	}
	if ( errorMessage2 != "" ) {
		errorMessage2 = "Fields that can not be empty: " + errorMessage2 + " ";
		errorMessage = errorMessage + "###" + errorMessage2;
	}
	if ( errorMessage3 != "" ) {	
		errorMessage = errorMessage + "###" + errorMessage3;
	}
	
	if ( errorMessage == "" ) {
		if ( document.getElementById('acceptTerms').checked ) {
			return true;
		} else {
			alert("You must accept terms & conditions");
			return false;	
		}
	} else {
		document.getElementById('evententererror').innerHTML = errorMessage.replace(/###/g,"<br>");
		alert(errorMessage.replace(/###/g,"\r"));
		return false;
	}
}

var span = document.getElementsByClassName("closeModal")[0];
function closeModal() {
    document.getElementById('myModal').style.display = "none";
}
function openModal() {
	var modal = document.getElementById('myModal');

    modal.style.display = "block";
}
