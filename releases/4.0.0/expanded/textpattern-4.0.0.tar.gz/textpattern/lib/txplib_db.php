<?php

/*
$HeadURL$
$LastChangedRevision$
*/

define('TXP_DEBUG', 0);

if (!defined('PFX')) {
	if (!empty($txpcfg['table_prefix'])) {
		define ("PFX",$txpcfg['table_prefix']);
	} else define ("PFX",'');
}

class DB {
    function DB() 
	{
		global $txpcfg;

		$this->host = $txpcfg['host'];
		$this->db   = $txpcfg['db'];
		$this->user = $txpcfg['user'];
		$this->pass = $txpcfg['pass'];

		$this->link = mysql_connect($this->host, $this->user, $this->pass);
		$this->version = mysql_get_server_info();;

		if (!$this->link) {
			$GLOBALS['connected'] = false;
		} else $GLOBALS['connected'] = true;
		mysql_select_db($this->db) or die(db_down());

		$version = $this->version;
		// be backwardscompatible
		if ( isset($txpcfg['dbcharset']) && (intval($version[0]) >= 5 || preg_match('#^4\.[1-9]#',$version)) )
			mysql_query("SET NAMES ". $txpcfg['dbcharset']);
    }
}
$DB = new DB;

//-------------------------------------------------------------
	function safe_query($q='',$debug='',$unbuf='')
	{
		global $DB,$txpcfg, $qcount, $production_status;
		$method = (!$unbuf) ? 'mysql_query' : 'mysql_unbuffered_query';
		if (!$q) return false;
		if ($debug or TXP_DEBUG === 1) { 
			dmp($q);
			dmp(mysql_error());
//			dmp(debug_backtrace());
		}
		$result = $method($q,$DB->link);
		@$qcount++;
		if ($result === false and (@$production_level == 'debug' or @$production_level == 'test'))
			trigger_error(mysql_error() . n . $q, E_USER_ERROR);

		if(!$result) return false;
		return $result;
	}

// -------------------------------------------------------------
	function safe_delete($table, $where, $debug='')
	{
		$q = "delete from ".PFX."$table where $where";
		if ($r = safe_query($q,$debug)) {
			return true;
		}
		return false;
	}

// -------------------------------------------------------------
	function safe_update($table, $set, $where, $debug='') 
	{
		$q = "update ".PFX."$table set $set where $where";
		if ($r = safe_query($q,$debug)) {
			return true;
		}
		return false;
	}

// -------------------------------------------------------------
	function safe_insert($table,$set,$debug='') 
	{
		global $DB;
		$q = "insert into ".PFX."$table set $set";
		if ($r = safe_query($q,$debug)) {
			$id = mysql_insert_id($DB->link);
			return ($id === 0 ? true : $id);
		}
		return false;
	}

// -------------------------------------------------------------
	function safe_alter($table, $alter, $debug='') 
	{
		$q = "alter table ".PFX."$table $alter";
		if ($r = safe_query($q,$debug)) {
			return true;
		}
		return false;
	}

// -------------------------------------------------------------
	function safe_optimize($table, $debug='') 
	{
		$q = "optimize table ".PFX."$table";
		if ($r = safe_query($q,$debug)) {
			return true;
		}
		return false;
	}

// -------------------------------------------------------------
	function safe_repair($table, $debug='') 
	{
		$q = "repair table ".PFX."$table";
		if ($r = safe_query($q,$debug)) {
			return true;
		}
		return false;
	}

// -------------------------------------------------------------
	function safe_field($thing, $table, $where, $debug='') 
	{
		$q = "select $thing from ".PFX."$table where $where";
		$r = safe_query($q,$debug);
		if (@mysql_num_rows($r) > 0) {
			$f = mysql_result($r,0);
			mysql_free_result($r);
			return $f;
		}
		return false;
	}

// -------------------------------------------------------------
	function safe_column($thing, $table, $where, $debug='') 
	{
		$q = "select $thing from ".PFX."$table where $where";
		$rs = getRows($q,$debug);
		if ($rs) {
			foreach($rs as $a) {
				$v = array_shift($a);
				$out[$v] = $v;
			}
			return $out;
		}
		return array();
	}

// -------------------------------------------------------------
	function safe_row($things, $table, $where, $debug='') 
	{
		$q = "select $things from ".PFX."$table where $where";
		$rs = getRow($q,$debug);
		if ($rs) {
			return $rs;
		}
		return array();
	}


// -------------------------------------------------------------
	function safe_rows($things, $table, $where, $debug='') 
	{
		$q = "select $things from ".PFX."$table where $where";
		$rs = getRows($q,$debug);
		if ($rs) {
			return $rs;
		}
		return array();
	}

