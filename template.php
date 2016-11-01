<?php

$head = "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n";
$head .= "<title>Bowls England - Competitions Portal </title>\n";
$head .= "<link href=\"eba.css\" rel=\"stylesheet\" type=\"text/css\">\n";
$head .= "<link href=\"bowls_england_style.css\" rel=\"stylesheet\" type=\"text/css\">\n";
$head .= "<script type=\"text/javascript\" src=\"dw_paus_scroller.js\"></script>\n";
$head .= "<script type=\"text/javascript\" src=\"entry.js\"></script>\n";
$head .= "<script type=\"text/javascript\" src=\"tinymce/tinymce.min.js\"></script>\n";

$head .= "<script type=\"text/javascript\" src=\"http://openjs.com/js/jsl.js\" ></script>\n";
$head .= "<script type=\"text/javascript\" src=\"http://openjs.com/common.js\"></script>\n";
$head .= "<script type=\"text/javascript\" src=\"calendar.js\"></script>\n";


$head .= "<script type=\"text/javascript\">var sendMailTimer=".MAILTIMER.";</script>\n";
$head .= "<script type=\"text/javascript\">	\n";
$head .= "#JSHEAD#\n";
$head .= "</script>\n";

$head2 = "</head>\n";

$foot = "</html>\n";
$body = "<body>";
$body .= "<div id=container>\n";

$body .= "<div id=topBlueBar2>";
$body .= "<div id=topLogo><a href=\"?\" ><img src=\"images/".TOPLOGO."\" width=70></a></div>";
$body .= "<div id=topText><p>".TOPTEXT."</p></div>";
$body .= "<div id=centerBlock2>";
$body .= "<div id=divLogin>";
$body .= "#LOGIN#";
$body .= "</div>";
$body .= "</div>";
$body .= "</div>";

$body .= "<div id=topNav>#TOP#</div>\n";
//$body .= "<div id=divLogin>#LOGIN#</div>\n";
$body .= "<div id=divMessage>#MESSAGE#</div>\n";
$body .= "<div id=divMenuParent><div id=divMenu>#MENU# <a class=scheduleA href=\"images/#NATIONALFINALSCHEDULE#\" target=_blank>National Championships Schedule</a><a class=regulationA href=\"".REGULATIONFILE."\" target=_blank>Regulations</a></div></div>\n";
$body .= "<div id=topBeNav><table width=100%><tr>";
$body .= "<td valign=top width=200px><div id=topBeNavLeft ><a id=topBeNavLeftLink href=\"https://www.bowlsengland.com/\" target=_blank>www.bowlsengland.com</a></div></td>";
$body .= "<td valign=top align=center><div id=topBeNavMiddle><p id=topBeNavText >#STAGETEXT#</p></div></td>";
$body .= "<td valign=top width=200px><div id=topBeNavRight ><a id=topBeNavLeftLink href=\"?stage=".DEFAULT_PAGE."&season=".DEFAULT_SEASON."\">Portal Home</a></div></td>";
$body .= "</tr></table></div>\n";
$body .= "<div id=#MAINDIV#>#MAIN#</div>\n";
$body .= "<div id=rightNav>";
$body .= "<div id=rightNavUser>#RIGHT#</div>\n";
$body .= "<div id=rightNavRest>#RIGHT2#</div>\n";
$body .= "</div>\n";

$body .= "<div id=divFoot >#FOOT#</div>\n";
$body .= "</div>\n";
$body .= "<script type=\"text/javascript\">	\n";
$body .= "#JSBODY#\n";
$body .= "</script>\n";
$body .= "</body>\n";
?>