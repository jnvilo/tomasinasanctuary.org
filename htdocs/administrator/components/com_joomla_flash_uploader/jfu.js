/** XHConn - Simple XMLHTTP Interface - bfults@gmail.com - 2005-04-01        **
 ** Code licensed under Creative Commons Attribution-ShareAlike License      **
 ** http://creativecommons.org/licenses/by-sa/2.0/                           **/
var myConnB = null;

function JFUXHConn()
{
  var xmlhttp;  // Msxml2.XMLHTTP.4.0
  try { xmlhttp = new XMLHttpRequest(); }
  catch (e) { try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
  catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");}
  catch (e) { xmlhttp = false; }}}
  if (!xmlhttp) return null;
  this.connect = function(sURL, fnDone)
  {    
    if (!xmlhttp) return false;   
    try {
        xmlhttp.open("GET", sURL, true);
        xmlhttp.onreadystatechange = function(){
          if (xmlhttp.readyState == 4)
          {
          fnDone(xmlhttp);
          }};
      xmlhttp.send("");
    }
    catch(z) { return false; }
    return true;
  };
  return this;
}

function disableProfile(id) {
if (!myConnB) { myConnB = new JFUXHConn(); } // we reuse the XHC!
if (!myConnB) return;
var fnWhenDoneR = function (oXML) { disableProfileDone(id); };
myConnB.connect( "index2.php?option=com_joomla_flash_uploader&no_html=1&task=changeProfile&type=disable&profile="+id, fnWhenDoneR);
}

function enableProfile(id) {
if (!myConnB) { myConnB = new JFUXHConn(); } // we reuse the XHC!
if (!myConnB) return;
var fnWhenDoneR = function (oXML) { enableProfileDone(id); };
myConnB.connect( "index2.php?option=com_joomla_flash_uploader&no_html=1&task=changeProfile&type=enable&profile="+id, fnWhenDoneR);
}

function disableProfileDone(id) {
  var new_but = "<img onClick='enableProfile("+id+")' src='images/publish_x.png' border='0' />";
  document.getElementById("enableP" + id).innerHTML = new_but;
}

function enableProfileDone(id) {
  var new_but = "<img onClick='disableProfile("+id+")' src='images/tick.png' border='0' />";
  document.getElementById("enableP" + id).innerHTML = new_but;
}

function disableMaster(id) {
if (!myConnB) { myConnB = new JFUXHConn(); } // we reuse the XHC!
if (!myConnB) return;
var fnWhenDoneR = function (oXML) { disableMasterDone(id); };
myConnB.connect( "index2.php?option=com_joomla_flash_uploader&no_html=1&task=changeMaster&type=disable&profile="+id, fnWhenDoneR);
}

function enableMaster(id) {
if (!myConnB) { myConnB = new JFUXHConn(); } // we reuse the XHC!
if (!myConnB) return;
var fnWhenDoneR = function (oXML) { enableMasterDone(id); };
myConnB.connect( "index2.php?option=com_joomla_flash_uploader&no_html=1&task=changeMaster&type=enable&profile="+id, fnWhenDoneR);
}

function disableMasterDone(id) {
  var new_but = "<img onClick='enableMaster("+id+")' src='images/publish_x.png' border='0' />";
  document.getElementById("enableM" + id).innerHTML = new_but;
}

function enableMasterDone(id) {
  var new_but = "<img onClick='disableMaster("+id+")' src='images/tick.png' border='0' />";
  document.getElementById("enableM" + id).innerHTML = new_but;
}

function showform(my_form) {
  twg_hideSec('form1');
  twg_hideSec('form2');
  twg_hideSec('form3');
  twg_showSec(my_form);
}

function twg_hideSec(n) {
  if (document.getElementById(n).style.display == "none") {
	    return false;
  } else {
    document.getElementById(n).style.display = "none";
    document.getElementById(n+"h").className="form-block-menu";
    return true;
  }
return true;
}

function twg_showSec(n) {
  document.getElementById(n).style.display = "block";
  document.getElementById(n+"h").className="form-block-menu-sel";
}

function removeSpaces(element) {
  var text = element.value;
  // trim spaces before and after - needs 2 statements to get all spaces
  text = text.replace(/([\s]*\,)+/g, ',');
  text = text.replace(/(\,[\s]*)+/g, ',');
  // trim front and back
  text = text.replace(/^\s+|\s+$/g, '');
  element.value = text;
}

var test = false;
var lastTimeout;

function testFolder() {
if (test == false) {
test = true;
var folder = document.getElementById("folder").value;
if (!myConnB) { myConnB = new JFUXHConn(); } // we reuse the XHC!
if (!myConnB) return;
var fnWhenDoneR = function (oXML) { checkFolderDone(oXML.responseText); };
myConnB.connect( "index2.php?option=com_joomla_flash_uploader&no_html=1&task=testFolder&folder="+escape(folder), fnWhenDoneR);
} else {
// we wait a little bit and do the test 200ms later and clear any timeouts before
window.clearTimeout(lastTimeout);
lastTimeout = window.setTimeout("testFolder",200);
}
}

function checkFolderDone(ret) {
  if (ret == "JFU:OUTPUT:1"){
    document.getElementById("foldertestimage").src = 'images/tick.png';
  } else if (ret == "JFU:OUTPUT:2") {
    document.getElementById("foldertestimage").src = '../includes/js/ThemeOffice/warning.png';
  } else {
    document.getElementById("foldertestimage").src = 'images/publish_x.png';
  }
  
  test = false;
}
function isNumeral(str) {
	if (str == "") {
		return true;
	}
	if (str == "null") {
		return false;
	}
	var reg_exp = /[-]?[0-9]+$/;
	var value = "" + str.match(reg_exp);
	return (value == str);
}
