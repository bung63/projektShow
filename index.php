<?php
require_once('config.php'); 
require_once('db.php'); 
require_once('template.php');
require_once('functions.php');
require_once('user.php');


define('SAGEPAY_SDK_PATH', 'path');
require_once('./util.php');

session_start();

$sessStege1 = "";
$sessStege2 = "";
if ( isset($_SESSION['stage']) ) {
	$sessStege1 = $_SESSION['stage'];
}
$myCookieStage = "";
$myCookieSeason = "";
setSiteSessions();
//print_r($_SESSION);
if ( isset($_SESSION['stage']) ) {
	$sessStege2 = $_SESSION['stage'];
}

$variables = new Variables();
$login = new Login($variables);
$login->checkLogin();

//print_r($variables);

$funcs = new Functions($login,$words,$variables);

$loginTop = $login->getLogin();

$funcs->doActions();

$message = $login->error;



if ( $message == "" )
	$message = $funcs->error;
$top = $funcs->getTop();
//$entry = $funcs->getEntry();
$entry = "";
$menu = $funcs->getMenu();
$main = $funcs->getMain();
$rightColumn = $funcs->getRightColumn();
$bottom = $funcs->getBottom();
if ( $message == "" )
	$message = $funcs->error;

if ( $funcs->variables->stage == 'e' ) 
	$stageText = $funcs->season['stage1title'];			//EARLY_STAGES;
else
	$stageText = $funcs->season['stage2title'];			//FINAL_STAGES;


$head = str_replace("#JSHEAD#",$funcs->jsHead,$head);

$body = str_replace("#NATIONALFINALSCHEDULE#",$funcs->season['stage2schedule'],$body);
$body = str_replace("#JSBODY#",$funcs->jsBody,$body);
$body = str_replace("#LOGIN#",$loginTop,$body);
$body = str_replace("#TOP#",$top,$body);
$body = str_replace("#MENU#",$menu,$body);
$body = str_replace("#MESSAGE#",$message,$body);
$body = str_replace("#MENU#",$menu,$body);
$body = str_replace("#STAGETEXT#",$stageText,$body);
$body = str_replace("#MAIN#",$main,$body);
if ( $funcs->variables->view == "k" ) {
	$entry = "";
	$rightColumn = "";
	$mainNav = "divMainWide";
} else {
	$mainNav = "mainNav2";
	
}
$body = str_replace("#RIGHT#",$entry,$body);
$body = str_replace("#RIGHT2#",$rightColumn,$body);
$body = str_replace("#MAINDIV#",$mainNav,$body);

$body = str_replace("#FOOT#",$bottom,$body);

print $head;
include_once("analyticstracking.php");
print $head2;
print $body;
print "<!-- Stage1 = $sessStege1  Stage2 = $sessStege2 -->";
?>
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="closeModal" onclick="closeModal();">×</span>
    <p id="p1Modal"><?php print stripslashes($funcs->configTable['termshead']);?></p>
    <p id="p2Modal"><?php print nl2br(stripslashes($funcs->configTable['terms']));?></p>
  </div>
</div>
<?php  
print $foot;



?>