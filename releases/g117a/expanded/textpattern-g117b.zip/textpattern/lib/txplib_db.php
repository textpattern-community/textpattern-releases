<?php

class DB {

    function DB() {
         global $txpcfg;
         $this->host = $txpcfg['host'];
         $this->db   = $txpcfg['db'];
         $this->user = $txpcfg['user'];
         $this->pass = $txpcfg['pass'];
         $this->link = mysql_connect($this->host, $this->user, $this->pass);
         if (!$this->link) die;
         mysql_select_db($this->db);
    }
} 

$DB = new DB;

//-------------------------------------------------------------
	function safe_query($q='',$debug='',$unbuf='')
	{
		global $DB;
		$method = (!$unbuf) ? 'mysql_query' : 'mysql_unbuffered_query';
		if (!$q) return false;
		if ($debug) echo '<pre style="text-align:left">'.$q.'</pre>'.n;
		$result = $method($q,$DB->link);
		
		if(!$result) return false;
		return $result;
	}

//-------------------------------------------------------------
	function fetch($col,$table,$key,$val,$debug='') 
	{
		$q = "select $col from $table where `$key` = '$val' limit 1";
		if ($r = safe_query($q,$debug)) {
			return (mysql_num_rows($r) > 0) ? mysql_result($r,0) : '';
		}
		return false;
	}

//-------------------------------------------------------------
	function getRow($query,$debug='') 
	{
		if ($r = safe_query($query,$debug)) {
			return (mysql_num_rows($r) > 0) ? mysql_fetch_array($r) : false;
		}
		return false;
	}

//-------------------------------------------------------------
	function getRows($query,$debug='') 
	{
		if ($r = safe_query($query,$debug)) {
			if (mysql_num_rows($r) > 0) {
				while ($a = mysql_fetch_array($r)) $out[] = $a; 
				return $out;
			}
		}
		return false;
	}

//-------------------------------------------------------------
	function getThing($query,$debug='') 
	{
		if ($r = safe_query($query,$debug)) {
			return (mysql_num_rows($r) != 0) ? mysql_result($r,0) : '';
		}
		return false;
	}

//-------------------------------------------------------------
	function getThings($query,$debug='') 
	// return values of one column from multiple rows in an num indexed array
	{
		$rs = getRows($query,$debug);
		if ($rs) {
			foreach($rs as $a) $out[] = $a[0];
			return $out;
		}
		return array();
	}
	
//-------------------------------------------------------------
	function getCount($table,$where,$debug='') 
	{
		return getThing("select count(*) from `$table` where $where",$debug);
	}

// -------------------------------------------------------------
 	function getTree($root, $type)
 	{ 
	    extract(getRow("select lft as l, rgt as r from txp_category 
					  where name='".doSlash($root)."' and type = '$type'"));

		$right = array(); 

	    $rs = getRows("select name, lft, rgt from txp_category 
					   where lft between $l and $r
					   and type = '$type' order by lft asc"); 

	    foreach ($rs as $row) {
	    	extract($row);
	        if (count($right) > 0) {
    	        while ($right[count($right)-1] < $rgt) { 
    	            array_pop($right);
    	        }
    	    }

        	$out[] = 
        		array(
        			'level' => count($right), 
        			'name' => $name,
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

		$result = safe_query("select name from txp_category where 
			parent='".doSlash($parent)."' and type='$type' order by name");
	
		while ($row = mysql_fetch_array($result)) { 
    	    $right = rebuild_tree($row['name'], $right, $type); 
	    } 

	    safe_query("update txp_category set lft=$left, rgt=$right 
	    	where name='".doSlash($parent)."' and type='$type'"); 

    	return $right+1; 
 	} 

//-------------------------------------------------------------
	function get_prefs()
	{
		$r = getRows('select name, val from txp_prefs where prefs_id=1');
		if ($r) {
			foreach ($r as $a) {
				$out[$a['name']] = $a['val']; 
			}
			return $out;
		}
		return false;
	}

