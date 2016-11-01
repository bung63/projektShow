<?php

class Login {
    private $id;
    private $recordId;
    private $username;
    private $password;
	
    private $first_name; 
    private $last_name;
	private $name;
    private $email;
    private $variables;
	
    private $session_id;
	private $login;
	
	var $teamsep = '~';
	var $type = "";
	var $mess = "";
	var $sh;
	var $playcode = "";
	var $error = "";
	var $teams = array();
	
    public function __construct($variables){
		$this->variables = $variables;
    }
	
	function getLogin() {
		
		if ( $this->username or $this->email ) {
			$this->login = "<a href=?p=Account>".$this->name."</a> / <a href=?p=Logout>Logout</a>";
		} else {
			$this->login = "<a href=?p=Login>Login</a>";
			$this->login .= " / <a href=?p=Register>Register</a>";
		}
		return $this->login;
	}

    // Creates a new account based on a new user name and password
    // username must be unique
    // password gets md5 (hashed)
    // It also checks if username already exists
	public function addUser(){
		$username = trim($this->variables->posts['username']);
		$password = trim($this->variables->posts['password']);
		$fname = trim($this->variables->posts['fname']);
		$lname = trim($this->variables->posts['lname']);
		$username = $_POST['username'];     
		$query = "SELECT * FROM passw WHERE email = '$username'  and profile = '".UID."'";
		$thisDB = new Database();
		$thisDB->query($query);
		$thisDB->result();
		
		if ( count($thisDB->rows) > 0 ) {
			$this->error = "Email already exists. Please pick another one!";  
		}
		if ( !$this->error ) {
			if ( strpos($username,'@') === false ) {
				$this->error = $username." is not an email ";
			} else {

				if ( strlen($password) > 5 ) {
					if ( strlen($lname ) == 0 and strlen($lname ) == 0 ) {
						$this->error = "Both First Name and Last Name can't be blank ";
					} else {
						$uniqueid = "";
						$modul = "WEB";
						$code = "PLA";
						$name = $fname.' '.$lname;
						$query = "INSERT INTO passw (profile,uniqueid,passw,name ,email,modul,code ) values ('".UID."','$uniqueid','$password','$name','$username','$modul','$code')";
						$thisDB = new Database();
						$thisDB->query($query);
						$thisDB->close();
						$this->error = "User added ";
						
					}
				} else {
					$this->error = "Password need to be at least 6 characters long ";
				}
			}
		}
	}
	public function addUser3(){
		$username = $_POST['username'];
		$query = "SELECT * FROM passw WHERE uniqueid = '$username'  and profile = '".UID."'";
		$thisDB = new Database();
		$thisDB->query($query);
		$thisDB->result();
		
		if ( count($thisDB->rows) > 0 ) {
			$this->error = "Username name already exists. Please pick another one!";  
		}
		if ( !$this->error ) {
			$thisDB = new Database();
			$query = "SELECT code  FROM webteams WHERE uid = '".UID."' order by 1 desc LIMIT 1";
			$thisDB->query($query);
			$thisDB->result();
			if ( count($thisDB->rows) > 0 ) {
				$teamnum = substr($thisDB->rows[0]['code'], 1);    
				$teamnum++;
			} else {
				$teamnum = 1;
			}
			$teamnum = "W".str_pad($teamnum, 6, "0", STR_PAD_LEFT);
			$thisDB->query($query);
			$thisDB->result();
			$username = $_POST['username'];
			$password = $_POST['password'];
			$fname = $_POST['fname'];
			$lname = $_POST['lname'];
			$name = $fname." ".$lname;
			
			$query = "INSERT INTO passw (uniqueid,profile,name,passw) values( '$username'  ,'".UID."','$name','$password')";
			$thisDB->query($query);
			$query = "INSERT INTO passw (uniqueid,profile,name,passw,uid) values( '$username'  ,'".UID."','$name','$password','$teamnum')";
			$thisDB->query($query);
			$query = "INSERT INTO webteams (uid,name,code,fname,lname) values( '".UID."','$name','$teamnum','$fname','$lname')";
			$thisDB->query($query);
		}
		$thisDB->close();
	}
    public function addUser2($username, $password, $first_name, $last_name, $email){
        $username = $this->clean($username);
        $password = $this->generateHash($this->clean($password));
        $first_name = $this->clean($first_name);
        $last_name = $this->clean($last_name);
        $email = $this->clean($email);

        // Check if username already exists
        $query = ("SELECT * FROM users WHERE username = '$username' LIMIT 0,5");    

        $result = mysql_query($query) OR die("Cannot perform query!");

        // Check if user name already exists and if it does not exist, create a new account

        if (mysql_num_rows($result) >= 1) {
            echo "User's name already exists. Please pick another one!";        
        } else {

            // otherwise create an account
            $query = "INSERT INTO users VALUES('', '" . $username . "', '" . $password . "', '" . $first_name . "'
                        , '" . $last_name . "', '" . $email . "')";       
            $result = mysql_query($query) OR die('Cannot perform query! Make sure you have filled out all the fields!');    
            echo "Your account has been created. You can now log in.";
        }
    }

    public function deleteUser($username){
        $username = $this->clean($username);
         // Check if username already exists
        $query = "DELETE FROM users WHERE username = '$username'";      

        $result = mysql_query($query) OR die("Cannot perform query!");
        $this->destroyCookieAndSession();
        header("Location: index.php");

    }

    // updates user's information
    public function updateUser($username, $password){

        $username = $this->clean($username);
        $password =  $this->generateHash($this->clean($password));

        $query = "UPDATE users SET password ='$password' WHERE username = '$username'";     

        //die();    
        $result = mysql_query($query) OR die("Cannot perform query!");
        echo "Your changes have been saved.<br/>";

    }

    // Check if the user account and password match the one in the database
	
	public function checkLogin() {
		
		if ( $this->variables->page == 'Logout' )  {
			$this->username = '';
			$this->password  = '';
			$this->name = '';
			$this->type = '';
			$this->playcode = '';
			$this->id = '';
			$this->sh = '';
			$this->mess = '';
			$this->teams = array();
		//	unset($_SESSION['recordId']);
			setcookie('recordId', '', time()-1);
			setcookie('loggedIn', '', time()-1);
			unset($_GET['p']);
		} elseif ( isset($_POST['submitReg']) && isset($_POST['username']) && isset($_POST['password']) ) {
			$this->addUser();
		} elseif ( isset($_POST['submitLogin']) && isset($_POST['username']) && isset($_POST['password']) ) {
			$username = $_POST['username'];
			$password = $_POST['password'];
			if ( strpos($username,'@') === false ) 
				$query = "SELECT * FROM passw WHERE uniqueid = '$username' and passw = '$password' and profile = '".UID."'";
			else 
				$query = "SELECT * FROM passw WHERE email = '$username' and passw = '$password' and profile = '".UID."'";
		
			$thisDB = new Database();
			$thisDB->query($query);
			$thisDB->resultRecord();
			$thisDB->close();
			if ( count($thisDB->row) > 0 ) {
				$this->getAccount($thisDB->row);
				unset($_GET['page']);
			} else {
				$this->error = "Not valid Username or Password";
			}
			
	//	} elseif ( isset($_SESSION['recordId']) ) {
		} elseif ( isset($_COOKIE['recordId']) or isset($_COOKIE['loggedIn']) ) {
			if ( !isset($_COOKIE['recordId']) and isset($_COOKIE['loggedIn']) ) {
				
				setcookie('recordId', '', time()-1);
				setcookie('loggedIn', '', time()-1);
				$this->error = "Session has run out";
//			if ( time() - $_SESSION['time'] > SESSIONLIFE ) {
			} elseif ( time() - $_COOKIE['time'] > COOKIELIFEUSER ) {
//				unset($_SESSION['recordId']);
				setcookie('recordId', '', time()-1);
				setcookie('loggedIn', '', time()-1);
				$this->error = "Session has run out";
			} else {
//				$query = "SELECT * FROM passw WHERE id = '".$_SESSION['recordId']."'";
				$query = "SELECT * FROM passw WHERE id = '".$_COOKIE['recordId']."'";
				$thisDB = new Database();
				$thisDB->query($query);
				$thisDB->resultRecord();
				$thisDB->close();
				if ( count($thisDB->row) > 0 ) {
					$this->getAccount($thisDB->row);
				} else {
					$this->error = "Session has run out";
				}
				
			}
		}
		//print_r($this);
	}
	public function getVariable($thisVariable) {
		return $this->$thisVariable;
	}
	public  function updatePassword() {
		if ( isset($_POST['newPassword1']) and isset($_POST['newPassword2']) and isset($_POST['oldPassword']) ) {
			$newPassword1 = trim($_POST['newPassword1']);
			$newPassword2 = trim($_POST['newPassword2']);
			$oldPassword = trim($_POST['oldPassword']);
			if ( $oldPassword == $this->password ) {
				if ( $newPassword1 == $newPassword2 ) {
					if ( strlen($newPassword1) > 5  ) {
						$query = "UPDATE passw SET passw = '$newPassword1'  WHERE id = '".$this->id."' ";
						$thisDB = new Database();
						$thisDB->query($query);
						$thisDB->close();
						$this->error = "Password updated ";
					} else {
						$this->error = "Password need to be at least 6 characters long ";
					}
				} else {
					$this->error = "The 2 New passwords are not the same ";
				}
			} else {
				$this->error = "Old password is not correct";
			}
		} else {
			$this->error = "Old password and New password need to be filled in";
		}
	}
	public  function getAccount($thisRow) {
		
		$this->username = $thisRow['uniqueid'];
		$this->password = $thisRow['passw'];
		$this->name = $thisRow['name'];
		$this->type = $thisRow['code'];
		$this->playcode = $thisRow['uid'];
		$this->id = $thisRow['id'];
		$this->email = $thisRow['email'];
		$this->sh = $thisRow['sh'];
		$this->mess = $thisRow['mess'];
	//	$this->teams = $thisRow['teams'];
		
//		$_SESSION['recordId'] = $thisRow['id'];
//		$_SESSION['time'] = time();
		setcookie("recordId",$thisRow['id'],time()+COOKIELIFEUSER);
		setcookie("time",time(),time()+COOKIELIFEUSER);
		setcookie('loggedIn', 'loggedIn', time()+COOKIELIFE);
		
		if ( $this->email != "" ) {
			$this->teams = array();
			$query = "SELECT * FROM webteams WHERE uid = '".UID."' and email = '".$this->email."'";
			$thisDB = new Database();
			$thisDB->query($query);
			$thisDB->result();
			
			foreach ($thisDB->rows as $key => $value) {
				$this->teams[$value['tourcode'].$this->teamsep.$value['code']] = $value['leagcode'];
			}
			$thisDB->close();
		}
	}	
    public function checkLogin2($username, $password, $rememberme, $session_id) {

        $this->username = $this->clean($username);
        $this->password = $this->clean($password);
        $this->$session_id = $session_id;

        //extract the salt/hash from db and check if the hash/password is correct
        $query = "SELECT * FROM users WHERE username = '" . $this->username . "' LIMIT 0,1"; 

        $result = @mysql_query($query) OR die('Cannot perform query!'); 
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $dbHash = $row['password'];


        // generates hash based on the submitted password and stored salt
        $this->password = $this->generateHash($this->password, $dbHash);


        $query = "SELECT * FROM users WHERE username = '" . $this->username . "' AND 
                password ='" . $this->password . "' LIMIT 0,1";                                             

        $result = mysql_query($query) OR die('Cannot perform query!');  


        if (mysql_num_rows($result) == 1) {     

            //set a cookie if rememberme is set to 'on' 

            if($rememberme == "on"){
                $this->setRememberMe($session_id);

        }

        // user has logged in successfuly, store all his information in this object 
        // before redirecting to securePage.php
        $this->setFirstName($row['first_name']);
        $this->setLastName($row['last_name']);
        $this->setEmail($row['email']);


        $this->createSession();
        header("Location: securePage.php");
        exit();

        } else {

            echo "Incorrect username or/and password.";
        }

        // frees the memory used by query   
        mysql_free_result($result);         
    }

    private function createSession(){


        // save state of this object before passing
        // php automatically serializes the object
        // and will automatically unserialize it

        $_SESSION['usrData'] = $this;

    }

    // sets the cookie
    // which allows the user to be logged into automatically
    private function setRememberMe($session_id){

        // check if the user id exists in the session db, if it does, delete that row

        $query = "SELECT * FROM sessions WHERE user_id = '" . $this->getUsername() . "' LIMIT 0,5"; 
        $result = mysql_query($query) OR die("Cannot perform query!");

        if (mysql_num_rows($result) >= 1) {
            $query = "DELETE FROM sessions WHERE user_id = '" . $this->getUsername() . "'"; 
            $result = mysql_query($query) OR die("Cannot perform query!");
        }

        // insert the user's information into a session table
        $query = "INSERT INTO sessions (session_id, user_ip, user_agent, user_id) 
                  VALUES('" . $session_id . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . 
                  $_SERVER['HTTP_USER_AGENT'] . "', '" . $this->getUsername() . "')";
        $result = mysql_query($query) OR die('Cannot perform query!!'); 

        // create a cookie with session_id         
        setcookie("autologin", $session_id, time() + 60*60*24*365, "/");

    }

    // check if the user has access to the page
    public function isAuthorized() {

        // check the session access
        if(isset($_COOKIE['autologin']) ) {

            // check if user information matches up
            // we do that by checking user agent and user ip information
            $session_id = $_COOKIE['autologin'];
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $user_agent =  $_SERVER['HTTP_USER_AGENT'];

            $query = "SELECT * FROM sessions WHERE session_id = '" . $session_id . "'";                                         

            $result = mysql_query($query) OR die('Cannot perform query!');  

            // query the results only once since there's supposed to be only
            // one record for each session_id
            $row = mysql_fetch_assoc($result); 

            if ( $row["user_ip"] == $user_ip && $row["user_agent"] == $user_agent)
            {
                // if everything matches, create a new Login object based on user ID

                // Check if username already exists
                $query2 = "SELECT * FROM users WHERE username = '" . $row["user_id"] . "' LIMIT 0,5";   
                $result2 = mysql_query($query2) OR die("Cannot perform query!");
                while ( $row2 = mysql_fetch_assoc($result2) ){
                    $this->username = $row2['username'];
                    $this->first_name = $row2['first_name'];
                    $this->last_name = $row2['last_name'];
                    $this->password = $row2['password'];
                    $this->email = $row2['email'];
                    $this->session_id = $session_id;
                } 

                $_SESSION['usrData'] = $this;
                return true;

            } else {
                // Information does not match
                return false;
            }

        } else {
            // if cookie is not set.
            return false;
        }

    }

    // private function that allows connection to the database 
    public function connectToDB() { 
        @mysql_connect(DB_HOST, DB_USER, DB_PASS) OR die("Cannot connect to MySQL server!");    
        mysql_select_db("dig_login") OR die("Cannot select database!");
    }


    // Returns the username of a user
    public function getUsername() {
        return $this->username;
    }

    // Returns the plain text password of a user
    public function getPassword() {
        return $this->password;
    }
    // Returns first name
    public function getFirstName() {
        return $this->first_name;
    }
    // Returns last name
    public function getLastName() {
        return $this->last_name;
    }
    public function getEmail() {
        return $this->email;
    }
    //gets session
    public function getSessionID(){
        return $this->session;
    }


    // sets first name
    public function setFirstName($firstName) {
        $this->first_name = $firstName;
    }
    // sets last name
    public function setLastName($lastName) {
        $this->last_name = $lastName;
    }
    // sets email
    public function setEmail($email) {
        $this->email = $email;
    }


    // Escape bad input, sql injections, etc 
    private function clean($input) {
        return mysql_real_escape_string($input);
    }   

    // Kill the cookie
    public function destroyCookieAndSession(){
        setcookie('autologin', '', time()-42000, '/');
        session_unset();
        session_destroy();

    }
    // This is a function that does the hashing
    // we are going to use sha256 as hashing algorithm 
    // If $salt is not passed, it creates a new salt
    // otherwise it extracts the salt from db 
    public function generateHash($password, $salt = null){

        if ($salt === null)
        {
            $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
        }
        else
        {
            $salt = substr($salt, 0, SALT_LENGTH);

        }


        return $salt . hash('sha256', $salt . $password);

    }

}



?>