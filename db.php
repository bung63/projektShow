<?php

class Database { 

	var $server   = ""; 
	var $user     = ""; 
	var $pass     = ""; 
	var $database = ""; 
	var $connected = false; 
	
	var $cache = "";
	var $index = "";
	var $link_id = 1; 
	var $query_id = 0; 
	var $result = "";
	var $row = array();
	var $rows = array();
	var $value = ""; 
	var $Json = "";
	// db details
	var $db1 = array('server'=>'','user'=>'','pass'=>'','database'=>'');
	
	function connect() { 
		$db = $this->db1;
		$this->server = $db['server'];
		$this->user = $db['user'];
		$this->pass = $db['pass'];
		$this->database = $db['database'];
		
		$this->link_id = new mysqli($this->server, $this->user, $this->pass, $this->database);
		if ($this->link_id->connect_errno) {//open failed 
			$this->oops("Could not connect to server: <b>$this->server</b>."); 
        } else {
			$this->connected = true;
		//	$this->set_charset('utf8');
		}

	}
	function close() { 
		$this->link_id->close();
	}
	function setIndex($index="") {
		$this->index = $index;
	}

	function query($query) {
		if ( !$this->connected ) {
			$this->connect();
		}
		//print_r($this);
		$this->clearResult(0);
		$this->result = $this->link_id->query($query);
		$this->index = "";
	}
	function clearResult($index=1) {
		$this->rows = array();
		$this->row = array();
		$this->value = "";
		if ( $index )
			$this->index = "";
	}
	function resultValue() {
		$this->row = $this->result->fetch_row();
		$this->value = $this->row[0];
	}
	function resultRecord() {
		$this->row = $this->result->fetch_assoc();
	}
	function result() {
		
		while($r = $this->result->fetch_assoc()) {
			
			if ( $this->index ) {
				$this->rows[$r[$this->index]] = $r;
				
			} else {
				$this->rows[] = $r;
			}
		}
		
		
	}
	
	function makeJson() {
	//	print_r($this->rows);
//	print mb_detect_encoding(json_encode($this->rows));
		$thisRows = $this->utf8ize($this->rows);
		$this->Json = json_encode($thisRows);
//		$this->Json = json_encode($this->rows);
		if ($this->cache) {
			$filename = "cache/".$this->cache."_cache.php";
			file_put_contents($filename, $this->Json);
		}
	//	print $this->Json;
	}
	function utf8ize($d) {
		if (is_array($d)) 
			foreach ($d as $k => $v) 
				$d[$k] = $this->utf8ize($v);
	
		else if(is_object($d))
			foreach ($d as $k => $v) 
				$d->$k = $this->utf8ize($v);
	
		else 
			return utf8_encode($d);
	
		return $d;
	}
}
?>