// -------------------------------------------------------------
	function safe_rows_start($things, $table, $where, $debug='') 
	{
		$q = "select $things from ".PFX."$table where $where";
		return startRows($q,$debug);
	}

//-------------------------------------------------------------
	function safe_count($table, $where, $debug='') 
	{
		return getThing("select count(*) from ".PFX."$table where $where",$debug);
	}

// -------------------------------------------------------------
	function safe_show($thing, $table, $debug='') 
	{
		$q = "show $thing from ".PFX."$table";
		$rs = getRows($q,$debug);
		if ($rs) {
			return $rs;
		}
		return array();
	}


//-------------------------------------------------------------
	function fetch($col,$table,$key,$val,$debug='') 
	{
		$q = "select $col from ".PFX."$table where `$key` = '$val' limit 1";
		if ($r = safe_query($q,$debug)) {
			$thing = (mysql_num_rows($r) > 0) ? mysql_result($r,0) : '';
			mysql_free_result($r);
			return $thing;
		}
		return false;
	}

//-------------------------------------------------------------
	function getRow($query,$debug='') 
	{
		if ($r = safe_query($query,$debug)) {
			$row = (mysql_num_rows($r) > 0) ? mysql_fetch_assoc($r) : false;
			mysql_free_result($r);
			return $row;
		}
		return false;
	}

//-------------------------------------------------------------
	function getRows($query,$debug='') 
	{
		if ($r = safe_query($query,$debug)) {
			if (mysql_num_rows($r) > 0) {
				while ($a = mysql_fetch_assoc($r)) $out[] = $a; 
				mysql_free_result($r);
				return $out;
			}
		}
		return false;
	}

//-------------------------------------------------------------
	function startRows($query,$debug='')
	{
		return safe_query($query,$debug);
	}

//-------------------------------------------------------------
	function nextRow($r)
	{
		$row = mysql_fetch_assoc($r);
		if ($row === false)
			mysql_free_result($r);
		return $row;
	}

//-------------------------------------------------------------
	function getThing($query,$debug='') 
	{
		if ($r = safe_query($query,$debug)) {
			$thing = (mysql_num_rows($r) != 0) ? mysql_result($r,0) : '';
			mysql_free_result($r);
			return $thing;
		}
		return false;
	}

//-------------------------------------------------------------
	function getThings($query,$debug='') 
	// return values of one column from multiple rows in an num indexed array
	{
		$rs = getRows($query,$debug);
		if ($rs) {
			foreach($rs as $a) $out[] = array_shift($a);
			return $out;
		}
		return array();
	}
	
//-------------------------------------------------------------
	function getCount($table,$where,$debug='') 
	{
		return getThing("select count(*) from ".PFX."$table where $where",$debug);
	}

// -------------------------------------------------------------
 	function getTree($root, $type)
 	{ 

		$root = doSlash($root);

	    extract(safe_row(
	    	"lft as l, rgt as r", 
	    	"txp_category", 
			"name='$root' and type = '$type'"
		));


		$right = array(); 

	    $rs = safe_rows_start(
	    	"id, name, lft, rgt, parent, title", 
	    	"txp_category",
	    	"lft between $l and $r and type = '$type' order by lft asc"
		); 

	    while ($row = nextRow($rs)) {
	   		extract($row);
			while (count($right) > 0 && $right[count($right)-1] < $rgt) { 
				array_pop($right);
			}

        	$out[] = 
        		array(
        			'id' => $id,
        			'name' => $name,
        			'title' => $title,
        			'level' => count($right), 
        			'children' => ($rgt - $lft - 1) / 2
        		);

	        $right[] = $rgt; 
	    }
    	return($out);
 	}

// -------------------------------------------------------------
	function rebuild_tree($parent, $left, $type) 
	{ 
		$right = $left+1;

		$parent = doSlash($parent);

		$result = safe_column("name", "txp_category", 
			"parent='$parent' and type='$type' order by name");
	
		foreach($result as $row) { 
    	    $right = rebuild_tree($row, $right, $type); 
	    } 

	    safe_update(
	    	"txp_category", 
	    	"lft=$left, rgt=$right",
	    	"name='$parent' and type='$type'"
	    );
    	return $right+1; 
 	} 

//-------------------------------------------------------------
	function get_prefs()
	{
		$r = safe_rows_start('name, val', 'txp_prefs', 'prefs_id=1');
		if ($r) {
			while ($a = nextRow($r)) {
				$out[$a['name']] = $a['val']; 
			}
			return $out;
		}
		return false;
	}

// -------------------------------------------------------------
	function db_down() 
	{
		// 503 status might discourage search engines from indexing or caching the error message
		header('Status: 503 Service Unavailable');
		return <<<eod
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Untitled</title>
</head>
<body>
<p align="center" style="margin-top:4em">Database unavailable.</p>
</body>
</html>
eod;
	}

?>
