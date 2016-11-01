<?php
function setSiteSessions () {
	global $myCookieStage, $myCookieSeason;
	if ( isset($_GET["stage"]) ) {
		$myCookieStage = $_GET["stage"];
	} elseif ( isset($_COOKIE["stage"]) ) {
		$myCookieStage = $_COOKIE["stage"];
	} else {
		$myCookieStage = DEFAULT_PAGE;
	}
	if ( isset($_GET["season"]) ) {
		$myCookieSeason = $_GET["season"];
	} elseif ( isset($_COOKIE["season"]) ) {
		$myCookieSeason = $_COOKIE["season"];
	} else {
		$myCookieSeason = DEFAULT_SEASON;
	}
	
	setcookie("stage",$myCookieStage,time()+COOKIELIFE);
	setcookie("season",$myCookieSeason,time()+COOKIELIFE);
}
function getData($uid,$type,$var1,$var2,$var3) {
	$cache = "";
	switch ($type) {
		case "TEAMS":
			if ($var1) {
				$evening = $var1;
				if ($var2 == 'COUNT' ) {
					$query = "SELECT count(*) FROM webteams WHERE uid = '$uid' and tourcode = '$evening'  ";
				} else {
					$query = "SELECT * FROM webteams WHERE uid = '$uid' and tourcode = '$evening' order by lname,fname ";
				}
			} else {
				$query = "SELECT * FROM webteams WHERE uid = '$uid' order by lname,fname ";
			}
			$index = "";
		//	$cache = "TEAMS";
			break;
		case "LEAGS":
			$evening = $var1;
			if ($var2 == 'COUNT' ) {
				$query = "SELECT count(*) FROM webleag WHERE uid = '$uid' and tourcode = '$evening' ";
			} else {
				$query = "SELECT * FROM webleag WHERE uid = '$uid' and tourcode = '$evening' order by orde ";
			}
			break;
		case "CLUBS":
			$evening = $var1;
			if ($var2 == 'COUNT' ) {
				$query = "SELECT count(*) FROM webclubs WHERE uid = '$uid'  ";
			} else {
				$query = "SELECT * FROM webclubs WHERE uid = '$uid'  ";
			}
			break;
		case "RES":
			$evening = $var1;
			if ($var2 == 'COUNT' ) {
				if ($var3 == 'UPDATED' ) {
					$query = "SELECT count(*) FROM webres WHERE uid = '$uid' and tourcode = '$evening' and fixres = 'F' and updated = 'W' ";
				} elseif ($var3 == 'FIX' ) {
					$query = "SELECT count(*) FROM webres WHERE uid = '$uid' and tourcode = '$evening' and fixres = 'F' ";
				} else {
					$query = "SELECT count(*) FROM webres WHERE uid = '$uid' and tourcode = '$evening' and fixres = 'R'  ";
				}
			} else {
				if ($var3 == 'UPDATED' ) {
					$query = "SELECT * FROM webres WHERE uid = '$uid' and tourcode = '$evening' and fixres = 'F' and updated = 'W' ";
				} else {
					$query = "SELECT * FROM webres WHERE uid = '$uid' and tourcode = '$evening' ";
				}
			}
			break;
		case "TOURS":
			if ($var1) {
				$evening = $var1;
				$query = "SELECT id,code,name,sdate,size FROM webtour WHERE uid = '$uid' and code = '$evening' order by sdate desc LIMIT 50";
			} else {
				$query = "SELECT id,code,name,sdate,size FROM webtour WHERE uid = '$uid' order by sdate desc LIMIT 50";
			}
		
			$index = "code";
			break;
		case "TOURENTRY":
			$evening = $var1;
			
			$query = "SELECT playcode FROM webentry WHERE uid = '$uid' and tourcode = '$evening'";
			$index = "";
			break;
	}		
	//print $query;	
	if ( false ) {
	//if ( file_exists("cache/".$cache."_cache.php") ) {
		$jsonZ = file_get_contents('http://www.example.com/');
	} elseif ( !isset($query) )  {
		$jsonZ = "";
	} else {
		$thisDB = new Database();
		if ( $query == "" )
			$query = "SELECT 1";
	//	$this->db->setIndex($index);
		$thisDB->cache = $cache;
		$thisDB->query($query);
		if ($var2 == 'COUNT' ) {
			$thisDB->resultValue();
			$jsonZ = $thisDB->value;
			
		} else {
			$thisDB->result();
			if ( $type == "TOURENTRY" ) {
				$thisDB2 = new Database();
				$query2 = "SELECT code,fname,lname FROM webteams WHERE uid = '$uid' ";
				$index = "code";
		
				$thisDB2->setIndex($index);
				$thisDB2->query($query2);
				$thisDB2->result();

				foreach ($this->db->rows as $key => $value) {
					$this->db->rows[$key]['fname'] = $thisDB2->rows[$value['playcode']]['fname'];
					$this->db->rows[$key]['lname'] = $thisDB2->rows[$value['playcode']]['lname'];
				}
				$thisDB2->close();
			}
			$thisDB->makeJson();
			$jsonZ = $thisDB->Json;
		}
		
	}

	return $jsonZ;
	
}
class Variables {
	var $page;			// 
	var $v1;			// 
	var $v2;			// 
	var $v3;			// 
	var $view;			// 
	var $action;		// 
	var $posts;			//
	var $gets;			//
	var $tour;
	var $div;
	var $team;
	
	public $stage;	
	public $season;		
	public function __construct(){
		global $myCookieStage, $myCookieSeason;
		$this->posts = $_POST;
		$this->gets = $_GET;
		$this->stage = $myCookieStage;
		$this->season = $myCookieSeason;
		$this->setVari('page','p');
		$this->setVari('v1','v1');
		$this->setVari('v2','v2');
		$this->setVari('v3','v3');
		$this->setVari('view','v');
		$this->setVari('action','a');
		if ( $this->page == 'e' or $this->page == 'd' or $this->page == 't' ) 
			$this->tour = $this->v1;
		if ( $this->page == 'd'  ) {
			$this->div = $this->v2;
			if ( !$this->view )
				$this->view = 'k';
		}
		if ( $this->page == 't'  ) {
			$this->div = $this->v2;
			$this->team = $this->v3;
		}
	}
	function setVari($vari,$index) {
		if ( isset( $this->posts[$index] ) )
			$this->$vari = $this->posts[$index];
		elseif ( isset( $this->gets[$index] ) )
			$this->$vari = $this->gets[$index];
		else 
			$this->$vari = false;
	}
}
class Functions { 
	private $login;
	public $words;
	public $variables;
	public $jsHead;
	public $jsBody;
	private $imagesFolder = "images/";
	private $footerImage = "default.jpg";
	public $mainAccount;
	private $db;
	private $challanger = " [Challenger] ";
	private $home = " [Home] ";
	private $away = " [Away] ";
	private $teams = array();
	private $team;
	private $orderedTeams = array();
	private $division = array();
	private $divisions = array();
	private $event = array();
	private $entries = array();
	private $fixtures = array();
	private $points = array();
	//private $upkey = " &#8593; ";
	private $upkey = " <font color=red><b>&#8657;</b></font> ";
	private $downkey = " <font color=red><b>&#8659;</b></font> ";			//" &#8595; ";
	private $halfPoint = " <b>&#189;</b>";
	private $myTime;
	private $myDateFormat1 = "jS M Y";
	private $NonConfirmed = "-";
	private $Confirmed = "X";
	private $poundSign = "£";
	private $statusArray = array(0=>'',1=>'Initiated',2=>'Payed',3=>'Failed',4=>'Aborted');
	public $season;
	public $error = "";
	public $configTable = array();
	private $sideLinks = "";
	public function __construct($login,$words,$variables){
		$this->login = $login;
		$this->words = $words;
		$this->variables = $variables;
		if ( $this->login->type == 'PLA' and !strpos( $this->login->mess ,'e') ) {
//			$this->variables->page = "";
		} else {
			
		}
		$this->myTime = gmdate("Y/m/d H:i:s",time()+date("Z")+date("I"));
		$this->db = new Database();
		$this->loadConfig();
		$this->mainAccount = $this->getMainAccount();
		if ( $this->variables->page == 'e' ) {
			$this->getDivisions($this->variables->v1);
			if ( count($this->divisions) == 1 ) {
				$this->variables->v2 = $this->divisions[0]['code'];
				$this->variables->page = 'd';
			}
		}
		if ( $this->variables->page == 'd' ) {
			$this->getTeams($this->variables->v1,$this->variables->v2);
			$this->getEvent($this->variables->v1,$this->variables->v2);
			$this->getDiv($this->variables->v1,$this->variables->v2);
			$this->getEntries($this->variables->v1,$this->variables->v2);
			$this->getResults($this->variables->v1,$this->variables->v2);
		}
		global $myCookieStage, $myCookieSeason;
		$this->season = $this->getSeason($myCookieSeason);
	}
	public function __destruct() {
		$this->db->close();
	}
	function loadConfig() {
		$query = "SELECT * FROM config WHERE uid = '".UID."'";
		$this->db->query($query);
		$this->db->resultRecord();
		foreach ($this->db->row as $key => $val ) {
			$this->configTable[$key] = $val;
		} 
	}
	function headerForward($url) {
		header("Location: ".$url);
	}
	function encrpt($decrpt){
		$encrypt = base64_encode( ENTRYLINKKEY.$decrpt); 
		return $encrypt;
	}
	function decrpt($encrpt){
		$decrpt = substr(base64_decode( $encrpt),strlen(ENTRYLINKKEY)); 
		return $decrpt;
	}
	// Actions
	function doActions () {
		if ( isset($this->variables->posts['resSubmit']) ) {
			$this->updateRes();
		}
		if ( isset($this->variables->posts['submitTour']) ) {
			$this->updateTour();
		}	
		if ( isset($this->variables->posts['submitAdminAccount']) ) {
			$this->updateAccount();
		}	
		if ( isset($this->variables->posts['submitAccount']) ) {
			$this->login->updatePassword();
		}
		if ( isset($this->variables->posts['submitForgot']) ) {
			if ( isset($this->variables->posts['username']) and filter_var($this->variables->posts['username'], FILTER_VALIDATE_EMAIL) )
				$this->updateForgotPassword();
			else {
				$this->error = "You need to enter a valid email Address";
				$this->variables->page = 'Forgot';
			}
		}
		if ( isset($this->variables->posts['submitNewPw']) ) {
			$this->updateForgotNewPassword();
		}
		if ( isset($_GET['enter']) ) {
			enter($_GET['enter'],$_GET['div']);
		}		
		if ( isset($this->variables->posts['submitEntry']) ) {
			$this->updateCountyEntries();
		}
		if ( isset($this->variables->posts['submitAdminDownloadEntries']) ) {
			$this->downloadCountyEntries(1);
		}	
		if ( isset($this->variables->posts['submitAdminDownloadEntries2']) ) {
			$this->downloadCountyEntries(2);
		}	
		if ( isset($this->variables->posts['submitUserTeams2']) ) {
			$this->preEnterUserTeams2();
		}
		if ( isset($this->variables->posts['submitEnterCommon']) ) {
			$this->preEnterUserTeams2();
		}
		if ( isset($this->variables->posts['eventEntryFieldsSubmit']) ) {
			$this->preEnterFieldsUpdate();
		}
		if ( isset($this->variables->posts['preSetConfig']) ) {
			$this->preSetConfig();
		}
		if ( isset($this->variables->posts['submitUserTeams']) ) {
			$this->preEnterUserTeams();
			if ($this->variables->page == 'Account' )
				header('Location: ./index.php?p=Account&v1=pre&v2=view&ev='.$this->variables->v3);
		}
		if ( isset($this->variables->posts['submitConfirmEntries']) ) {
			$this->preEnterConfirmEntries();
		}
		if ( isset($this->variables->posts['submitDownloadEntries']) ) {
			$this->preEnterDownloadEntries();
		}
		if ( isset($this->variables->posts['submitPayEntries']) ) {
			$this->preEnterPayGather();
		}
	}
	
	
	// Account
	function getMainAccount() {
		$query = "SELECT * FROM syst WHERE code = '".UID."'";
		$this->db->query($query);
		$this->db->resultRecord();
		return $this->db->row;
		
	}
	function getAccount() {
		$name = $this->login->getVariable('name');
		$email = $this->login->getVariable('email');
		$html = "";
		
		$html .= "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" class=formClass  >";
		$html .= "<table>";
		$html .= "<tr><td>Name</td>";
		$html .= "<td>$name</td>";
		$html .= "</tr>";
		$html .= "<tr><td>Email</td>";
		$html .= "<td>$email</td>";
		$html .= "</tr>";

		$html .= "<tr><td>New Password</td><td>";
		$html .= "<input class=\"inputBox\" id=\"newPassword1\" name=\"newPassword1\" value=\"\" type=\"text\">";
		$html .= "</td></tr>";
		$html .= "<tr><td>Repeat New Password</td><td>";
		$html .= "<input class=\"inputBox\"  id=\"newPassword2\" name=\"newPassword2\" value=\"\" type=\"text\">";
		$html .= "</td></tr>";
		$html .= "<tr><td>Old Password</td><td>";
		$html .= "<input class=\"inputBox\" id=\"oldPassword\" name=\"oldPassword\" value=\"\" placeholder=\"**********\" type=\"password\">";
		$html .= "</td></tr>";

		
		
		$html .= "<tr><td colspan=2 align=right><input class=\"redSubmitBtn\" name=\"submitAccount\" type=\"submit\" value=\" Submit \"></td></tr>";
		
		$html .= "</table>";
		$html .= "</form>";
		return $html;	
	}	
	
	
	// Usefulls
	function getEditor ($editorClass) {
		$editor = "<script type=\"text/javascript\">";
		$editor .= "
		tinymce.init({
			selector: \"textarea.$editorClass\",
			height : 380,
			width : 600
		});";
		$editor .= "function saveEditor () {
			tinyMCE.triggerSave();	
		}";
		$editor .= "</script>";
		return $editor;
	}
	function searchUsers($search) {
		$query = "SELECT id,name,email FROM passw WHERE profile = '".UID."' and ( locate('$search',name) or locate('$search',email)) order by name";
		$this->db->query($query);
		$this->db->result();
		$users = $this->db->rows;
		$tbl = "<table id=entries>";
		foreach($users as $key => $user ) {
			$name = $user['name'];
			$name = "<A href=?p=Account&v1=u2&v2=".$user['id']." class=entryNameA>$name</a>";
			$email = $user['email'];
			$tbl .= "<tr>";
			$tbl .= "<td class=class=entryName>".$name."</td><td class=class=entryName>".$email."</td>";
			$tbl .= "</tr>";
		}
		$tbl .= "</table>";
		return $tbl;
	}
	// GetData 
	
	// Get Page Divs
	function getTourMenu() {
		$htmlMenu = "";
		//	$entries = "<a href=\"?view=e\" class=aTour><p class=pTour>".EARLY_STAGES."</p></a>";
		//	$account = "<a href=\"?view=f\" class=aTour><p class=pTour>".FINAL_STAGES."</p></a>";
		$thisStages = "";
		if ( $this->variables->stage  == 'f' and !$this->season['stage2'] ) {
			
			setcookie("stage",'e',time()+COOKIELIFE);
			$this->variables->stage = 'e';
			
		} else {
			if ( $this->variables->stage == 'e' ) {
				if ( $this->season['stage2'] ) 
					$thisStages = "<li class=liTour><a href=\"?stage=f\" class=aTour><p class=pTour>Click here to view Final Stages</p></a></li>";	
				else 
					$thisStages = "";
			} else {
				$thisStages = "<li class=liTour><a href=\"?stage=e\" class=aTour><p class=pTour>Click here to view Early Rounds</p></a></li>";
			}
			
		}
		if ( $thisStages ) {
			$htmlMenu .= "<div id=tourMenu>";
			$htmlMenu .= "<ul class=ulTour>";
			$htmlMenu .= $thisStages;
			$htmlMenu .= "</ul></div>";
		}
		return $htmlMenu;
		
	}	
	function getAccountCountyMenu () {
		$entries = "<a href=\"?p=Account&v1=e\" class=aAdmin>Entries </a>";
		$account = "<a href=\"?p=Account&v1=a\" class=aAdmin>Account </a>";
		$htmlMenu = "";
		$htmlMenu .= "<div id=adminMenu>";
		$htmlMenu .= "<ul class=ulAdmin>";
		$htmlMenu .= "<li class=liAdmin>$entries</li>";
		$htmlMenu .= "<li class=liAdmin>$account</li>";
		$htmlMenu .= "</ul></div>";
		return $htmlMenu;	}
	function getAccountAdminMenu($user) {
		$rightText = "<a href=\"?p=Account&v1=t\" class=aAdmin>Text Right </a>";
		$rightPics = "<a href=\"?p=Account&v1=p\" class=aAdmin>Pictures Right</a>";
		$rightSponsor = "<a href=\"?p=Account&v1=s\" class=aAdmin>Sponsors</a>";
		$rightMailing = "<a href=\"?p=Account&v1=m\" class=aAdmin>Add Email</a>";
		$rightMailing2 = "<a href=\"?p=Account&v1=m2\" class=aAdmin>Send Email</a>";
		$rightMailing3 = "<a href=\"?p=Account&v1=m3\" class=aAdmin>TE 1</a>";
		$rightSearch = "<a href=\"?p=Account&v1=u\" class=aAdmin>Search</a>";
		$pdfEvent = "<a href=\"?p=Account&v1=f\" class=aAdmin>Pdf ".$this->words['events']."</a>";
		$entriesDownload = "<a href=\"?p=Account&v1=d\" class=aAdmin>Entries</a>";
		$setupEvent = "<a href=\"?p=Account&v1=pre\" class=aAdmin>Setup ".$this->words['events']."</a>";
		$htmlMenu = "";
		$htmlMenu .= "<div id=adminMenu>";
		$htmlMenu .= "<ul class=ulAdmin>";
		$htmlMenu .= "<li class=liAdmin>$rightText</li>";
		$htmlMenu .= "<li class=liAdmin>$rightPics</li>";
		$htmlMenu .= "<li class=liAdmin>$rightSponsor</li>";
		$htmlMenu .= "<li class=liAdmin>$pdfEvent</li>";
		if ( $user == 'bengt' ) {
			$htmlMenu .= "<li class=liAdmin>$rightMailing</li>";
			$htmlMenu .= "<li class=liAdmin>$rightMailing2</li>";
		//	$htmlMenu .= "<li class=liAdmin>$rightMailing3</li>";
		}
		$htmlMenu .= "<li class=liAdmin>$setupEvent</li>";
		$htmlMenu .= "<li class=liAdmin>$entriesDownload</li>";
		$htmlMenu .= "<li class=liAdmin>$rightSearch</li>";
		
		$htmlMenu .= "</ul></div>";
		return $htmlMenu;
	}
	function getEntryEvents($eventcode='') {
		if ($eventcode)
			$query = "SELECT * FROM webevent WHERE uid = '".UID."' and season = '".DEFAULT_SEASON."' and code = '$eventcode' ";
		else 
			$query = "SELECT * FROM webevent WHERE uid = '".UID."' and season = '".DEFAULT_SEASON."' ORDER BY orde";
		
		$this->db->query($query);
		if ($eventcode) {
			$this->db->resultRecord();
			$webEvents = $this->db->row;
		} else {
			$this->db->result();
			$webEvents = $this->db->rows;
		}
		
		return $webEvents;
	}
	function getCountyEntry($eventcode,$abc) {
		$query = "SELECT * FROM wentries WHERE uid = '".UID."' and season = '".DEFAULT_SEASON."' and eventcode = '$eventcode' and user = '".$this->login->getVariable('name')."' and abc = '$abc' ";



		$this->db->query($query);
		$this->db->resultRecord();
		$countyEntry = $this->db->row;
		return $countyEntry;
	}
	function getEventEntry($eventcode) {
		$query = "SELECT * FROM wentries WHERE uid = '".UID."' and season = '".DEFAULT_SEASON."' and eventcode = '$eventcode' and club != '' and contactname != '' order by user,abc";
		$this->db->query($query);
		$this->db->result();
		$countyEntry = $this->db->rows;
		return $countyEntry;
	}
	function getCountyEntries() {
		$webEvents = $this->getEntryEvents();
		$countyEntryName = "";
		if ( isset($this->variables->posts['countyEntry']) ) 
			$countyEntry = trim($this->variables->posts['countyEntry']);
		else {
			if ( isset($webEvents[0]) )
				$countyEntry = trim($webEvents[0]['code']);
			else 
				$countyEntry = "";
		}
		$user = $this->login->getVariable('name');
		$abcArray = $this->getCentries($countyEntry,$user);
		$html = "<form id=countyEntryForm action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" class=formClass >";
		$html .= "<table><tr><td>Event</td>";
		$html .= "<td><select class=inputBox  name=countyEntry onchange=\"document.getElementById('countyEntryForm').submit();\"><option value=''>Please select event</option>";
		foreach ($webEvents as $key => $webEvent) {
			$optionName = trim($webEvent['name']);
			$optionCode = trim($webEvent['code']);
			$selected = "";
			if ( $optionCode == $countyEntry) {
				$selected = "selected";
				$countyEntryName = $optionName;
			}
			
			$html .= "<option value=$optionCode $selected>$optionName</option>";
		}
		$html .= "</select></td></tr>";
		$html .= "</table></form>";
		$html .= "<form id=countyEntryFormData action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" class=formClass><table>";
		$html .= "";
		$html .= "<tr><td>County</td><td>".$this->login->getVariable('name')."</td></tr>";
		$clubs = $this->getClubs($this->login->getVariable('name'));
		
		if ( in_array("A",$abcArray) ) {
			$eventEntryA = $this->getCountyEntry($countyEntry,'A');
			$html .= $this->getCountyEntriesABC($eventEntryA,'A',$clubs,$countyEntryName);
		}
		if ( in_array("B",$abcArray) ) {
			$eventEntryB = $this->getCountyEntry($countyEntry,'B');
			$html .= $this->getCountyEntriesABC($eventEntryB,'B',$clubs,$countyEntryName);
		}
		if ( in_array("C",$abcArray) ) {
			$eventEntryC = $this->getCountyEntry($countyEntry,'C');
			$html .= $this->getCountyEntriesABC($eventEntryC,'C',$clubs,$countyEntryName);
		}
		$html .= "<tr><td colspan=3 align=center><input type=submit name=submitEntry value=Submit></td></tr>";
		$html .= "</tr>";
		$html .= "</table><input type=hidden name=countyEntry value='".$countyEntry."'></form>";
		return $html;
	}
	function getCountyEntriesABC($eventEntry,$abc,$clubs,$eventEntryName) {
		$html2 = "<tr><td colspan=2 class=entriesABC>$abc</td></tr>";
		$html2 .= "<tr><td>Club</td><td><select class=inputBox  name=club_$abc><option ></option>";
		foreach ($clubs as $key => $club) {
			$optionName = trim($club['name']);
			$optionCode = trim($club['clubid']);
			$selected = "";
			if ( $optionCode == $eventEntry['club'])
				$selected = "selected";
			
			$html2 .= "<option value=$optionCode $selected>$optionName</option>";
		}
		$html2 .= "</select></td></tr>";
		$html2 .= "<tr><td>Contact</td><td><input class=inputBox name=contactname_$abc value=\"".$eventEntry['contactname']."\"></td></tr>";
		$html2 .= "<tr><td>Property</td><td><input class=inputBox name=property_$abc value=\"".$eventEntry['property']."\"></td></tr>";
		$html2 .= "<tr><td>Street</td><td><input class=inputBox name=street_$abc value=\"".$eventEntry['street']."\"></td></tr>";
		$html2 .= "<tr><td>Locality</td><td><input class=inputBox name=locality_$abc value=\"".$eventEntry['locality']."\"></td></tr>";
		$html2 .= "<tr><td>Town</td><td><input class=inputBox name=town_$abc value=\"".$eventEntry['town']."\"></td></tr>";
		$html2 .= "<tr><td>County</td><td><input class=inputBox name=county_$abc value=\"".$eventEntry['county']."\"></td></tr>";
		$html2 .= "<tr><td>Postcode</td><td><input class=inputBox name=postcode_$abc value='".$eventEntry['postcode']."'></td></tr>";
		$html2 .= "<tr><td>Mobile Phone</td><td><input class=inputBox name=contactmobile_$abc value='".$eventEntry['contactmobile']."'></td></tr>";
		$html2 .= "<tr><td>Landline Phone</td><td><input class=inputBox name=contactphone_$abc value='".$eventEntry['contactphone']."'></td></tr>";
		$html2 .= "<tr><td>Email</td><td><input class=inputBox name=contactemail_$abc value='".$eventEntry['contactemail']."'></td></tr>";
		if ( strpos(' '.$eventEntryName,'Singles') ) {
			$html2 .= "<tr><td>Player</td><td><input class=inputBox name=teammember1_$abc value='".$eventEntry['teammember1']."'></td></tr>";
			$html2 .= "<tr><td></td><td><input type=hidden name=teammember2_$abc value=''></td></tr>";
			$html2 .= "<tr><td></td><td><input type=hidden name=teammember3_$abc value=''></td></tr>";
			$html2 .= "<tr><td></td><td><input type=hidden name=teammember4_$abc value=''></td></tr>";
		} else {
			$html2 .= "<tr><td>Lead</td><td><input class=inputBox name=teammember1_$abc value=\"".$eventEntry['teammember1']."\"></td></tr>";
			if ( strpos(' '.$eventEntryName,'Pairs') ) {
				$html2 .= "<tr><td>Skip</td><td><input class=inputBox name=teammember2_$abc value=\"".$eventEntry['teammember2']."\"></td></tr>";
				$html2 .= "<tr><td></td><td><input type=hidden name=teammember3_$abc value=''></td></tr>";
				$html2 .= "<tr><td></td><td><input type=hidden name=teammember4_$abc value=''></td></tr>";
			} else {
				$html2 .= "<tr><td>Second</td><td><input class=inputBox name=teammember2_$abc value=\"".$eventEntry['teammember2']."\"></td></tr>";
				if ( strpos(' '.$eventEntryName,'Triples') ) {
					$html2 .= "<tr><td>Skip</td><td><input class=inputBox name=teammember3_$abc value=\"".$eventEntry['teammember3']."\"></td></tr>";
					$html2 .= "<tr><td></td><td><input type=hidden name=teammember4_$abc value=''></td></tr>";
				} else {
					$html2 .= "<tr><td>Third</td><td><input class=inputBox name=teammember3_$abc value=\"".$eventEntry['teammember3']."\"></td></tr>";
					$html2 .= "<tr><td>Skip</td><td><input class=inputBox name=teammember4_$abc value=\"".$eventEntry['teammember4']."\"></td></tr>";
				}
			}
		}
		return $html2;
	}	
	function getAccountCounty() {
		$htmlMain = "";
		$htmlMain .= $this->getAccountCountyMenu();
		if ( $this->variables->v1 == "a" ) {
//		if ( true ) {
			$htmlMain .= $this->getAccount();
		} else {
			$htmlMain .= $this->getCountyEntries();
		}
		return $htmlMain;
	}
	function getAccountAdmin() {
		$htmlMain = "";
		$haveForm = true;
		if ( $this->variables->v1 == "d" ) 
			$haveForm = false;
		$accountRec = $this->mainAccount;
		$user = $this->login->getVariable('username');
		$html2 = "";	
		if ($haveForm)
			$htmlMain .= "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" class=formClassAdmin enctype=\"multipart/form-data\" >";
		$htmlMain .= $this->getAccountAdminMenu($user);
		$html = "<table>";				//print_r($_SERVER);
		$submitbutton = true;
		if ( $this->variables->v1 == "p" ) {
			$html .= "<tr><td>Picture 1</td>";
			$html .= "<td><input class=\"inputBox\" name=\"pic1\" value=\"".$accountRec['pic1']."\" type=\"text\"> <input class=\"inputBox\" id=\"picUpload1\" name=\"picUpload1\"   type=\"file\"></td></tr>";
			$html .= "<tr><td>Picture 2</td>";
			$html .= "<td><input class=\"inputBox\" name=\"pic2\" value=\"".$accountRec['pic2']."\" type=\"text\"> <input class=\"inputBox\" id=\"picUpload2\" name=\"picUpload2\"   type=\"file\"></td></tr>";

	//	$html .= "<td><input name=\"tourid\" value=\"".$event['id']."\" type=\"hidden\"></td></tr>";
		} elseif ( $this->variables->v1 == "u" ) {
			$searchDiv = "";
			if ( isset( $this->variables->posts['search'] ) and strlen($this->variables->posts['search']) > 2 ) {
				$searchDiv = $this->searchUsers($this->variables->posts['search']); 
			}
		//	$submitbutton = false;
			$html .= "<tr><td>Search</td>";
//			$html .= "<td><input class=\"inputBox\" name=\"search\" value=\"".""."\" type=\"text\" onchange=\"searchUser(this.value)\"> ";
			$html .= "<td><input class=\"inputBox\" name=\"search\" value=\"".""."\" type=\"text\"  \"> ";

			$html .= "<tr><td colspan=2><div id=searchDiv>".$searchDiv."</div></td>";
			
		} elseif ( $this->variables->v1 == "u2" ) {
			
			$wheres = array();
			$wheres[] = array('field'=>'id','value'=> $this->variables->v2);	
			$userMember =  $this->getUser($wheres);
			$html .= "<tr><td>Name</td>";
			$html .= "<td><input class=\"inputBox\" name=\"name\" value=\"".$userMember['name']."\" type=\"text\"  \"> </td></tr>";
			$html .= "<tr><td>Email</td>";
			$html .= "<td><input class=\"inputBox\" name=\"email\" value=\"".$userMember['email']."\" type=\"text\"  \"> </td></tr>";
			$html .= "<tr><td>Password </td>";
			$html .= "<td><input class=\"inputBox\" name=\"pwuser\" value=\"\" type=\"text\"  placeholder=\"Leave blank to ignore\"> </td></tr>";
	//		$html .= "<td>".$userMember['email']."</td></tr>";
			$html .= "<tr><td>Current </td>";
			$html .= "<td><i>".$userMember['passw']."</i></td></tr>";
			$html .= "<input  name=\"v2\" value=\"".$this->variables->v2."\" type=\"hidden\"  \">";
			$html2 = $this->getEntry($userMember['email'],false);
		} elseif ( $this->variables->v1 == "m" ) {
			$editor = $this->getEditor('editor');
			$html .= $editor;
			$html .= "<tr><td>Subject</td>";
			$html .= "<td><input class=\"inputBox\" name=\"subject\" value=\"".""."\" type=\"text\"> ";
			$html .= "<tr><td valign=top>Message</td>";
			$html .= "<td><textarea class=editor onblur=\"saveEditor();\" name=\"message\"></textarea></td>";
		//	$this->testMail();
		} elseif ( $this->variables->v1 == "m2" ) {
			$submitbutton = false;
			$outStanding = $this->getMailQueue();
			$html .= "<tr><td id=outStanding>".$outStanding." Email(s) in queue</td>";
			$html .= "<td><input id=outStandingButt class=\"inputBox\" name=\"subject\" value=\"Send\" type=\"button\" onclick=\"sendMail()\"> </td></tr>";
		} elseif ( $this->variables->v1 == "m3" ) {
			$submitbutton = false;
			$emailMess = $this->testMail();;
			$html .= "<tr><td >$emailMess</td></tr>";
		} elseif ( $this->variables->v1 == "m4" ) {
			$submitbutton = false;
			$emailMess = $this->testMail2();;
			$html .= "<tr><td >$emailMess</td></tr>";
		} elseif ( $this->variables->v1 == "f" ) {
			$html .= $this->getPdfEvents();
			
		} elseif ( $this->variables->v1 == "d" ) {
			$submitbutton = false;
			$html .= $this->getDownloadEvents();
		} elseif ( $this->variables->v1 == "s" ) {
			$sponsors = explode('###',$accountRec['sponsors']);
				$html .= "<tr>";
				$html .= "<td>Sponsor</td>";
				$html .= "<td>Link</td>";
				$html .= "<td>Logo</td>";
				$html .= "<td></td>";
				$html .= "</tr>";
			
			$cnt = 0;
			while ( $cnt < SPONSORLOGOS_MAX ) {
				if ( isset($sponsors[$cnt]) ) {
					$sponsor = explode('---',$sponsors[$cnt]);
					$sponsorsLogo = $sponsor[0];
					if ( isset($sponsor[1]) )
						$sponsorsLink = $sponsor[1];
					else 
						$sponsorsLink = "";
				} else {
					$sponsorsLogo = "";
					$sponsorsLink = "";
				}
				$currllink = "<input width=150 class=\"inputBox\" name=\"sponsorsLink_".$cnt."\" value=\"".$sponsorsLink."\" type=\"text\">";
				$currlogo = "<input class=\"inputBox\" name=\"sponsorsLogo_".$cnt."\" value=\"".$sponsorsLogo."\" type=\"text\">";
				$uploadlogo = "<input class=\"inputBox\" id=\"sponsorsUpload_".$cnt."\" name=\"sponsorsUpload_".$cnt."\"   type=\"file\">";
				$cnt++;
				$html .= "<tr>";
				$html .= "<td>$cnt</td>";
				$html .= "<td>$currllink</td>";
				$html .= "<td>$currlogo</td>";
				$html .= "<td>$uploadlogo</td>";
				$html .= "</tr>";
			}
		} elseif ( $this->variables->v1 == "pre" ) {
			$option = false;
			if ( $this->variables->v2  ) {
				$option = $this->variables->v2;
				if ( $option == 'view' ) $submitbutton = false;
			} else {
				$submitbutton = false;
			}
			$html .= $this->setupPreEvent($option);
		} else {
			$html .= "<tr><td>Head Right Column</td>";
			$html .= "<td><input class=\"inputBox\" name=\"textheadright\" value=\"".$accountRec['mback']."\" type=\"text\"></td></tr>";
			$html .= "<tr><td>Text Right Column </td>";
			$html .= "<td><textarea rows=7 cols=70 class=\"inputBox\" name=\"textright\" >".$accountRec['message']."</textarea></td></tr>";
		}
		if ($submitbutton)
			$html .= "<tr><td colspan=2 align=right><input class=\"redSubmitBtn\" name=\"submitAdminAccount\" type=\"submit\" value=\" Submit \"></td></tr>";
		$html .= "</table>";
		if ( $html2 != "") {
			$html = "<table width=\"100%\"><tr><td align=center valign=top>".$html."</td><td align=center valign=top>".$html2."</td></tr></table>";
		}
		$htmlMain .= $html;
		if ($haveForm)
			$htmlMain .= "</form>";
		return $htmlMain;	
	}	
	function getDownloadEvents() {
		$htmlMain = '';
		$webEvents = $this->getEntryEvents();
		if ( isset($this->variables->posts['countyEntry']) )
			$countyEntry = trim($this->variables->posts['countyEntry']);
		else {
			if ( isset($webEvents[0]) )
				$countyEntry = trim($webEvents[0]['code']);
			else 
				$countyEntry = "";
		}
		$htmlMain .= "<form id=countyEntryForm action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" class=formClass >";
		$htmlMain .= "<table><tr><td>Event</td>";
		$htmlMain .= "<td><select class=inputBox  name=countyEntry onchange=\"document.getElementById('countyEntryForm').submit();\">";
		foreach ($webEvents as $key => $webEvent) {
			$optionName = trim($webEvent['name']);
			$optionCode = trim($webEvent['code']);
			$selected = "";
			if ( $optionCode == $countyEntry)
				$selected = "selected";
			
			$htmlMain .= "<option value=$optionCode $selected>$optionName</option>";
		}
		$htmlMain .= "</select></td></tr>";
		$htmlMain .= "</table></form>";
		$webEntries = $this->getEventEntry($countyEntry);

		$htmlMain .= "<form id=countyEntryFormData action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" class=formClass><table>";
		$countyEntries = array();
		foreach ($webEntries as $key => $webEntry) {
			$user = $webEntry['user'];
			if ( isset($countyEntries[$user]) ) 
				$countyEntries[$user]++;
			else
				$countyEntries[$user] = 1;
		}
		$htmlMain .= "<tr><td>County</td><td width=10></td><td>Entries</td></tr>";
		foreach ($countyEntries as $key => $countyEnt) {
			$htmlMain .= "<tr><td>$key</td><td width=10></td><td>$countyEnt</td></tr>";
		}
		$htmlMain .= "<tr><td colspan=3 align=right><input class=\"redSubmitBtn\" name=\"submitAdminDownloadEntries\" type=\"submit\" value=\" Download v1\"></td></tr>";
		$htmlMain .= "<tr><td colspan=3 align=right><input class=\"redSubmitBtn\" name=\"submitAdminDownloadEntries2\" type=\"submit\" value=\" Download v2\"></td></tr>";
		$htmlMain .= "</table><input type=hidden name=countyEntry value='".$countyEntry."'></form>";
		return $htmlMain;
	}
	function getSeason ($seasonId = 0) {
		$query = "SELECT * from webseason WHERE id = $seasonId";
		
		$this->db->query($query);
		$this->db->resultRecord();
		$season = $this->db->row;
		return $season;
	}
	function getSeasons ($archive=3) {
		if ($archive==0)
			$query = "SELECT * from webseason WHERE uid = '".UID."' and archive = ".$archive." and active = 1 order by ord ";
		elseif ( $archive==1 )
			$query = "SELECT * from webseason WHERE uid = '".UID."' and archive = ".$archive." and active = 1 order by ord ";
		elseif ( $archive==2)
			$query = "SELECT * from webseason WHERE uid = '".UID."' and archive != 1 and active = 1 order by ord desc";
		else 
			$query = "SELECT * from webseason WHERE uid = '".UID."' and active = 1 order by ord ";
		$this->db->index = 'id';
		$this->db->query($query);
		$this->db->result();
		$seasons = $this->db->rows;
		return $seasons;
	}
	function getPdfEvents() {
		$html2 = "";
		
		$seasons = $this->getSeasons(3);
		$thisSseason = 0;
		if ( isset($_GET['currseason']) )
			$thisSseason = $_GET['currseason'];
		elseif ( isset($_POST['currseason']) )
			$thisSseason = $_POST['currseason'];
		else
			$thisSseason = DEFAULT_SEASON;
		$html2 .= "<tr><td colspan=5><table border = 1 cellspacing = 0 cellpadding = 4><tr><td>Seasons</td>";
		foreach ( $seasons as $key => $season ) {
			if ( $season['id'] == $thisSseason )
				$html2 .= "<td>".$season['description']."</td>";
			else 
				$html2 .= "<td><a href=\"?p=Account&v1=f&currseason=".$season['id']."\">".$season['description']."</a></td>";
		}
		$html2 .= "</tr></table></td></tr>";
		$currseason = "<input name=\"currseason\" value=\"".$thisSseason."\" type=\"hidden\">";
		$html2 .= $currseason;
//		$query = "SELECT * from webtour WHERE uid = '".UID."' and ( code = '' or code IS NULL) order by page,grid desc,ranklev";
		$query = "SELECT * from webtour WHERE uid = '".UID."' and season = $thisSseason order by page,grid desc,ranklev";
		$this->db->query($query);
		$this->db->result();
		$events = $this->db->rows;
		
		$html2 .= "<tr>";
		$html2 .= "<td>Event</td>";
		$html2 .= "<td>Stage</td>";
		$html2 .= "<td>Code</td>";
		$html2 .= "<td>PDF</td>";
		$html2 .= "<td></td>";
		$html2 .= "</tr>";
		$cnt = 0;
		foreach ( $events as $key => $event ) {
			$eventId = $event['id'];
			$eventName = $event['name'];
			$eventPdf = $event['pdfs'];
			$eventCode = $event['code'];
			$eventPage = $event['page'];
			$eventPdfUpload = "";
			$eventCode = "<input style=\"width:60px;\" class=\"inputBox\" name=\"eventCode_".$eventId."\" value=\"".$eventCode."\" type=\"text\">";
			$eventPdf = "<input class=\"inputBox\" name=\"eventPdf_".$eventId."\" value=\"".$eventPdf."\" type=\"text\">";
			$eventPdfUpload = "<input class=\"inputBox\" id=\"pdfUpload_".$eventId."\" name=\"pdfUpload_".$eventId."\"   type=\"file\">";
			$html2 .= "<tr>";
			$html2 .= "<td>$eventName</td>";
			if ($eventPage == 'F' )
				$html2 .= "<td>Final</td>";
			else
				$html2 .= "<td>Early</td>";
			$html2 .= "<td>$eventCode</td>";
			$html2 .= "<td>$eventPdf</td>";
			$html2 .= "<td>$eventPdfUpload</td>";
			$html2 .= "</tr>";
			$cnt++;
		}
		return $html2;
	}
	function getMailQueue() {
		$query = "SELECT count(*) as cnt FROM emaillog WHERE uid = '".UID."' and !sent ";
		$this->db->query($query);
		$this->db->resultValue();
		$outStanding = $this->db->value;
		return $outStanding;
	}
	function getClubs($county='',$cliubId=false) {
		if ( $cliubId ) {
			$query = "SELECT * FROM webclubs where uid = '".UID."' and clubid = '$cliubId'  ";

			$this->db->query($query);
			$this->db->resultRecord();
			$clubs = $this->db->row;
			
		} else {
			$query = "SELECT * FROM webclubs where uid = '".UID."' and county = '$county' order by name  ";

			$this->db->query($query);
			$this->db->result();
			$clubs = $this->db->rows;
		}
		return $clubs;
	}
	function getCounties() {
		
		$query = "SELECT distinct county,upper(countycode) as countycode FROM webclubs WHERE uid = '".UID."' ";
		$this->db->query($query);
		$index = "countycode";
		$this->db->setIndex($index);
		$this->db->result();
		$counties = $this->db->rows;
		$this->db->clearResult();
		return $counties;
	}
	function getCentries($eventcode,$county) {
		$query = "SELECT * FROM centries WHERE uid = '".UID."' and county= '".$county."' and eventcode = '".$eventcode."'";
		$this->db->query($query);
		$this->db->resultRecord();
		$rec = $this->db->row;
		$retarray = array();
		if ($rec['a']) $retarray[] = "A";
		if ($rec['b']) $retarray[] = "B";
		if ($rec['c']) $retarray[] = "C";
		return $retarray;
	}
	function updateCountyEntries() {
		$countyEntry = $this->variables->posts['countyEntry'];
		$user = $this->login->getVariable('name');

        if($user == '')
        {
            // not logged in
            header("Location: index.php?error=login");
            exit;
        }
		
		$query = "SELECT id,abc FROM wentries WHERE uid = '".UID."' and season = '".DEFAULT_SEASON."' and eventcode = '$countyEntry' and user = '$user' ";
		$this->db->query($query);
		$this->db->setIndex('abc');
		$this->db->result();
		$entries = $this->db->rows;
	//	$abcArray = array('A','B','C');
		$abcArray = $this->getCentries($countyEntry,$user);
		foreach ( $abcArray as $key => $abc ) {

			$club = addslashes($this->variables->posts['club_'.$abc]);
			$contactname = addslashes($this->variables->posts['contactname_'.$abc]);
			$teammember1 = addslashes($this->variables->posts['teammember1_'.$abc]);
			$teammember2 = addslashes($this->variables->posts['teammember2_'.$abc]);
			$teammember3 = addslashes($this->variables->posts['teammember3_'.$abc]);
			$teammember4 = addslashes($this->variables->posts['teammember4_'.$abc]);
			$property = addslashes($this->variables->posts['property_'.$abc]);
			$street = addslashes($this->variables->posts['street_'.$abc]);
			$locality = addslashes($this->variables->posts['locality_'.$abc]);
			$town = addslashes($this->variables->posts['town_'.$abc]);
			$county = addslashes($this->variables->posts['county_'.$abc]);
			$postcode = $this->variables->posts['postcode_'.$abc];
			$contactmobile = $this->variables->posts['contactmobile_'.$abc];
			$contactphone = $this->variables->posts['contactphone_'.$abc];
			$contactemail = $this->variables->posts['contactemail_'.$abc];			
			if ( isset($entries[$abc]) ) {
				$id = $entries[$abc]['id'];
				$query = "UPDATE wentries SET ";
				
				$query .= " club = '$club', contactname = '$contactname'";
				$query .= ", teammember1 = '$teammember1', teammember2 = '$teammember2', teammember3 = '$teammember3', teammember4 = '$teammember4'";
				$query .= ", property = '$property', street = '$street', locality = '$locality', town = '$town', county = '$county', postcode = '$postcode'";
				$query .= ", contactmobile = '$contactmobile', contactphone = '$contactphone', contactemail = '$contactemail'";
				$query .= "WHERE id = '$id' ";
			} else {
				$query = "INSERT INTO wentries (";
				$query .= "uid,season,eventcode,user,abc,club,contactname,teammember1,teammember2,teammember3,teammember4";
				$query .= ",property,street,locality,town,county,postcode ";
				$query .= ",contactmobile,contactphone,contactemail ";
				$query .= " ) VALUES (";
				$query .= "'".UID."','".DEFAULT_SEASON."', '$countyEntry', '$user', '$abc','$club','$contactname'";
				$query .= ",'$teammember1','$teammember2','$teammember3','$teammember4'";
				$query .= ",'$property','$street','$locality','$town','$county','$postcode'";
				$query .= ",'$contactmobile','$contactphone','$contactemail'";
				$query .= ")";
			}
			$this->db->query($query);
		}
	}	
	function updateRes() {
		$newScore = array();
		foreach ($this->variables->posts as $key => $val ) {
			$keyArr = explode('_', $key);
			if ( $keyArr[0] == 'score' ) {
				$newScore[$keyArr[2]]['score'.$keyArr[1]] = $val;
			}
			if ( $keyArr[0] == 'game' ) {
				$newScore[$keyArr[2]]['game'.$keyArr[1]] = $val;
			}
			if ( $keyArr[0] == 'reset' ) {
				$newScore[$keyArr[1]]['reset'] = $val;
			}
			
		}
		
		if ( count($newScore) > 0 ) {
			
			foreach ( $newScore as $id => $scores ) {
				$score1 = trim($scores['score1']);
				$score2 = trim($scores['score2']);
				$game1 = trim($scores['game1']);
				$game2 = trim($scores['game2']);
				
				if ( !( $score1 == "" and  $score2 == "" ) or isset($scores['reset']) ) {
					if ( $score1 == "WO" ) 
						$score1 = "-1";
					if ( $score2 == "WO" ) 
						$score2 = "-1";
					$updated = "W";
					if ( isset($scores['reset']) ) {
						$score1 = "";
						$score2 = "";
						$game1 = "";
						$game2 = "";
						$updated = "";
					}
					$query = "UPDATE webres SET score1='".$score1."',score2='".$score2."',score1string='".$score1."',score2string='".$score2."',game1string='".$game1."',game2string='".$game2."',updated='".$updated."' WHERE id = ".$id." ";
				//	print $query;
					$this->db->query($query);
					if ( $this->login->type == 'ADM' )
						$this->error = RESULT_ENTERED_MESSAGE_ADMIN;
					else 
						$this->error = RESULT_ENTERED_MESSAGE;
					
					$this->logScoreEntry($id);
				//	print $query;
				}
			}
			
		}
	}
	function logScoreEntry($fixId) {
		$query = "SELECT tourcode,league,fixnum,playcode1,playcode2,score1string,score2string,game1string,game2string FROM webres WHERE id = ".$fixId." ";
		$this->db->query($query);
		$this->db->resultRecord();
		$uid = UID;
		$userType = $this->login->getVariable('type');
		if ( strtoupper($this->login->getVariable('type')) == 'ADM' )
			$user = $this->login->getVariable('username');
		else 
			$user = $this->login->getVariable('email');
		
		$evening = $this->db->row['tourcode'];
		$league = $this->db->row['league'];
		$fixnum = $this->db->row['fixnum'];
		$team1 = $this->db->row['playcode1'];
		$team2 = $this->db->row['playcode2'];
		$score1 = $this->db->row['score1string'];
		$score2 = $this->db->row['score2string'];
		$game1 = $this->db->row['game1string'];
		$game2 = $this->db->row['game2string'];
		
		$query = "SELECT name FROM webtour WHERE uid = '".UID."' and code = '".$evening."' ";
		$this->db->query($query);
		$this->db->resultValue();
		$evenname = $this->db->value;
		
		$query = "SELECT name FROM webleag WHERE uid = '".UID."' and tourcode = '".$evening."' and code = '".$league."' ";
		$this->db->query($query);
		$this->db->resultValue();
		$leaguename = $this->db->value;
		
		$query = "SELECT name FROM webteams WHERE uid = '".UID."' and tourcode = '".$evening."' and code = '".$team1."' ";
		$this->db->query($query);
		$this->db->resultValue();		
		$team1name = $this->db->value;
		
		$query = "SELECT name FROM webteams WHERE uid = '".UID."' and tourcode = '".$evening."' and code = '".$team2."' ";
		$this->db->query($query);
		$this->db->resultValue();		
		$team2name = $this->db->value;
		
		$query = "INSERT INTO webscore (uid,user,evening,evenname,league,leaguename,fixnum,team1,team1name,team2,team2name,score1,score2,game1,game2,date) VALUES ('$uid','$user','$evening','$evenname','$league','$leaguename','$fixnum','$team1','$team1name','$team2','$team2name','$score1','$score2','$game1','$game2',now())";
		$this->db->query($query);
	}
	function getEntry($email=false,$readonly=false) {
		if ($email) {
			$html = "";
		} else  {
			$html = "<p id=myMatches>My ".$this->words['fixtures']."</p>";
			$html .= "<p id=myInstructions>".RESULT_ENTER_INSTRUCTIONS."</p>";
		} 
//		if ( count($this->login->teams) > 0 ) {
		if ( true ) {
			if ( !$email ) 
				$email = $this->login->getVariable('email');
		//	$email = "yeo.m@sky.com";
			$updates = false;
			$this->jsBody .= "var matches = [];\n";
			$query = "SELECT code,tourcode,leagcode FROM webteams WHERE uid = '".UID."' and LEFT(tourcode,2) = '".YEAR."' and email = '".$email."' ";
			$this->db->query($query);
			$this->db->result();
			$myTeams = $this->db->rows;
			$this->db->clearResult();
			$html .= "<form method=post><table id=userMatches class=formClass>";
		//	foreach ($this->login->teams as $key => $div ) {
			$canUpdateCnt = 0;
			foreach ($myTeams as $key => $divis ) {
			//	$keyArr = explode($this->login->teamsep,$key);
			//	$tour = $keyArr[0];
			//	$team = $keyArr[1];
				
				$tour = trim($divis['tourcode']);
				if ( $tour != "" ) {
					$teamCode = $divis['code'];
					$div = $divis['leagcode'];
					$event = $this->getEvent($tour);
					$division = $this->getDiv($tour,$div);
					$teamRecord = $this->getTeamRec($tour,$div,$teamCode);

					$eventLink = $event['name'];
					$divLink = $division['name'];
					$divLink = "<a href=\"?p=d&v1=".$tour."&v2=".$div."\" class=divisionHeadAZZ>".$divLink."</a>";
					$html .= "<tr><td colspan=5 class=eventName>".$eventLink."</td></tr>";
					$html .= "<tr><td colspan=5>".$divLink."</td></tr>";
					$this->db->clearResult();
					if ( $division['type'] == 'R' ) 
						$query = "SELECT * FROM webres WHERE uid = '".UID."' and tourcode = '".$tour."' and league = '".$div."' and ( playcode1 = '".$teamCode."' or playcode2 = '".$teamCode."' ) order by date";
					else 
						$query = "SELECT * FROM webres WHERE uid = '".UID."' and tourcode = '".$tour."' and league = '".$div."' and ( playcode1 = '".$teamCode."' or playcode2 = '".$teamCode."' ) order by round";
					$this->db->query($query);
					$this->db->result();
					$matches = $this->db->rows;
					$matchTable = "";
					foreach ( $matches as $key2 => $match ) {
						$id = $match['id'];
						$playcode1 = trim($match['playcode1']);
						$playcode2 = trim($match['playcode2']);
						
						if ($playcode1 == 'BYE' or $playcode2 == 'BYE'  ) {
							$matchTable = "";
						} else {
							$score1 = trim($match['score1string']);
							$score2 = trim($match['score2string']);
							$game1 = trim($match['game1string']);
							$game2 = trim($match['game2string']);
							$scoreWO = false;
							if ( $score1 == '-1' ) {
								$scoreWO = true;
								$score1 = "WO";
								$score2 = "";
							}
							if ( $score2 == '-1' ) {
								$scoreWO = true;
								$score1 = "";
								$score2 = "WO";
							}
							$date = $match['date'];
							$time = $match['time'];
							$date = $match['date'];
							$roundDesc = $match['rounddesc'];
							$updated = $match['updated'];
							$fixres = $match['fixres'];
							$canUpdate = false;
							if ( $fixres == 'F' and !$readonly) {
								$canUpdate = true;
								$updates = true;
								if ( !$scoreWO and  !$updated  ) {
									$score1 = "";
									$score2 = "";
									$game1 = "";
									$game2 = "";
								}
							}
							if ( $fixres == 'F' and $readonly) {
								if ( !$scoreWO and  !$updated  ) {
									$score1 = "";
									$score2 = "";
									$game1 = "";
									$game2 = "";
								}
							}
							if ( $playcode1 == $teamCode ) {
								$otherTeamRecord = $this->getTeamRec($tour,$div,$match['playcode2']);
							} else {
								$otherTeamRecord = $this->getTeamRec($tour,$div,$match['playcode1']);
							}
							$matchTable .= "<tr><td class=roundDesc>".$roundDesc."</td></tr>";
							$reset = "";
							if ( $canUpdate  ) {
								$this->jsBody .= "var match = {id:$id,score1:\"".$score1."\",score2:\"".$score2."\",game1:\"".$game1."\",game2:\"".$game2."\", type:\"".$division['type']."\", sectier:\"".$division['sectier']."\"};";
								$this->jsBody .= "matches.push(match);\n";
								$matchTable .= "<tr><td colspan=7 id=recordError_$id class=recordError></td></tr>";
								$reset = "<input type=checkbox class=scoreInput id=reset_".$id." name=reset_".$id."> Reset";
							}	
							$sc1 = $this->makeScoreCell($id,$score1,$game1,'1',$canUpdate,$division['sectier'],$canUpdateCnt);
							$sc2 = $this->makeScoreCell($id,$score2,$game2,'2',$canUpdate,$division['sectier'],$canUpdateCnt);
							if ( $canUpdate  )
								$canUpdateCnt++;
							if ( $playcode1 == $teamCode ) {
								$t1 = $teamRecord['name'];
								$t2 = $otherTeamRecord['name'];
							} else {
								$t2 = $teamRecord['name'];
								$t1 = $otherTeamRecord['name'];
							}
							$matchTable .= "<tr>";
							if ( $division['type'] == "R" ) {
								$d = date('jS M Y',strtotime($date));
								//$matchTable .= "<td>$date</td>";
								$matchTable .= "<td align=right>$d</td>";
							} else {
								$matchTable .= "<td></td>";
							}
							$matchTable .= "<td>$t1</td>";
							$matchTable .= "<td>$sc1 </td>";
							$matchTable .= "<td> - </td>";
							$matchTable .= "<td>$sc2 </td>";
							$matchTable .= "<td>$t2</td>";
							$matchTable .= "<td>$reset</td>";
							$matchTable .= "</tr>";
						}
						
					}
					
				} else {
					$matchTable = "";
				}
				$matchTable .= "<tr><td >&nbsp;</td></tr>";
				$html .= $matchTable;
			}      
			if ( $updates ) {
					$html .= "<tr><td><input type=submit name=resSubmit value=Submit onclick=\"return validateScore();\"></td></tr>";
			}
			$html .= "</table></form><br>";
			
			$this->db->clearResult();
		}
		return $html;
	}
	function makeScoreCell($id,$score,$game,$team,$canUpdate,$secTier,$canUpdateCnt) {
		if ( $canUpdate  ) {
			$sc = "<input class=scoreInput size=2 name=score_".$team."_".$id." id=score_".$team."_".$id." value='".$score."' onblur=\"updateScore('score".$team."','score_".$team."_".$id."',$canUpdateCnt);\">";
			if ( $secTier == "1" ) {
				$sc .= " <input class=scoreInput size=2 id=game_".$team."_".$id." name=game_".$team."_".$id." value='".$game."' onblur=\"updateScore('game".$team."','game_".$team."_".$id."',$canUpdateCnt);\">";
			} else {
				$sc .= "<input type=hidden name=game_".$team."_".$id." value=".$game.">";
			}
		} else {
			$sc = $this->cleanScore($score);
			if ( $secTier == "1" ) {
				$game = $this->cleanScore($game);
				$sc .= " ($game)";
			}
		}
		return $sc;
	}
	function getDivTeam($tour,$div,$teamcode) {
		
		
		$event = $this->getEvent($tour);
		$division = $this->getDiv($tour,$div);
		$team = $this->getTeamRec($tour,$div,$teamcode);
		$this->db->clearResult();
		
		$query = "SELECT * FROM webres WHERE uid = '".UID."' and tourcode = '".$tour."' and league = '".$div."' and (playcode1 = '".$teamcode."' or playcode2 = '".$teamcode."') order by date,time";

		$this->db->query($query);
		$this->db->result();
		$results = $this->db->rows;
		$this->db->clearResult();
		
		$teams = $this->getEntries($tour,$div);// print_r($teams);
		$table = "<table id=entries>";
		foreach ($results as $key => $row) {
			$showScore = true;
			$player1code = trim($row['playcode1']);
			$player2code = trim($row['playcode2']);
			$date = strtotime($row['date']);
			$date = date('jS M Y', $date);
			$showPlayedBy = true;
			if ($player1code == "BYE"  ) {
				$player1 = "BYE";
				$showScore = false;
				$showPlayedBy = false;
			} elseif ( $player1code == "") {
				$player1 = "TBD";
				$showScore = false;
			} elseif (isset($teams[$player1code]) ) {					
				$player1 = $teams[$player1code]['name'];
				if ( $player1code != $teamcode ) {
					$player1 .= " (".$teams[$player1code]['clubname'].")";
					$player1 = "<a href=?p=t&v1=$tour&v2=$div&v3=$player1code>$player1</a>";
				}
			} else {
				$player1 = "";
				$showScore = false;
				
			}                                       
			if ( $player2code == "BYE" ) {
				$player2 = "BYE";
				$showScore = false;
				$showPlayedBy = false;
			} elseif ( $player2code == "" ) {
				$player2 = "TBD";
				$showScore = false;
			} elseif (isset($teams[$player2code]) ) {	
				$player2 = $teams[$player2code]['name'];
				if ( $player2code != $teamcode ) {
					$player2 .= " (".$teams[$player2code]['clubname'].")";
					$player2 = "<a href=?p=t&v1=$tour&v2=$div&v3=$player2code>$player2</a>";
				}
			} else  {
				$player2 = "";
				$showScore = false;
			} 
			$score1 = trim($row['score1string']);
			$score2 = trim($row['score2string']);
			if ( $score1 == '-1' ) {
				$score1 = "";
				$score2 = "WO";
			}
			if ( $score2 == '-1' ) {
				$score1 = "WO";
				$score2 = "";
			}
			$scores = "";
			$scoresClass = "entryName";
			if ( $showScore  ) {
					$score1 = str_replace('.0','',$score1);
					$score2 = str_replace('.0','',$score2);
					$score1 = str_replace('.5',$this->halfPoint,$score1);
					$score2 = str_replace('.5',$this->halfPoint,$score2);
				if ( $row['updated'] == 'W' ) {
					$scoresClass = "entryScoreNoneConfirmed";
					$scores = "$score1 - $score2";
				} elseif ( $row['fixres'] == 'R' ) {
					$scores = "$score1 - $score2";
				}		
			}
			if ( $showPlayedBy ) {
				$playedby = "Played by $date";
			} else {
				$playedby = "";
			}
			$table .= "<tr>";
			$table .= "<td class=entryName>".$player1."</td>";
			$table .= "<td class=$scoresClass>".$scores."</td>";
			$table .= "<td class=entryName>".$player2."</td>";
			$table .= "<td class=entryName>".$playedby."</td>";
			
			$table .= "</tr>";
		}
		$table .= "</table>";
	//	print_r($division);
		$eventLink = $event['name'];
		$eventLink = "<a href=\"?p=e&v1=".$tour."\" class=eventHeadA  width=100%>".$eventLink."</a>";
		$divtLink = $division['name'];
		$divtLink = "<a href=\"?p=d&v1=".$tour."&v2=".$div."\" class=divisionHeadA  width=100%>".$divtLink."</a>";
		
		$html = "<table>";
		$html .= "<tr><td><p class=eventHead >".$eventLink."</p></td></tr>";
		$html .= "<tr><td><p class=divisionHead >".$divtLink."</p></td></tr>";
		$html .= "<tr><td><p class=teamHead>".$team['name']." (".$team['clubname']." ".$team['county'].")</p></td></tr>";
		$html .= "</table>";
		$html .= $table;
		
		return $html;
	}
	function getTeamRec($tour,$div='',$team) {
		$query = "SELECT t.name,t.email,t.clubid,c.name as clubname,c.county,c.area FROM webteams t LEFT JOIN webclubs c ON t.clubid = c.clubid and  c.uid = '".UID."' WHERE t.uid = '".UID."' and t.tourcode = '".$tour."' and t.code = '".$team."' ";
		$this->db->query($query);
		$this->db->resultRecord();
		$teamRecord = $this->db->row;
		return $teamRecord;
	}
	function getDiv($tour,$div) {
		$query = "SELECT * FROM webleag WHERE uid = '".UID."' and tourcode = '".$tour."' and code = '".$div."' ";
		$this->db->query($query);
		$this->db->resultRecord();
		$this->division = $this->db->row;
		return $this->division;
	}
	function getEntries($tour,$div,$fullevent=false) {
		if ( $fullevent ) {
			$query = "SELECT t.code,t.fname,t.lname,t.name,t.clubid,c.name as clubname,c.county,c.area FROM webteams t left join webclubs c ON t.clubid = c.clubid  and  t.uid = c.uid  WHERE t.uid = '".UID."'  and tourcode = '".$tour."'   order by lname,fname,name";
			$index = "code";
			$this->db->query($query);
			
			$this->db->setIndex($index);
			$this->db->result();
			return $this->db->rows;
		} else {
			$query = "SELECT t.code,t.fname,t.lname,t.name,t.clubid,c.name as clubname,c.county,c.area FROM webteams t left join webclubs c ON t.clubid = c.clubid WHERE t.uid = '".UID."' and c.uid = '".UID."'  and tourcode = '".$tour."' and leagcode = '".$div."' order by lname,fname,name";
			$query = "SELECT t.code,t.fname,t.lname,t.name,t.clubid,c.name as clubname,c.county,c.area FROM webteams t left join webclubs c ON t.clubid = c.clubid  and  t.uid = c.uid  WHERE t.uid = '".UID."'  and tourcode = '".$tour."' and leagcode = '".$div."' order by lname,fname,name";
			$index = "code";
			$this->db->query($query);
			
			$this->db->setIndex($index);
			$this->db->result();
			$this->entries = $this->db->rows;
			$this->db->clearResult();
			return $this->entries;
		}
	}
	function getKnockHeaders($tour,$div) {
		$query = "SELECT distinct round,rounddesc,date FROM webres WHERE uid = '".UID."'  and tourcode = '".$tour."' and league = '".$div."' ORDER BY round";
		$this->db->query($query);
		$this->db->result();
		$knockHeaders = array();   
		
		foreach( $this->db->rows as $key => $row) {
			if ( $row['date'] != '0000-00-00' ) {
				$thisRound = $row['round'];
				if ( isset($knockHeaders[$thisRound]) ) {
					$knockHeaders[$thisRound]['date2'][] = $row['date'];
				} else {
					$knockHeaders[$thisRound] = $row;
					$knockHeaders[$thisRound]['date2'][] = $row['date'];
				}
			}
		}
		return $knockHeaders;
	}
	function getResults($tour,$div) {
		if (  $this->division['type'] == 'R') {
			$query = "SELECT playcode1,playcode2,score1,score2,score1string,score2string,game1string,game2string,date,time,fixres,updated,round,rounddesc,CONCAT(knock) as knock,chall,homeaway,fixnum FROM webres WHERE uid = '".UID."'  and tourcode = '".$tour."' and league = '".$div."' ORDER BY date,time";
			$index = "";
		} elseif ( $this->variables->view == 'k'  or $this->variables->view == ''  ) {
			$query = "SELECT playcode1,playcode2,score1,score2,score1string,score2string,game1string,game2string,date,time,fixres,updated,round,rounddesc,CONCAT(knock) as knock,chall,homeaway,fixnum FROM webres WHERE uid = '".UID."'  and tourcode = '".$tour."' and league = '".$div."' ORDER BY knock,round,date,time";
			$index = "knock";
		} else {
			$query = "SELECT playcode1,playcode2,score1,score2,score1string,score2string,game1string,game2string,date,time,fixres,updated,round,rounddesc,CONCAT(knock) as knock,chall,homeaway,fixnum FROM webres WHERE uid = '".UID."'  and tourcode = '".$tour."' and league = '".$div."' ORDER BY round,date,time";
			$index = "";
		}
		$this->db->query($query);
		$this->db->setIndex($index);
		$this->db->result();
		$this->fixtures = $this->db->rows;
	}
	function getDivision($tour,$div) {
		
		
//		$event = $this->getEvent($tour);
//		$division = $this->getDiv($tour,$div);
		$event = $this->event;
		$division = $this->division;
		$entries = $this->entries;
		$fixtures = $this->fixtures;
//		$this->db->clearResult();
//		$entries = $this->getEntries($tour,$div);

	
		if ( count($fixtures) == 0 or $this->variables->view == 'l' ) {
			$players = array();
			foreach ($entries as $key => $row) {
				
				$playcode = $row['code'];
				$fname = $row['fname'];
				$lname = $row['lname'];
				$name = $row['name'];
				$clubname = $row['clubname'];
				$county = $row['county'];
				$clubId = $row['clubid'];
			//	$name = $fname." ".$lname;
				$lname = $name;
				$lnameArr = explode(' ',$name);
				if ( count($lnameArr) > 0 )
					$lname = $lnameArr[count($lnameArr) - 1];
				$name = "<A href=?p=t&v1=".$tour."&v2=".$div."&v3=".$playcode." class=entryNameA>".$name."</A>";
				$playRec = "";
				$playRec .= "<TD class=entryName>".$name."</TD>";
				$playRec .= "<TD class=entryName>".$clubname."</TD>";
				$playRec .= "<TD class=entryName>".$county."</TD>";
				if ( $this->login->type == 'PLA' and false ) {
					if ( isset($entered[$code])  ) {
						$enter = "Entered";
						$playRecc .= "<TD>".$enter."</TD>";
					} else {
						$enter = "<a href=?enter=".$code.">Enter</a>";
						$playRec .= "<TD>".$enter."</TD>";
					}
				}
				$playInder = str_pad($county,50,' ',STR_PAD_RIGHT).str_pad($clubname,70,' ',STR_PAD_RIGHT).$lname;
				$players[$playInder] = $playRec;
			}
			if ( count($players) > 1 )
				ksort($players);
			$tableRec = "";
		//	$tableRec = "<TR><TD><p >".$this->words['teams']."</p></TD></TR>";
			
			foreach ($players as $index => $record) {
				$tableRec .= "<TR>";
				$tableRec .= $record;
				$tableRec .= "</TR>\n";
			}
		} elseif ( $this->variables->view == 'f' ) {	
			$entries = $this->getEntries($tour,$div,true);
			$knockHeaders = $this->getKnockHeaders($tour,$div);
			$tableRec = "<TR><TD><p >".$this->words['teams']."</p></TD></TR>";
			$tableRec = "";
			$oldRounddesc = "";
			$cntRound = 1;
			foreach ($fixtures as $key => $row) {
				$playcode1 = trim($row['playcode1']);
				$playcode2 = trim($row['playcode2']);
				$playClass1 = "entryName";
				$playClass2 = "entryName";
				$scoreClass1 = "entryScore";
				$scoreClass2 = "entryScore";
				$fixres = $row['fixres'];
				$updated = $row['updated'];
				$chall = $row['chall'];
				if ( $updated == 'W' ) {
					$scoreClass1 = "entryScoreNoneConfirmed";
					$scoreClass2 = "entryScoreNoneConfirmed";
				}
				$date = strtotime($row['date']);
				$date = date('jS M Y', $date);
				$scoreSep = "";
				$time = $row['time'];
				if ( $time == "00.00" )
					$time = "";
				$winner = 0;
				$updated = $row['updated'];
				$rounddesc = $row['rounddesc'];
				$printFix = true;
				if ($division['type'] == 'R' )
					$chall = 0;
				if ( $playcode1 == 'BYE' or $playcode2 == 'BYE' ) 
					$printFix = false;
					
				if ( $fixres == 'F' and $updated != 'W' ) {
					$score1 = "";
					$score2 = "";
				} else {
					$score1 = trim($row['score1string']);
					$score2 = trim($row['score2string']);
					if ( $score1 > $score2 ) {
						$playClass1 = "entryNameWon";
						$winner = 1;
					} 
					if ( $score2 > $score1 ) {
						$playClass2 = "entryNameWon";
						$winner = 2;
					} 
					$scoreSep = " - ";
				}
				if ( $score1 == '-1' ) {
					$score1 = "WO";
					$score2 = "";
					$playClass1 = "entryNameWon";
					$playClass2 = "";
					$winner = 1;
					$scoreSep = " ";
				}
				if ( $score2 == '-1' ) {
					$score1 = "";
					$score2 = "WO";
					$playClass1 = "";
					$playClass2 = "entryNameWon";
					$winner = 2;
					$scoreSep = " ";
				}
				if ( isset($entries[$playcode1]) ) {
					$name1 = $entries[$playcode1]['name'];
					if ( $chall == 1 )
						$name1 .= $this->challanger;
					if ( isset($entries[$playcode1]['clubname']) )
						$name1 .= " (".$entries[$playcode1]['clubname'].")";
					$name1 = "<a href=?p=t&v1=$tour&v2=$div&v3=$playcode1>$name1</a>";
					
					
				} else {
					$name1 = "";
				}
				if ( isset($entries[$playcode2]) ) {
					$name2 = $entries[$playcode2]['name'];
					if ( $chall == 2 )
						$name2 .= $this->challanger;
					if ( isset($entries[$playcode2]['clubname']) )
						$name2 .= " (".$entries[$playcode2]['clubname'].")";
					$name2 = "<a href=?p=t&v1=$tour&v2=$div&v3=$playcode2>$name2</a>";
					
				} else {
					$name2 = "";
				}
				if ( $rounddesc != $oldRounddesc ) {
					$oldRounddesc = $rounddesc;
					if ( isset($knockHeaders[$cntRound]) ) {
					//	$rounddate = strtotime($knockHeaders[$cntRound]['date']);
						$rounddate2 = $knockHeaders[$cntRound]['date2'];
						$rounddate = strtotime(end($rounddate2));
						$rounddate = "Play-by ".date('jS M Y', $rounddate);
					} else {
						$rounddate = "";
					}
					$tableRec .= "<TR><TD class=entryRound colspan=5>".$rounddesc."&nbsp&nbsp&nbsp&nbsp".$rounddate."</TD></TR>";
					$cntRound++;
				}
				if ( $printFix ) {
					$tableRec .= "<TR>";
					$score1 = $this->cleanScore($score1);
					$score2 = $this->cleanScore($score2);
					
					$tableRec .= "<TD class=$playClass1>".$name1."</TD>";
					$tableRec .= "<TD class=$scoreClass1>".$score1."</TD>";
					$tableRec .= "<TD class=entryName>".$scoreSep."</TD>";
					$tableRec .= "<TD class=$scoreClass2>".$score2."</TD>";
					$tableRec .= "<TD class=$playClass2>".$name2."</TD>";
					
				//	$tableRec .= "<TD class=entryName> ".$fixres."</TD>";
				//	$tableRec .= "<TD class=entryName> ".$date."</TD>";
				//	$tableRec .= "<TD class=entryName> ".$time."</TD>";
				//	$tableRec .= "<TD class=entryName> ".$updated."</TD>";
					$tableRec .= "</TR>";
				}
			}
	//	} elseif ( $this->variables->view == 'k' ) {
		} elseif ( $division['type'] == 'R' ) {	
			$entries = $this->getEntries($tour,$div,true);
			$this->getPoints();
			$this->processResults();
			$this->sortTeams();
			
			$tableRec = "";
			$tableRec .= "<TR><TD width=800px><center>";
			$tableRec .= $this->drawTable();
			$tableRec .= "<br><br>";
			$tableRec .= $this->drawReults();
			$tableRec .= "</center></td></TR>";
		} else {
			
			$entries = $this->getEntries($tour,$div,true);
			$teamsDone = ",,";
			$knockHeaders = $this->getKnockHeaders($tour,$div);
			require_once('knock.php');
			//print_r($knockHeaders);
			$rounds = count($knockHeaders)- 1 ;
		//	$rounds = 2;
			$roundsFiddle = 0;
			$roundsFiddleCols = 0;
			if ( !isset($fixtures[9]) ) {
				$roundsFiddleCols++;
				$roundsFiddle++;
				if ( !isset($fixtures[5]) ) {
					$roundsFiddle++;
					if ( !isset($fixtures[1]) ) {
						$roundsFiddle++;
					}
				}
			} 
			$rows = pow(2,$rounds + $roundsFiddle);
			$knockArray = getKnockArr ($rounds + 1 + $roundsFiddle);
	//		$rows = pow(2,$rounds);
	//		$knockArray = getKnockArr ($rounds+1);
	//				$rows = pow(2,$rounds+ 1);
	//				$knockArray = getKnockArr ($rounds+2);
			
			$tableKnockSize = "<tr><td >Draw $rows</td></tr>";
			$tableKnockSize = "";
			$tableKnockHead = "";
			$tableKnockRows = "";
			$cntCount = 0;
			for ($i = 0; $i < $rows; $i++) {
				$tableKnockRows .= "<tr>";
				$colCount = 0;
				for ($j = 0; $j < $rounds + $roundsFiddleCols; $j++) {
					$rowSpan = pow(2,$j);
					if ( $i == 0 ) {
						$tableKnockHead .= "<td class=knockCellHead>#HEAD_$j#</td>";
					}
					if ( $i % $rowSpan == 0 ) {
						if ( $j == 0 ) {
							$cellCount = $i;
						} else {
							$cellCount = $rows + $i / $rowSpan;
						}
						for ($k = 1; $k < $j; $k++) { 
							$cellCount += $rows / pow(2,$k);
						}
						$tableKnockRows .= "<td rowspan=$rowSpan class=knockCell>#CELL_$cellCount#</td>";
					}
				}
				if ( $i == 0 and $roundsFiddle == 0) {		// Final
					if ( isset($rowSpan) ) 
						$rowSpan *=2;
					else
						$rowSpan =1;
					$cellCount = 2 * $rows - 1 - 1;
					$tableKnockHead .= "<td class=knockCellHead>#HEAD_$rounds#</td>";
					$tableKnockRows .= "<td rowspan=$rowSpan class=knockCell>#CELL_".$cellCount."#</td>";
				}
				$tableKnockRows .= "</tr>";
			}
			
			foreach ( $knockHeaders as $key => $value  ) {
				$round = $value['round'];
				$rounddesc = $value['rounddesc'];
		//		$rounddesc .= "<br>Play-by ".date('jS M Y',strtotime($value['date']));
				$dates = $this->makeDates($value['date2']);

				
				$rounddesc = $this->getRounddesc($rounddesc,$dates,$division,$event);
			//	$rounddesc .= "<br>".date('jS M Y',strtotime($value['date']));
				$roundSearch = $round - 1;
				$roundSearch = "#HEAD_".$roundSearch."#";
				$tableKnockHead = str_replace($roundSearch,$rounddesc,$tableKnockHead);
			}
			$knockCnt = 0;
			foreach ( $knockArray as $key => $value  ) {
				$cellName = "#CELL_".$knockCnt."#";
				if ( isset($fixtures[$key]) ) {
					$cellArray = $fixtures[$key];
					$playcode1 = trim($cellArray['playcode1']);
					$playcode2 = trim($cellArray['playcode2']);
					$playClass1 = "knockPCell";
					$playClass2 = "knockPCell";
					$score1 = trim($cellArray['score1string']);
					$score2 = trim($cellArray['score2string']);
					$fixres = $cellArray['fixres'];
					$updated = $cellArray['updated'];
					$chall = $cellArray['chall'];
					$homeaway = $cellArray['homeaway'];
					$date = date('jS M Y',strtotime($cellArray['date']));
					$name1Win = "";
					$name2Win = "";
					if ( isset($entries[$playcode1]) ) {
						$name1 = $entries[$playcode1]['name'];
						$name1Win = $name1;
						if ( $chall == 1 and false)
							$name1 .= $this->challanger;
						$name1 = "<a href=?p=t&v1=$tour&v2=$div&v3=$playcode1>$name1</a>";		
						if ( !strpos($teamsDone,$playcode1) ) {
							if ( $entries[$playcode1]['clubname'] ) 
								$name1 .= '<br>('.$entries[$playcode1]['clubname'].")";
		//					$teamsDone .= $playcode1.',';
						}
					} elseif ( $playcode1 == 'BYE' )  {
						$name1 = "BYE";
					} else  {
						if ( $chall == 1 and false)
							$name1 = $this->challanger;
						else 
							$name1 = "&nbsp;";
					} 
					if ( isset($entries[$playcode2]) ) {
						$name2 = $entries[$playcode2]['name'];
						$name2Win = $name2;
						if ( $chall == 2 and false )
							$name2 .= $this->challanger;
						$name2 = "<a href=?p=t&v1=$tour&v2=$div&v3=$playcode2>$name2</a>";	
						if ( !strpos($teamsDone,$playcode2) ) {
							if ( $entries[$playcode2]['clubname'] ) 
								$name2 .= '<br>('.$entries[$playcode2]['clubname'].")";
		//					$teamsDone .= $playcode2.',';
						}
					} elseif ( $playcode2 == 'BYE' ) {
						$name2 = "BYE";
					} else {
						
						if ( $chall == 2 and false)
							$name2 = $this->challanger;
						else 
							$name2 = "&nbsp;";
					} 
					if ( ( $name1 == "&nbsp;" and $name2 == "BYE" ) or ( $name2 == "&nbsp;" and $name1 == "BYE" ) ) {
						$name1 = "&nbsp;";
						$name2 = "&nbsp;";
					}
					if ( $score1 == "-1" ) {
						$scores = "<b>WO ".$name1Win."</b>";
						$playClass1 = "knockPCellWon";
					} elseif ( $score2 == "-1" ) {
						$scores = "<b>WO ".$name2Win."</b>";
						$playClass2 = "knockPCellWon";
					} else {
						if (  $score1 > $score2 ) 
							$playClass1 = "knockPCellWon";
						if (  $score2 > $score1 ) 
							$playClass2 = "knockPCellWon";
						$score1 = $this->cleanScore($score1);
						$score2 = $this->cleanScore($score2);
						
						$scores = "<b>".$score1." - ".$score2."</b>";
					} 
					$scoreClass = "knockPCell";
					if ( $updated == 'W' ) {
						$scoreClass = "knockPCellNoneConfirmed";
					}
					if ($fixres == 'F') {
						$dateRes = "";
						if ( $chall == 1 )
							$dateRes = $this->upkey.$this->challanger;		//$date;
						if ( $chall == 2 )
							$dateRes = $this->downkey.$this->challanger;		//$date;
						if ($name1 == 'BYE' or $name2 == 'BYE') {
							$dateRes = "";
						}
						if ( $updated == 'W' ) {
							$dateRes = $scores." ".$dateRes;
						}
					} else  {
						$dateRes = $scores;
					}
					if ( $homeaway == 1 and false) {
						$name1 = $this->home.$name1;
						$name2 = $this->away.$name2;
					}
					if ( $homeaway == 2 and false) {
						$name2 = $this->home.$name2;
						$name1 = $this->away.$name1;
					}
						
					$cellValue = "<p class=$playClass1>$name1</p>";
			//		if ( $knockCnt >= $rows ) 
					$cellValue .= "<p class=$scoreClass>$dateRes</p>";
					$cellValue .= "<p class=$playClass2>$name2</p>";
				} else {
					$cellValue = "";
				}
				$tableKnockRows = str_replace($cellName,$cellValue,$tableKnockRows);
				$knockCnt++;
			}
			$tableKnock = "<table id=knockTable border=1 width=100% cellspacing=0>";
			$tableKnock .= $tableKnockSize;
			$tableKnock .= $tableKnockHead;
			$tableKnock .= $tableKnockRows;
			$tableKnock .= "</table>";
			$tableRec = $tableKnock;
	//	} else {
		
		}
		$eventlink = $event['name'];
		$eventlink = "<a class=eventHeadA href=\"?p=e&v1=".$event['code']."\">$eventlink</a>";
		$table = "<table>";
		$table .= "<tr><td><p class=eventHead>".$eventlink."</p></td></tr>";
		$table .= "<tr><td><p class=divisionHead>".$division['name']."</p></td></tr>";
		$table .= "</table>";
		$table .= "<TABLE id=entries>";
		$table .= $tableRec;
		$table .= "</TABLE>";
		
		
		$return = $table;
		return $return;
	}
	function getRounddesc($rounddesc,$dates,$division,$event) {
		if ( strtoupper($division['name']) == "FINAL" or strtoupper($division['name']) == "FINALS" ) {
			if ( stripos($event['name'],"Club Two Fours") ) {
				if ( $event['season'] == 2 ) {
					if ( $rounddesc == "Round 1" ) {
						$rounddesc = " Last 16";
						$rounddesc .= "<br>Play by ".$dates;
						$rounddesc .= "<br>Neutral venue ";
					} else {
						$rounddesc .= "<br>Play on ".$dates;
						$rounddesc .= "<br>Victoria Park ";
					} 	
				} else {
					if ( $rounddesc == "Round 1" )
						$rounddesc .= "<br>Play by ".$dates;
					else
						$rounddesc .= "<br>Play on ".$dates;
				}
			}	
			elseif ( stripos($event['name'],"Tony Allcock Trophy") ) {
				if ( $event['season'] == 2 ) {
					if ( $rounddesc == "Round 1" ) {
						$rounddesc = " Last 16";
						$rounddesc .= "<br>Play by ".$dates;
						$rounddesc .= "<br>Neutral venue ";
					} else {
						$rounddesc .= "<br>Play on ".$dates;
						$rounddesc .= "<br>Victoria Park ";
					} 	
				} else {
					if ( $rounddesc == "Round 1" )
						$rounddesc .= "<br>Play by ".$dates;
					else
						$rounddesc .= "<br>Play on ".$dates;
				}
			}
			elseif ( stripos($event['name'],"Womens Top Club") ) {
				if ( $event['season'] == 2 ) {
					if ( $rounddesc == "Preliminary Round" or $rounddesc == "Round 1"  or $rounddesc == "Round 2" ) {
						$rounddesc .= "<br>Play by ".$dates;
						$rounddesc .= "<br>Neutral venue ";
					} else {
						$rounddesc .= "<br>Play on ".$dates;
						$rounddesc .= "<br>Victoria Park ";
					} 						
				} else {	
					$rounddesc .= "<br>Play by ".$dates;
				}	
			}
			elseif ( stripos($event['name'],"Club Two Fours") ) {
				if ( $event['season'] == 2 ) {
					if ( $rounddesc == "Round 1" ) {
						$rounddesc .= "<br>Play by ".$dates;
						$rounddesc .= "<br>Neutral venue ";
					} else {
						$rounddesc .= "<br>Play on ".$dates;
						$rounddesc .= "<br>Victoria Park ";
					} 						
				} else {	
					$rounddesc .= "<br>Play by ".$dates;
				}	
			}	
			elseif ( stripos($event['name'],"Mens Top Club") ) {
				if ( $event['season'] == 2 ) {
					if ( $rounddesc == "Round 1" ) {
						$rounddesc = "Last 16";
						$rounddesc .= "<br>Play by ".$dates;
					} else {	
						$rounddesc .= "<br>Play on ".$dates;
					}					
				} else {	
					$rounddesc .= "<br>Play on ".$dates;
				}	
			}	
			else 
				$rounddesc .= "<br>Play on ".$dates;
		} else 
			$rounddesc .= "<br>Play by ".$dates;	

		return $rounddesc;
	}
	function makeDates($dates) {
		if ( count($dates) < 2 )
			$thisDates = date('jS M Y',strtotime(end($dates)));
		else {
			$thisDates = "";
			$thisCurrentMonth = date('M',strtotime($dates[0]));
			foreach ( $dates as $key => $date ) {
				$thisDate = date('jS',strtotime($date));
				$thisMonth = date('M',strtotime($date));
				if ( $thisMonth != $thisCurrentMonth ) {
					$thisCurrentMonth = $thisMonth;
				}
				if ( $thisDates == "" )
					$thisDates .= $thisDate;
				else 
					$thisDates .= "/".$thisDate;
			}
			$thisDates .= " ".$thisCurrentMonth;
			$thisDates .= date(' Y',strtotime(end($dates)));
		}
		return $thisDates;
	}
	function getPoints() {
		$points = array();
		$points['win'] = 10;
		$points['draw'] = 5;
		$points['loss'] = 0;
		
		$this->points = $points;
	}	
	function processResults() {
		foreach ( $this->fixtures as $key => $r ) {

			if ( $r['fixres'] == 'R' or $r['updated'] == 'W' ) {
				$fixnum = $r['fixnum'];
				$playcode1 = $r['playcode1'];
				$playcode2 = $r['playcode2'];
				$score1 = $r['score1string'];
				$score2 = $r['score2string'];
				$game1 = $r['game1string'];
				$game2 = $r['game2string'];
				if ( $score1 > $score2 ) {
					$this->teams[$playcode1]['won']++;
					$this->teams[$playcode1]['points'] += $this->points['win'];
					$this->teams[$playcode2]['loss']++;
					$this->teams[$playcode2]['points'] += $this->points['loss'];
				} elseif ( $score2 > $score1 ) {
					$this->teams[$playcode1]['loss']++;
					$this->teams[$playcode1]['points'] += $this->points['loss'];
					$this->teams[$playcode2]['won']++;
					$this->teams[$playcode2]['points'] += $this->points['win'];
				} else {
					$this->teams[$playcode1]['draw']++;
					$this->teams[$playcode1]['points'] += $this->points['draw'];
					$this->teams[$playcode2]['draw']++;
					$this->teams[$playcode2]['points'] += $this->points['draw'];
				}
				$this->teams[$playcode1]['points'] += $game1 * 2;
				$this->teams[$playcode2]['points'] += $game2 * 2;
				$fixArray = array('fixnum'=>$fixnum,'t1'=>$playcode1,'t2'=>$playcode2,'s1'=>$score1,'s2'=>$score2);
				
				$this->teams[$playcode1]['played']++;
				$this->teams[$playcode1]['for']+= $score1;
				$this->teams[$playcode1]['against']+= $score2;
				$this->teams[$playcode1]['gfor']+= $game1;
				$this->teams[$playcode1]['gagainst']+= $game2;
				$this->teams[$playcode1]['diff'] = $this->teams[$playcode1]['for'] - $this->teams[$playcode1]['against'];
				$this->teams[$playcode1]['fixtures'][] = $fixArray;
				$this->teams[$playcode2]['played']++;
				$this->teams[$playcode2]['for']+= $score2;
				$this->teams[$playcode2]['against']+= $score1;
				$this->teams[$playcode2]['gfor']+= $game2;
				$this->teams[$playcode2]['gagainst']+= $game1;
				$this->teams[$playcode2]['diff'] = $this->teams[$playcode2]['for'] - $this->teams[$playcode2]['against'];
				$this->teams[$playcode2]['fixtures'][] = $fixArray;
		//	$this->results[$fixnum] = $r;
			}
		}
	}
	function sortTeams() {
		$teams2 = array();	//print_r($this->teams);
		$maxPoints = 10000;
		$cnt = 1;
		$oldPoint = "";
		foreach ( $this->teams as $key => $team ) {
			$padOrder = "";
	
			$padOrder .= str_pad($maxPoints - $team['points'], 5, "0", STR_PAD_LEFT);
			$padOrder .= str_pad($maxPoints - $team['diff'], 5, "0", STR_PAD_LEFT);
			$padOrder .= str_pad($maxPoints - $team['for'], 5, "0", STR_PAD_LEFT);
			$padOrder .= str_pad($maxPoints - $team['between'], 5, "0", STR_PAD_LEFT);
			$padOrder .= str_pad($team['lname'], 30, " ", STR_PAD_RIGHT);
			$padOrder .= str_pad($team['fname'], 30, " ", STR_PAD_RIGHT);
			$padOrder .= str_pad($cnt, 10, "0", STR_PAD_LEFT);
			$team['order'] = $padOrder;
			$teams2[$team['order']] = $team['code'];
			$cnt++;
		}
		ksort($teams2);
		$this->orderedTeams = $teams2;
	}
	function drawReults() {
		$reults = "";
		$reults .= "<table id=resultTable>";
		$oldDate = "";
		foreach ($this->fixtures as $key => $fixture ) {
			$team1 = "";
			$team2 = "";
			$scoreDiv = " - ";
			$score1 = $this->cleanScore($fixture['score1string']);
			$score2 = $this->cleanScore($fixture['score2string']);
			$game1 = $this->cleanScore($fixture['game1string']);
			$game2 = $this->cleanScore($fixture['game2string']);
			$date = date('jS M y',strtotime($fixture['date']));
			if ( $oldDate != $date ) 
				$reults .= "<tr><td class=resultsDate colspan=5>".$date."</td></tr>";
			$oldDate = $date;
			if ( $fixture['fixres'] == 'R' or $fixture['updated'] == 'W' ) {
				if ( $this->division['sectier'] == "1" ) {
					$score1 .= " ($game1) ";
					$score2 .= " ($game2) ";
				}
			} else {
				$score1 = "";
				$score2 = "";
			}
			if ( isset( $this->teams[$fixture['playcode1']] ) )
				$team1 = $this->teams[$fixture['playcode1']]['name'];
			if ( isset( $this->teams[$fixture['playcode2']] ) )
				$team2 = $this->teams[$fixture['playcode2']]['name'];
			
			$reults .= "<tr>";
			$reults .= "<td class=resultsTeam>".$team1."</td>";
			$reults .= "<td class=resultsTeam>".$score1."</td>";
			$reults .= "<td class=resultsTeam>".$scoreDiv."</td>";
			$reults .= "<td class=resultsTeam>".$score2."</td>";
			$reults .= "<td class=resultsTeam>".$team2."</td>";
		//	$reults .= "<td class=resultsTeam>".$date."</td>";
			$reults .= "</tr>";
		}
		$reults .= "</table>";
		return $reults;
	}
	function cleanScore($thisScore) {
		$newScore = trim($thisScore);
		if ( $newScore == '0.5')
			$newScore = str_replace('0.5',$this->halfPoint,$newScore);
		$newScore = str_replace('.5',$this->halfPoint,$newScore);
		$newScore = str_replace('.0',"",$newScore);
		return $newScore;
	}
	function drawTable() {
		$showRinksStats = false;
		$table = "";
		//print_r($this->orderedTeams);
		$cnt = 1;
		$table .= "<table cellspacing=0 id=leagueTable>";
		$tableHead = "<tr><td class=leagueHead>Pos</td><td class=leagueHead>".$this->words['team']."</td><td class=leagueHead>Played</td>";
		$tableHead .= "<td class=leagueHead>Win</td><td class=leagueHead>Draw</td><td class=leagueHead>Loss</td>";
		$tableHead .= "<td class=leagueHead>For</td><td class=leagueHead>Against</td><td class=leagueHead>Points</td>";
		if ( $showRinksStats ) 
			$tableHead .= "<td class=leagueHead>Rinks For</td><td class=leagueHead>Rinks Against</td>";
		$tableHead .= "</tr>";
		$table .= $tableHead;
		foreach ($this->orderedTeams as $key => $code ) {
//		foreach ($this->teams as $key => $team ) {
			$team = $this->teams[$code];
			$diff = $team['diff'];
			if ( $cnt % 2 == 0 )
				$table .= "<tr class=leagueRow1>";
			else 
				$table .= "<tr class=leagueRow2>";
			$table .= "<td class=leagueRight>".$cnt."</td>";
			$table .= "<td class=leagueTeamName>".$team['name']."</td>";
			$table .= "<td class=leagueRight>".$team['played']."</td>";
			$table .= "<td class=leagueCenter>".$team['won']."</td>";
			$table .= "<td class=leagueCenter>".$team['draw']."</td>";
			$table .= "<td class=leagueCenter>".$team['loss']."</td>";
			$table .= "<td class=leagueCenter>".$team['for']."</td>";
			$table .= "<td class=leagueCenter>".$team['against']."</td>";
		//	$table .= "<td class=leagueCenter>".(substr($diff,0,1)=='-' ? $diff: '+'.$diff)."</td>";
			$table .= "<td class=leagueRight>".$team['points']."</td>";
			if ( $showRinksStats ) {
				$table .= "<td class=leagueCenter>".$this->cleanScore($team['gfor'])."</td>";
				$table .= "<td class=leagueCenter>".$this->cleanScore($team['gagainst'])."</td>";
			}
			$table .= "</tr>";
			$cnt++;
		}
		$table .= "</table>";
		return $table;
		
	}
	function getEvent($tour) {
		$query = "SELECT * FROM webtour WHERE uid = '".UID."' and code = '".$tour."' ";
		$this->db->query($query);
		$this->db->resultRecord();
		$event = $this->db->row;
		$eventPic = $this->imagesFolder.$event['image'];
		if ( $event['image'] and file_exists($eventPic) )
			$this->footerImage = $event['image'];
		$this->event = $event;
		return $event;
	}
	
	function getTournament($tour) {
	
		$tour = $this->variables->tour;
		
		$counties = $this->getCounties();
		$event = $this->getEvent($tour);
		$this->db->clearResult();
		$query = "SELECT length(name) as name FROM webleag WHERE uid = '".UID."' and tourcode = '".$tour."' order by 1 desc LIMIT 1";
		$this->db->query($query);
		$this->db->resultValue();
		$width = $this->db->value;
		
		if ( $width < 25 )
			$cols = 3;
		elseif ( $width < 50 )
			$cols = 2;
		else 
			$cols = 1;
		$cntRow = 0;
		if ( false ) {
		if ( false ) {
			$query = "SELECT * FROM webleag WHERE uid = '".UID."' and tourcode = '".$tour."' and !locate('Finals',name) order by orde ";
		} else {
			$query = "SELECT * FROM webleag WHERE uid = '".UID."' and tourcode = '".$tour."' order by orde ";
		}
		$this->db->query($query);
		$this->db->result();
		}
		$tableRec = "";
		foreach ($this->divisions as $key => $row) {
			
			$leag = $row['code'];
			$leagname = $row['name'];
			$leagname = $this->getLeagueName($counties,$leagname);
			$players = "";
			if ( $event['detaildiv'] ) {
				$query = "SELECT name FROM webteams WHERE uid = '".UID."' and tourcode = '".$tour."' and leagcode = '".$leag."' order by name";
				$this->db->query($query);
				$this->db->result();
				$entries = $this->db->rows;
				$entriesCnt = count($entries);
				$players = "<table>";
				foreach ($entries as $key2 => $entry ) {
					$players .= "<tr><td>".$entry['name']."</td></tr>";
				}
				$players .= "</table>";
			}
			$league = "<A href=?p=d&v1=".$tour."&v2=".$leag.">".$leagname."</A>";
			if ( $cntRow % $cols == 0 ) 
				$tableRec .= "<TR>";
			$tableRec .= "<TD class=divisionName valign=top>".$league.$players."</TD>";
		//	$tableRec .= "<TD class=entryCount>".$entriesCnt." ".$this->words['fixtures']."</TD>";
			
			$cntRow++;
			if ( $cntRow % $cols == 0 ) 
				$tableRec .= "</TR>\n";
		}
		if ( $cntRow % $cols != 0 ) 
				$tableRec .= "</TR>\n";
			
		$allTours = "<a href=''>All ".$this->words['events']."</a>";
		$html = "";
	//	$table = "<p class=eventAll>".$allTours."</p>";
		$html .= "<p class=eventHead>".$event['name']."</p>";
		$html .= "<TABLE class=divisions>";
		$html .= $tableRec;
		$html .= "</TABLE>";
		
		
		
		return $html;
	}
	function getLeagueName($counties,$leagname) {
		$leagnameArr = explode('/',$leagname);
		$newLeagnameArr = array();
		foreach ( $leagnameArr as $key => $val ) {
			if ( isset($counties[$val]) ) 
				$newLeagname = $counties[$val]['county'];
			else 
				$newLeagname = ucwords(strtolower ($val));
			
			$newLeagnameArr[] = $newLeagname;
		}
		
		return implode(" / ",$newLeagnameArr);
	}
..............................
}
?>